<?php

namespace APP\plugins\generic\citations\classes\form;

use APP\core\Application;
use APP\notification\NotificationManager;
use APP\template\TemplateManager;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use PKP\notification\PKPNotification;

class CitationsSettingsForm extends Form
{
    public $plugin;

    public $contextId;

    /**
     * Constructor.
     * @copydoc Form::__construct()
     */
    public function __construct($plugin, $contextId)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->plugin = $plugin;
        $this->contextId = $contextId;
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * @copydoc Form::initData()
     */
    public function initData(): void
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
            $this->setData('citationsScopusKey', $data['scopusKey']);
            $this->setData('citationsCrossrefUser', $data['crossrefUser']);
            $this->setData('citationsCrossrefPwd', $data['crossrefPwd']);
            $this->setData('citationsScopusSaved', ($data['scopusKey'] && $data['scopusKey'] != '') ? 'key-saved' : '');
            $this->setData(
                'citationsCrossrefUserSaved',
                ($data['crossrefUser'] && $data['crossrefUser'] != '') ? 'key-saved' : ''
            );
            $this->setData(
                'citationsCrossrefPwdSaved',
                ($data['crossrefPwd'] && $data['crossrefPwd'] != '') ? 'key-saved' : ''
            );
        }
        parent::initData();
    }

    /**
     * @copydoc Form::readInputData()
     */
    public function readInputData(): void
    {
        $this->readUserVars(
            [
                'citationsProvider',
                'citationsShowList',
                'citationsShowTotal',
                'citationsScopusKey',
                'citationsCrossrefUser',
                'citationsCrossrefPwd',
                'citationsMaxHeight',
                'citationsShowGoogle',
                'citationsShowPmc'
            ]);
        parent::readInputData();
    }

    /**
     * @copydoc Form::fetch()
     */
    public function fetch($request, $template = null, $display = false): ?string
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$args)
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $data = [
            "provider" => $this->getData('citationsProvider'),
            "showList" => $this->getData('citationsShowList'),
            "showGoogle" => $this->getData('citationsShowGoogle'),
            "showPmc" => $this->getData('citationsShowPmc'),
            "showTotal" => $this->getData('citationsShowTotal'),
            "scopusKey" => $this->getData('citationsScopusKey'),
            "crossrefUser" => $this->getData('citationsCrossrefUser'),
            "crossrefPwd" => $this->getData('citationsCrossrefPwd'),
            "maxHeight" => $this->getData('citationsMaxHeight')
        ];
        $this->plugin->updateSetting($contextId, 'settings', json_encode($data));
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            PKPNotification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );
        return parent::execute(...$args);
    }
}
