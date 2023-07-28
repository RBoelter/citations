<?php

namespace APP\plugins\generic\citations\classes\processor;

use APP\plugins\generic\citations\classes\client\CitationsHttpClient;
use SimpleXMLElement;

class CrossrefProcessor implements CitationsProcessorInterface
{
    private const CROSSREF_API_URL = 'https://doi.crossref.org/servlet/getForwardLinks?usr=%s&pwd=%s&doi=%s';

    private const CROSSREF_ACCEPT = "application/json";

    /** Process Crossref citations
     * @param string $doi The DOI to process
     * @param array $settings The plugin settings
     * @return array The citations and count
     */
    public function process(string $doi, array $settings): array
    {
        $cUser = $settings['crossrefUser'] ?? null;
        $cPwd = $settings['crossrefPwd'] ?? null;
        $showList = $settings['showList'] ?? false;
        $result = ["count" => 0, "citations" => []];
        if (empty($cUser) || empty($cPwd) || empty($doi)) {
            $result["error"] = "Credentials or DOI missing";
            return $result;
        }
        $data = CitationsHttpClient::get(
            sprintf(self::CROSSREF_API_URL, urlencode($cUser), urlencode($cPwd), urlencode($doi)),
            self::CROSSREF_ACCEPT
        );
        if ($data != null && strpos($data, "<crossref_result")) {
            $xml = simplexml_load_string($data);
            $elementList = $xml->{"query_result"}->{"body"}->{"forward_link"};
            if (!empty($elementList) && is_iterable($elementList)) {
                $result["count"] = sizeof($elementList);
                if ($showList) {
                    $result["citations"] = $this->extractCitationsFromXMLList($elementList);
                }

            }
        }
        return $result;
    }

    /** Extracts the citations from the XML
     * @param SimpleXMLElement $elementList The List of XML elements
     * @return array The citations
     */
    private function extractCitationsFromXMLList(SimpleXMLElement $elementList): array
    {
        $result = [];
        foreach ($elementList as $item) {
            if ($item->{"journal_cite"}) {
                $citeItem = $this->getArticleCite($item, "journal_cite");
                $result[] = $citeItem;
            } elseif ($item->{"book_cite"}) {
                $citeItem = $this->getArticleCite($item, "book_cite");
                $result[] = $citeItem;
            }
        }
        return $result;
    }

    /** Extracts the citation data from the XML
     * @param SimpleXMLElement $item The XML element
     * @param string $type The type of the citation
     * @return array The citation data
     */
    public function getArticleCite(SimpleXMLElement $item, string $type): array
    {
        return array_merge(
            $this->extractBookOrArticleTitle($item, $type),
            $this->extractDoi($item, $type),
            $this->extractAuthorList($item, $type),
            $this->extractRemainingCitationData($item, $type),
            ['type' => $type === "book_cite" ? "Book" : "Article"]
        );
    }


    /** Extracts the titles of the book or article from the XML
     * @param SimpleXMLElement $item The XML element
     * @param string $type The type of the citation
     * @return array The titles
     */
    private function extractBookOrArticleTitle(SimpleXMLElement $item, string $type): array
    {
        switch ($type) {
            case "book_cite":
                $result["title"] = '' . $item->{$type}->{"volume_title"};
                $result["journal"] = '' . $item->{$type}->{"series_title"};

                break;
            case "journal_cite":
                $result["title"] = '' . $item->{$type}->{"article_title"};
                $result["journal"] = '' . $item->{$type}->{"journal_title"};
                break;
            default:
                $result["title"] = "";
                $result["journal"] = "";
        }

        return $result;
    }

    /** Extracts the DOI from the XML
     * @param SimpleXMLElement $item The XML element
     * @param string $type The type of the citation
     * @return array The DOI
     */
    private function extractDoi(SimpleXMLElement $item, string $type): array
    {
        $result['doi'] = "";
        foreach ($item->{$type}->{"doi"} as $doi) {
            if ($doi['type'] == "journal_article" || $doi['type'] == "book_content") {
                $result['doi'] = '' . $doi;
            }
        }
        return $result;
    }

    /** Extracts the authors from the XML
     * @param SimpleXMLElement $item The XML element
     * @param string $type The type of the citation
     * @return array The authors
     */
    private function extractAuthorList(SimpleXMLElement $item, string $type): array
    {
        $contributors = $item->{$type}->{"contributors"}->{"contributor"};
        $count = 0;
        $result['authors'] = "";
        if (is_iterable($contributors)) {
            $size = sizeof($contributors);
            foreach ($contributors as $contributor) {
                $name = $contributor->{"given_name"} . " " . $contributor->{"surname"};
                if (++$count < $size) {
                    $name .= ", ";
                }
                if ($contributor['first-author'] == "true") {
                    $result['authors'] = $name . $result['authors'];
                } else {
                    $result['authors'] .= $name;
                }
            }
        }
        return $result;
    }

    /** Extracts the remaining citation data from the XML
     * @param SimpleXMLElement $item The XML element
     * @param string $type The type of the citation
     * @return string[] The remaining citation data
     */
    private function extractRemainingCitationData(SimpleXMLElement $item, string $type): array
    {
        return
            [
                "year" => '' . $item->{$type}->{"year"},
                "volume" => '' . $item->{$type}->{"volume"},
                "issue" => '' . $item->{$type}->{"issue"},
                "pages" => '' . $item->{$type}->{"first_page"},
                "source" => 'crossref'
            ];
    }
}
