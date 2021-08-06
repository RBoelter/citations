<?php


import('classes.handler.Handler');

class CitationsHandler extends Handler
{
	public function get($args, $request)
	{
		$plugin = PluginRegistry::getPlugin('generic', 'citationsplugin');
		$pubId = null;
		if ($request->getUserVars() && sizeof($request->getUserVars()) > 0 && $request->getUserVars()['citationsId']) {
			$pubId = $request->getUserVars()['citationsId'];
			$provider = $request->getUserVars()['citationsProvider'];
			$loadList = intval($request->getUserVars()['citationsShowList']);
		} else {
			return new JSONMessage(false);
		}
		$ret = array();
		if ($pubId != null && '' != trim($pubId)) {
			import('plugins.generic.citations.classes.CitationsParser');
			$parser = new CitationsParser();
			$contextId = $request->getContext()->getId();
			$settings = json_decode($plugin->getSetting($contextId, 'settings'), true);
			switch ($provider) {
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
			}
		}
		if (sizeof($ret) > 0) {
			return new JSONMessage(true, $ret);
		} else {
			return new JSONMessage(false);
		}
	}
}

