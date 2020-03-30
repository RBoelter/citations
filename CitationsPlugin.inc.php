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
			$request = Application::getRequest();
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->addStyleSheet(
				'citations',
				$request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/citations.css'
			);
			HookRegistry::register('Templates::Article::Details', array($this, 'citationsContent'));
			HookRegistry::register('LoadHandler', array($this, 'setPageHandler'));
		}
		return $success;
	}


	public function citationsContent($hookName, $args)
	{
		$request = Application::getRequest();
		$smarty =& $args[1];
		$output =& $args[2];
		$article = $smarty->getTemplateVars('article');
		$pubId = $article->getStoredPubId('doi');


		$citationsShowList = true;
		$citationsProvider = 'all';
		$citationsShowTotal = true;

		if ($pubId == null)
			$pubId = '10.5964/ejop.v8i4.555';
		if ($pubId != null && $pubId != '') {
			$smarty->assign(array(
				'citationsImagePath' => $request->getBaseUrl() . '/' . $this->getPluginPath() . '/images/',
				'citationsId' => $pubId,
				'citationsProvider' => $citationsProvider,
				'citationsShowTotal' => $citationsShowTotal,
				'citationsShowList' => $citationsShowList,
				'citationsArgsList' => array('citationsId' => $pubId, 'citationsShowList' => $citationsShowList, 'citationsProvider' => $citationsProvider)
			));
			$smarty->addJavaScript(
				'citations',
				$request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/citations.js'
			);
			$output .= $smarty->fetch($this->getTemplateResource('citations.tpl'));
		}
	}


	public function setPageHandler($hookName, $params)
	{
		$page = $params[0];
		if ($this->getEnabled() && $page === 'citations') {
			$this->import('classes/CitationsHandler');
			define('HANDLER_CLASS', 'CitationsHandler');
			return true;
		}
		return false;
	}

}
