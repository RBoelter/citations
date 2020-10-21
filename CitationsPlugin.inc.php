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
			$request = Application::get()->getRequest();
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
		$request = Application::get()->getRequest();
		$smarty =& $args[1];
		$output =& $args[2];
		$article = $smarty->getTemplateVars('article');
		$pubId = $article->getStoredPubId('doi');
		$contextId = $request->getContext()->getId();
		$settings = json_decode($this->getSetting($contextId, 'settings'), true);
		if ($pubId != null && $pubId != '' && $settings) {
			$smarty->assign(array(
				'citationsImagePath' => $request->getBaseUrl() . '/' . $this->getPluginPath() . '/images/',
				'citationsId' => $pubId,
				'citationsProvider' => $settings['provider'] != null ? $settings['provider'] : 'all',
				'citationsShowGoogle' => $settings['showGoogle'] ? $settings['showGoogle'] : 0,
				'citationsShowPmc' => $settings['showPmc'] ? $settings['showPmc'] : 0,
				'citationsShowTotal' => $settings['showTotal'] != null ? $settings['showTotal'] : false,
				'citationsShowList' => $settings['showList'] != null ? $settings['showList'] : false,
				'citationsMaxHeight' => $settings['maxHeight'] != null ? intval($settings['maxHeight']) : 0,
				'citationsArgsList' => array(
					'citationsId' => $pubId,
					'citationsShowList' => $settings['showList'] != null ? $settings['showList'] : false,
					'citationsProvider' => $settings['provider'] != null ? $settings['provider'] : 'all'
				)
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

	/**
	 * Add settings button to plugin
	 * @param $request
	 * @param array $verb
	 * @return array
	 */
	public function getActions($request, $verb)
	{
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled() ? array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			) : array(),
			parent::getActions($request, $verb)
		);
	}

	/**
	 * Manage Settings
	 * @param array $args
	 * @param PKPRequest $request
	 * @return JSONMessage
	 */
	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->assign('citationsProviderOptions', [
					'all' => 'plugins.generic.citations.options.all',
					'scopus' => 'plugins.generic.citations.options.scopus',
					'crossref' => 'plugins.generic.citations.options.crossref'
				]);
				$this->import('CitationsSettingsForm');
				$form = new CitationsSettingsForm($this);
				if (!$request->getUserVar('save')) {
					$form->initData();
					return new JSONMessage(true, $form->fetch($request));
				}
				$form->readInputData();
				if ($form->validate()) {
					$form->execute();
					return new JSONMessage(true);
				}
		}
		return parent::manage($args, $request);
	}

}
