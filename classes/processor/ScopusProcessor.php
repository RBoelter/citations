<?php

namespace APP\plugins\generic\citations\classes\processor;

use APP\plugins\generic\citations\classes\client\CitationsHttpClient;

class ScopusProcessor implements CitationsProcessorInterface
{
    private const SCOPUS_API_URL = 'https://api.elsevier.com/content/search/scopus';
    private const SCOPUS_FIELDS = '&field=eid,citedby-count';
    private const SCOPUS_ACCEPT = "application/json";

    public function process(string $doi, array $settings): array
    {
        $apiKey = $settings['scopusKey'] ?? null;
        $showList = $settings['showList'] ?? false;
        $result = ["count" => 0, "citations" => []];
        if (empty($doi) || empty($apiKey)) {
            $result["error"] = "Credentials or DOI missing";
            return $result;
        }
        $url = self::SCOPUS_API_URL . "?query=DOI" . urlencode('("' . $doi . '")') . "&apiKey=" . $apiKey . self::SCOPUS_FIELDS;
        $data = json_decode(CitationsHttpClient::get($url, self::SCOPUS_ACCEPT), true);
        $count = !empty($data['search-results']['entry'][0]['citedby-count']) && is_numeric($data['search-results']['entry'][0]['citedby-count'])
            ? intval($data['search-results']['entry'][0]['citedby-count'])
            : 0;
        $eid = !empty($data['search-results']['entry'][0]['eid'])
            ? $data['search-results']['entry'][0]['eid']
            : null;
        $result['count'] = $count;
        if ($showList && 0 !== $count && null !== $eid) {
            $citationsUrl = self::SCOPUS_API_URL . "?query=REF" . urlencode('("' . $eid . '")') . "&apiKey=" . $apiKey;
            $citations = json_decode(CitationsHttpClient::get($citationsUrl, self::SCOPUS_ACCEPT), true);
            if (!empty($citations['search-results']['entry']) && is_array($citations['search-results']['entry'])) {
                foreach ($citations['search-results']['entry'] as $citation) {
                    $result['citations'][] = $this->mapScopusCitationResult($citation);
                }
            }
        }

        return $result;
    }

    /**
     * @param mixed $citation
     * @return array
     */
    private function mapScopusCitationResult(array $citation): array
    {
        return [
            'title' => $citation['dc:title'] ?? '',
            'doi' => $citation['prism:doi'] ?? '',
            'authors' => $citation['dc:creator'] ?? '',
            'year' => $citation['prism:coverDate'] ?? '',
            'journal' => $citation['prism:publicationName'] ?? '',
            'volume' => $citation['prism:volume'] ?? '',
            'issue' => $citation['prism:issueIdentifier'] ?? '',
            'pages' => $citation['prism:pageRange'] ?? '',
            'type' => $citation['subtypeDescription'] ?? '',
            'source' => 'Scopus' ?? '',
        ];
    }


}
