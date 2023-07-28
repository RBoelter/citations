<?php

namespace APP\plugins\generic\citations\classes\processor;

use APP\plugins\generic\citations\classes\client\CitationsHttpClient;

class EuropePmcProcessor implements CitationsProcessorInterface
{

    private const PMC_API_URL = 'https://www.ebi.ac.uk/europepmc/webservices/rest/search?query=%s';

    public function process(string $doi, array $settings): array
    {
        $ret = array();
        $data = CitationsHttpClient::get(sprintf(self::PMC_API_URL, $doi), "application/xml");
        $ret["pmc_count"] = 0;
        if ($data != null) {
            $xml = simplexml_load_string($data);
            $ret["count"] = intval($xml->{"hitCount"});
        }

        return $ret;
    }
}
