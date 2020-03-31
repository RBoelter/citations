<?php

import('lib.pkp.classes.form.Form');


class CitationsSettingsForm extends Form
{
	public $plugin;
	private $sKey;

	public function __construct($plugin)
	{
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
		$this->plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	public function initData()
	{
		$contextId = Application::getRequest()->getContext()->getId();
		$data = $this->plugin->getSetting($contextId, 'settings');
		if ($data != null && $data != '') {
			$data = json_decode($data, true);
			$this->setData('citationsProvider', $data['provider']);
			$this->setData('citationsShowList', $data['showList']);
			$this->setData('citationsShowTotal', $data['showTotal']);
			$this->setData('citationsScopusKeyS', $data['scopusKey']);
			$this->setData('citationsCrossrefUserS', $data['crossrefUser']);
			$this->setData('citationsCrossrefPwdS', $data['crossrefPwd']);
			$this->setData('citationsMaxHeight', $data['maxHeight']);
			$this->sKey = $data['scopusKey'];
		}
		parent::initData();
	}

	public function readInputData()
	{
		$this->readUserVars(['citationsProvider', 'citationsShowList', 'citationsShowTotal', 'citationsScopusKey', 'citationsCrossrefUser', 'citationsCrossrefPwd', 'citationsScopusKeyS', 'citationsCrossrefUserS', 'citationsCrossrefPwdS', 'citationsMaxHeight']);
		parent::readInputData();
	}

	public function fetch($request, $template = null, $display = false)
	{
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->plugin->getName());
		return parent::fetch($request, $template, $display);
	}

	public function execute()
	{
		$contextId = Application::getRequest()->getContext()->getId();
		$data = [
			"provider" => $this->getData('citationsProvider'),
			"showList" => $this->getData('citationsShowList'),
			"showTotal" => $this->getData('citationsShowTotal'),
			"scopusKey" => ($this->getData('citationsScopusKey') && $this->getData('citationsScopusKey') != '' ? $this->getData('citationsScopusKey') : $this->getData('citationsScopusKeyS')),
			"crossrefUser" => ($this->getData('citationsCrossrefUser') && $this->getData('citationsCrossrefUser') != '' ? $this->getData('citationsCrossrefUser') : $this->getData('citationsCrossrefUserS')),
			"crossrefPwd" => ($this->getData('citationsCrossrefPwd') && $this->getData('citationsCrossrefPwd') != '' ? $this->getData('citationsCrossrefPwd') : $this->getData('citationsCrossrefPwdS')),
			"maxHeight" => $this->getData('citationsMaxHeight')
		];
		$this->plugin->updateSetting($contextId, 'settings', json_encode($data));
		import('classes.notification.NotificationManager');
		$notificationMgr = new NotificationManager();
		$notificationMgr->createTrivialNotification(
			Application::getRequest()->getUser()->getId(),
			NOTIFICATION_TYPE_SUCCESS,
			['contents' => __('common.changesSaved')]
		);
		return parent::execute();
	}
}