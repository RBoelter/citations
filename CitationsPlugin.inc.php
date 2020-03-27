<?php
import('lib.pkp.classes.plugins.GenericPlugin');


class CitationsPlugin extends GenericPlugin
{
	/**
	 * @return string plugin name
	 */
	public function getDisplayName()
	{
		return __('plugins.generic.citations.title');
	}

	/**
	 * @return string plugin description
	 */
	public function getDescription()
	{
		return __('plugins.generic.citations.desc');
	}


	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			/*$request = Application::getRequest();
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->addStyleSheet(
				'mostViewedArticles',
				$request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/mostViewed.css'
			);*/
			HookRegistry::register('Templates::Article::Details', array($this, 'citationsContent'));
		}
		return $success;
	}


	public function citationsContent($hookName, $args)
	{
		$request = Application::getRequest();
		$smarty =& $args[1];
		$output =& $args[2];
		$article = $smarty->getTemplateVars('article');
		$pubId = null;
		$ret = array();

		$provider = 'all';
		$showList = '';
		$maxHeight = 500;


		if ($article)
			$pubId = $article->getStoredPubId('doi');
		/* TESTCASE*/
		if ($pubId == null)
			$pubId = '10.5964/ejop.v8i4.555';
		/* #############  */
		if ($pubId != null && '' != trim($pubId)) {
			import('plugins.generic.citations.classes.CitationsParser');
			$parser = new CitationsParser();
			$settings = json_decode(file_get_contents("../../settings.json"));
			switch ($provider) {
				case 'scopus':
					$ret = array_merge($ret, $parser->getScopusCitedBy($pubId, $settings->sca));
					break;
				case 'crossref':
					$ret = array_merge($ret, $parser->getCrossrefCitedBy($pubId, $settings->cru, $settings->crp));
					break;
				case 'all':
					$list = array();
					$ret = array_merge($ret, $parser->getScopusCitedBy($pubId, $settings->sca));
					$ret = array_merge($ret, $parser->getCrossrefCitedBy($pubId, $settings->cru, $settings->crp));
					$list = array_merge($list, $ret['crossref_list']);
					foreach ($ret['scopus_list'] as $scopus) {
						$inList = false;
						foreach ($list as $itm) {
							if (trim($itm['doi']) == trim($scopus['doi'])) {
								$inList = true;
								break;
							}
						}
						if (!$inList)
							array_push($list, $scopus);
					}
					$ret['all_list'] = $list;
					break;
			}
			$smarty->assign('citationsProvider', $provider);
			$smarty->assign('citationsImagePath', $request->getBaseUrl() . '/' . $this->getPluginPath() . '/images/');
			$smarty->assign('citationsId', $pubId);
			$smarty->assign('citationsList', $ret);
			$output .= $smarty->fetch($this->getTemplateResource('citations.tpl'));
		}
	}

}
