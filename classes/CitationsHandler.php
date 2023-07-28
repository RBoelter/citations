<?php

namespace APP\plugins\generic\citations\classes;

use APP\handler\Handler;

use APP\plugins\generic\citations\classes\processor\CrossrefProcessor;
use APP\plugins\generic\citations\classes\processor\EuropePmcProcessor;
use APP\plugins\generic\citations\classes\processor\ScopusProcessor;
use PKP\core\JSONMessage;
use PKP\core\PKPRequest;
use PKP\plugins\PluginRegistry;

class CitationsHandler extends Handler
{

    /** This function can be called via <HOST>/index.php/<journal>/citations/get?doi=<doi> and returns the citations as JSON
     * @param array $args The request arguments
     * @param PKPRequest $request The request
     * @return JSONMessage The JSON response
     */
    public function get(array $args, PKPRequest $request): JSONMessage
    {
        $doi = $request->getUserVars()['doi'] ?? null;
        $settings = $this->loadSettings($request);

        if (empty($doi) || empty($settings)) {
            return new JSONMessage(false, empty($settings) ? 'Missing settings' : 'Missing DOI');
        }
        if ('all' === $settings['provider'] || 'crossref' === $settings['provider']) {
            $crossrefProcessor = new CrossrefProcessor();
            $result['crossref'] = $crossrefProcessor->process($doi, $settings);
        }
        if ('all' === $settings['provider'] || 'scopus' === $settings['provider']) {
            $scopusProcessor = new ScopusProcessor();
            $result['scopus'] = $scopusProcessor->process($doi, $settings);
        }
        if (!empty($settings['showPmc'])) {
            $europePmcProcessor = new EuropePmcProcessor();
            $result['europepmc'] = $europePmcProcessor->process($doi, $settings);
        }
        if (!empty($result['crossref']['citations']) && !empty($result['scopus']['citations'])) {
            $result['scopus']['citations'] = $this->removeDoubletsFromScopus($result['crossref']['citations'], $result['scopus']['citations']);
        }

        return new JSONMessage(!empty($result), !empty($result) ? $result : null);
    }

    /** Loads the plugin settings
     * @param PKPRequest $request The request
     * @return array The settings
     */
    private function loadSettings(PKPRequest $request): array
    {
        $plugin = PluginRegistry::getPlugin('generic', 'citationsplugin');
        $contextId = $request->getContext()->getId();
        if (null !== $contextId) {
            return json_decode($plugin->getSetting($contextId, 'settings') ?? [], true);
        } else {
            return json_decode('', true);
        }
    }

    /** checks if the doi of a scopus citation is already in the crossref citations and removes it if so
     * @param array $crossrefCitations The Crossref citations
     * @param array $scopusCitations The Scopus citations
     * @return array The Scopus citations without doublets
     */
    private function removeDoubletsFromScopus(array $crossrefCitations, array $scopusCitations): array
    {
        $result = [];
        foreach ($scopusCitations as $scopusCitation) {
            $found = false;
            foreach ($crossrefCitations as $crossrefCitation) {
                if (!empty($scopusCitation['doi'])
                    && !empty($crossrefCitation['doi'])
                    && strtolower(trim($scopusCitation['doi'])) === strtolower(trim($crossrefCitation['doi']))) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $result[] = $scopusCitation;
            }
        }
        return $result;
    }


}

