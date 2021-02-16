<?php

use GuzzleHttp\Exception\GuzzleException;

class CitationsParser
{
	function getScopusCitedBy($doi, $apiKey, $loadList)
	{
		if ($apiKey == null || $apiKey == '' || $doi == null || $doi == '') {
			return array();
		}
		$url = "https://api.elsevier.com/content/search/scopus?query=DOI(".$doi.")&field=eid,citedby-count&apiKey=".$apiKey;
		$data = $this->getAPIContent($url);
		$ret = array();
		$ret["scopus_count"] = 0;
		$ret["scopus_url"] = null;
		$ret["scopus_list"] = [];
		$scopus_list = array();
		if ($data != null) {
			$xml = simplexml_load_string($data);
			if ($xml) {
				$ns = $xml->getNamespaces(true);
				if (!$xml->{'entry'}->{'error'} && $xml->children($ns['opensearch'])->{"totalResults"} != "0" && $xml->{'entry'}->{'citedby-count'}) {
					$ret["scopus_count"] = intval($xml->{'entry'}->{'citedby-count'});
					if ($ret["scopus_count"] != 0 && $loadList == 1) {
						$url = "https://api.elsevier.com/content/search/scopus?query=REF(".$xml->{'entry'}->{'eid'}.")&apiKey=".$apiKey;
						$xml = simplexml_load_string($data = $this->getAPIContent($url));
						$ns = $xml->getNamespaces(true);
						if (!$xml->{'entry'}->{'error'} && $xml->children($ns['opensearch'])->{"totalResults"} != "0") {
							foreach ($xml->{'entry'} as $entry) {
								$citeItem = array(
									"journal_title" => ''.$entry->children($ns['prism'])->{'publicationName'},
									"article_title" => ''.$entry->children($ns['dc'])->{'title'},
									"year" => ''.substr($entry->children($ns['prism'])->{'coverDate'}, 0, 4),
									"volume" => ''.$entry->children($ns['prism'])->{'volume'},
									"issue" => ''.$entry->children($ns['prism'])->{'issueIdentifier'},
									"first_page" => ''.$entry->children($ns['prism'])->{"pageRange"},
									"doi" => ''.$entry->children($ns['prism'])->{"doi"},
									"authors" => ''.$entry->children($ns['dc'])->{"creator"},
									"type" => 'scopus',
								);
								array_push($scopus_list, $citeItem);
							}
						}
					}
				}
			}
		}
		$ret["scopus_list"] = $scopus_list;

		return $ret;
	}


	function getCrossrefCitedBy($doi, $api_user, $api_pwd, $loadList)
	{
		if ($api_user == null || $api_user == '' || $api_pwd == null || $api_pwd == '' || $doi == null || $doi == '') {
			return array();
		}
		$url = "https://doi.crossref.org/servlet/getForwardLinks?usr=".$api_user."&pwd=".$api_pwd."&doi=".$doi;
		$data = $this->getAPIContent($url);
		$ret = array();
		$ret["crossref_count"] = 0;
		$ret["crossref_list"] = null;
		$ret["crossref_list"] = [];
		$crossref_list = array();
		if ($data != null && strpos($data, "<crossref_result") == true) {
			$xml = simplexml_load_string($data);
			$link_list = $xml->{"query_result"}->{"body"}->{"forward_link"};
			if ($link_list && sizeof($link_list) > 0) {
				$ret["crossref_count"] = sizeof($link_list);
				if ($loadList == 1) {
					foreach ($link_list as $item) {
						if ($item->{"journal_cite"}) {
							$citeItem = $this->getArticleCite($doi, $item, "journal_cite");
							array_push($crossref_list, $citeItem);
						} else {
							if ($item->{"book_cite"}) {
								$citeItem = $this->getArticleCite($doi, $item, "book_cite");
								array_push($crossref_list, $citeItem);
							}
						}
					}
				}
			}
		}
		$ret["crossref_list"] = $crossref_list;

		return $ret;
	}

	function getEuropePmcCount($doi)
	{
		$ret = array();
		$url = "https://www.ebi.ac.uk/europepmc/webservices/rest/search?query=(REF:".$doi.")";
		$data = $this->getAPIContent($url, 'application/xml');
		$ret["pmc_count"] = 0;
		if ($data != null) {
			$xml = simplexml_load_string($data);
			$ret["pmc_count"] = intval($xml->{"hitCount"});
		}

		return $ret;
	}

	private function getAPIContent($url, $type = "text/xml")
	{
		$ch = curl_init();
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"accept: ".$type,
					"cache-control: no-cache",
					"content-type: ".$type,
				),
			)
		);
		if (curl_error($ch)) {
			return null;
		}
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data) {
			return $data;
		} else {
			return null;
		}
	}

	/**
	 * @param $doi
	 * @param SimpleXMLElement $item
	 * @return array
	 */
	public function getArticleCite($doi, $item, $type)
	{
		switch ($type) {
			case "book_cite":
				$t_1 = ''.$item->{$type}->{"series_title"};
				$t_2 = ''.$item->{$type}->{"volume_title"};
				break;
			case "journal_cite":
				$t_1 = ''.$item->{$type}->{"journal_title"};
				$t_2 = ''.$item->{$type}->{"article_title"};
				break;
			default:
				$t_1 = "";
				$t_2 = "";
		}

		$citeItem = array(
			"journal_title" => $t_1,
			"article_title" => $t_2,
			"year" => ''.$item->{$type}->{"year"},
			"volume" => ''.$item->{$type}->{"volume"},
			"issue" => ''.$item->{$type}->{"issue"},
			"first_page" => ''.$item->{$type}->{"first_page"},
			"type" => 'crossref',
		);
		foreach ($item->{$type}->{"doi"} as $doi) {
			if ($doi['type'] == "journal_article" || $doi['type'] == "book_content") {
				$citeItem["doi"] = ''.$doi;
			}
		}
		$author_list = "";
		$contri_list = $item->{$type}->{"contributors"}->{"contributor"};
		$count = 0;
		if (is_countable($contri_list)) {
			$size = sizeof($contri_list);
			foreach ($contri_list as $contributor) {
				$name = $contributor->{"given_name"}." ".$contributor->{"surname"};
				if (++$count < $size) {
					$name .= ", ";
				}
				if ($contributor['first-author'] == "true") {
					$author_list = $name.$author_list;
				} else {
					$author_list .= $name;
				}
			}
		}
		$citeItem["authors"] = $author_list;

		return $citeItem;
	}
}
