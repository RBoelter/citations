<?php

namespace APP\plugins\generic\citations\classes\processor;

use APP\plugins\generic\citations\classes\client\CitationsHttpClient;

class EuropePmcProcessor implements CitationsProcessorInterface
{
    /**
     * The query has to be scoped to the REF field (the reference lists of
     * other articles) and the DOI has to be quoted. An unscoped, unquoted
     * DOI is parsed as free text, which matches the article's own record
     * and misses nearly every citing paper - for 10.1371/journal.pmed.0020124
     * that is 17 hits instead of 2490, one of them the article itself.
     *
     * Note this counts only references that carry the DOI, so it stays below
     * Europe PMC's own citedByCount (2490 vs 5054 for the DOI above). That
     * number exists only for articles Europe PMC indexes itself, which many
     * journals are not, so REF is what works for every article - and it is
     * the same query the linked result list in citations.tpl uses, so the
     * badge and the linked page agree.
     *
     * pageSize=1 because only hitCount is read; format=xml so the response
     * shape does not depend on the Accept header being honoured.
     */
    private const PMC_API_URL = 'https://www.ebi.ac.uk/europepmc/webservices/rest/search?query=%s&format=xml&pageSize=1';

    public function process(string $doi, array $settings): array
    {
        if (empty($doi)) {
            return ["error" => "DOI missing"];
        }

        $url = sprintf(self::PMC_API_URL, rawurlencode(sprintf('REF:"%s"', $doi)));
        $data = CitationsHttpClient::get($url, "application/xml");
        if ($data === '') {
            return ["error" => "Europe PMC request failed"];
        }

        $xml = simplexml_load_string($data);
        if ($xml === false) {
            return ["error" => "Europe PMC returned an unreadable response"];
        }

        // Deliberately no "count" key on the failure paths above: the badge
        // stays hidden instead of claiming zero citations for a request that
        // never succeeded.
        return ["count" => intval($xml->{"hitCount"})];
    }
}
