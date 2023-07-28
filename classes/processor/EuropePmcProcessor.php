<?php

namespace APP\plugins\generic\citations\classes\processor;

use APP\plugins\generic\citations\classes\client\CitationsHttpClient;

class EuropePmcProcessor implements CitationsProcessorInterface
{

    public function process(string $doi, array $settings): array
    {
        $ret = array();
        $url = "https://www.ebi.ac.uk/europepmc/webservices/rest/search?query=(REF:\"" . $doi . "\")";
        $data = CitationsHttpClient::get($url, "application/xml");
        $ret["pmc_count"] = 0;
        if ($data != null) {
            $xml = simplexml_load_string($data);
            $ret["count"] = intval($xml->{"hitCount"});
        }

        return $ret;
    }
}
