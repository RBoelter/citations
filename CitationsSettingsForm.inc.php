<?php

import('lib.pkp.classes.form.Form');


class CitationsSettingsForm extends Form
{
	public $plugin;

	public function __construct($plugin)
	{
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
		$this->plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	public function initData()
	{
		$contextId = Application::get()->getRequest()->getContext()->getId();
		$data = $this->plugin->getSetting($contextId, 'settings');
		if ($data != null && $data != '') {
			$data = json_decode($data, true);
			$this->setData('citationsProvider', $data['provider']);
			$this->setData('citationsShowList', $data['showList']);
			$this->setData('citationsShowGoogle', $data['showGoogle']);
			$this->setData('citationsShowPmc', $data['showPmc']);
			$this->setData('citationsShowTotal', $data['showTotal']);
			$this->setData('citationsMaxHeight', $data['maxHeight']);
			$this->setData('citationsScopusSaved', ($data['scopusKey'] && $data['scopusKey'] != '') ? 'key-saved' : '');
			$this->setData('citationsCrossrefUserSaved', ($data['crossrefUser'] && $data['crossrefUser'] != '') ? 'key-saved' : '');
			$this->setData('citationsCrossrefPwdSaved', ($data['crossrefPwd'] && $data['crossrefPwd'] != '') ? 'key-saved' : '');
		}
		parent::initData();
	}

	public function readInputData()
	{
		$this->readUserVars(['citationsProvider', 'citationsShowList', 'citationsShowTotal', 'citationsScopusKey', 'citationsCrossrefUser', 'citationsCrossrefPwd', 'citationsMaxHeight', 'citationsShowGoogle', 'citationsShowPmc']);
		parent::readInputData();
	}

	public function fetch($request, $template = null, $display = false)
	{
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->plugin->getName());
		return parent::fetch($request, $template, $display);
	}

	public function execute(...$args)
	{
		$contextId = Application::get()->getRequest()->getContext()->getId();
		$settings = json_decode($this->plugin->getSetting($contextId, 'settings'), true);
		$data = [
			"provider" => $this->getData('citationsProvider'),
			"showList" => $this->getData('citationsShowList'),
			"showGoogle" => $this->getData('citationsShowGoogle'),
			"showPmc" => $this->getData('citationsShowPmc'),
			"showTotal" => $this->getData('citationsShowTotal'),
			"scopusKey" => ($this->getData('citationsScopusKey') && $this->getData('citationsScopusKey') != '' ? $this->getData('citationsScopusKey') : $settings['scopusKey']),
			"crossrefUser" => ($this->getData('citationsCrossrefUser') && $this->getData('citationsCrossrefUser') != '' ? $this->getData('citationsCrossrefUser') : $settings['crossrefUser']),
			"crossrefPwd" => ($this->getData('citationsCrossrefPwd') && $this->getData('citationsCrossrefPwd') != '' ? $this->getData('citationsCrossrefPwd') : $settings['crossrefPwd']),
			"maxHeight" => $this->getData('citationsMaxHeight')
		];
		$this->plugin->updateSetting($contextId, 'settings', json_encode($data));
		import('classes.notification.NotificationManager');
		$notificationMgr = new NotificationManager();
		$notificationMgr->createTrivialNotification(
			Application::get()->getRequest()->getUser()->getId(),
			NOTIFICATION_TYPE_SUCCESS,
			['contents' => __('common.changesSaved')]
		);
		return parent::execute();
	}
}
