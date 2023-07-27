<?php

namespace APP\plugins\generic\citations\classes\form;

use APP\core\Application;
use APP\notification\NotificationManager;
use APP\plugins\generic\citations\CitationsPlugin;
use APP\template\TemplateManager;
use Exception;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use PKP\notification\PKPNotification;

class CitationsSettingsForm extends Form
{
    public CitationsPlugin $plugin;

    public int $contextId;

    private const PLUGIN_VARS = [
        'provider',
        'showList',
        'showTotal',
        'scopusKey',
        'crossrefUser',
        'crossrefPwd',
        'maxHeight',
        'showGoogle',
        'showPmc'
    ];

    /**
     * Constructor.
     * @copydoc Form::__construct()
     */
    public function __construct(CitationsPlugin $plugin, int $contextId)
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
            if (is_iterable($data)) {
                foreach (self::PLUGIN_VARS as $var) {
                    if (key_exists($var, $data)) {
                        $this->setData($var, $data[$var]);
                    }
                }
            }
        }
        parent::initData();
    }

    /**
     * @copydoc Form::readInputData()
     */
    public function readInputData(): void
    {
        $this->readUserVars(self::PLUGIN_VARS);
        parent::readInputData();
    }

    /**
     * @copydoc Form::fetch()
     * @throws Exception
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
        $data = [];
        foreach (self::PLUGIN_VARS as $var) {
            $data[$var] = $this->getData($var);
        }
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
