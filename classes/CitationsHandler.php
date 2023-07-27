<?php

namespace APP\plugins\generic\citations\classes;

use APP\handler\Handler;
use PKP\core\JSONMessage;
use PKP\core\PKPRequest;
use PKP\plugins\PluginRegistry;

class CitationsHandler extends Handler
{
    public function get($args, $request): JSONMessage
    {

        $doi = $request->getUserVars()['doi'] ?? null;
        $settings = $this->loadSettings($request);

        if (empty($doi) || empty($settings)) {
            return new JSONMessage(false, empty($settings) ? 'Missing settings' : 'Missing DOI');
        }

        $parser = new CitationsParser();

        $parser->getScopusCitedBy($doi, $settings);

        $ret = array();


        /*switch ($provider) {
            case 'scopus':
                $ret = array_merge($ret, $parser->getScopusCitedBy($pubId, $settings['scopusKey'], $loadList));
                break;
            case 'crossref':
                $ret = array_merge($ret, $parser->getCrossrefCitedBy($pubId, $settings['crossrefUser'], $settings['crossrefPwd'], $loadList));
                break;
            case 'all':
                $list = array();
                $ret = array_merge($ret, $parser->getScopusCitedBy($pubId, $settings['scopusKey'], $loadList));
                $ret = array_merge($ret, $parser->getCrossrefCitedBy($pubId, $settings['crossrefUser'], $settings['crossrefPwd'], $loadList));
                if (key_exists('crossref_list', $ret)) {
                    $list = array_merge($list, $ret['crossref_list']);
                }
                if (key_exists('scopus_list', $ret)) {
                    foreach ($ret['scopus_list'] as $scopus) {
                        $inList = false;
                        foreach ($list as $itm) {
                            if (trim($itm['doi']) == trim($scopus['doi'])) {
                                $inList = true;
                                break;
                            }
                        }
                        if (!$inList) {
                            array_push($list, $scopus);
                        }
                    }
                }
                $ret['all_list'] = $list;
                $ret['scopus_list'] = null;
                $ret['crossref_list'] = null;
                break;
        }
        if ($settings['showPmc'] && intval($settings['showPmc']) == 1) {
            $ret = array_merge($ret, $parser->getEuropePmcCount($pubId));
        }*/

        if (sizeof($ret) > 0) {
            return new JSONMessage(true, $ret);
        } else {
            return new JSONMessage(false);
        }
    }

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

    private function setResult()
    {

    }


}

