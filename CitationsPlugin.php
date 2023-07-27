<?php

/**
 * @file plugins/generic/citations/CitationsPlugin.inc.php
 *
 * @class CitationsPlugin
 *
 * @ingroup plugins_generic_citations
 *
 * @brief Citations plugin class
 */

namespace APP\plugins\generic\citations;

use APP\core\Application;
use APP\plugins\generic\citations\classes\form\CitationsSettingsForm;

use APP\template\TemplateManager;
use Exception;
use PKP\core\JSONMessage;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;


class CitationsPlugin extends GenericPlugin
{
    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     * @throws Exception
     */
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path, $mainContextId);
        if (Application::isUnderMaintenance()) {
            return true;
        }
        if ($success && $this->getEnabled($mainContextId)) {
            $request = Application::get()->getRequest();
            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->addStyleSheet(
                'citations', $request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/citations.css'
            );
            Hook::add('Templates::Article::Details', array($this, 'citationsContent'));
            Hook::add('Templates::Preprint::Details', array($this, 'citationsContent'));
            Hook::add('LoadHandler', array($this, 'setPageHandler'));
        }
        return $success;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName(): string
    {
        return __('plugins.generic.citations.title');
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDescription(): string
    {
        return __('plugins.generic.citations.desc');
    }


    public function citationsContent($hookName, $args): void
    {
        $request = Application::get()->getRequest();
        $smarty =& $args[1];
        $pubId = $this->getPubId($smarty);
        $contextId = $request->getContext()->getId();
        $settings = json_decode($this->getSetting($contextId, 'settings'), true);
        if ($pubId != null && $pubId != '' && $settings) {
            $smarty->assign(array(
                'citationsImagePath' => $request->getBaseUrl() . '/' . $this->getPluginPath() . '/images/',
                'citationsId' => $pubId,
                'citationsProvider' => $settings['provider'] ?: 'all',
                'citationsShowGoogle' => $settings['showGoogle'] ?: 0,
                'citationsShowPmc' => $settings['showPmc'] ?: 0,
                'citationsShowTotal' => $settings['showTotal'] ?: false,
                'citationsShowList' => $settings['showList'] ?: false,
                'citationsMaxHeight' => $settings['maxHeight'] ?: 0,
                'citationsArgsList' => array(
                    'citationsId' => $pubId,
                    'citationsShowList' => $settings['showList'] ?: false,
                    'citationsProvider' => $settings['provider'] ?: 'all'
                )
            ));
            $smarty->addJavaScript(
                'citations',
                $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/citations.js'
            );
            $args[2] .= & $smarty->fetch($this->getTemplateResource('citations.tpl'));
        }
    }


    public function setPageHandler($hookName, $params): bool
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
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $actionArgs): array
    {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled() ? array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url(
                            $request,
                            null,
                            null,
                            'manage',
                            null,
                            array('verb' => 'settings', 'plugin' => $this->getName(),
                                'category' => 'generic'
                            )
                        ),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ) : array(),
            parent::getActions($request, $actionArgs)
        );
    }


    /**
     * @copydoc Plugin::manage()
     * @throws Exception
     */
    public function manage($args, $request): JSONMessage
    {

        if ('settings' === $request->getUserVar('verb')) {
            $context = $request->getContext();
            $contextId = ($context == null) ? 0 : $context->getId();
            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->registerPlugin('function', 'plugin_url', [$this, 'smartyPluginUrl']);

            $templateMgr->assign('citationsProviderOptions', [
                'all' => 'plugins.generic.citations.options.all',
                'scopus' => 'plugins.generic.citations.options.scopus',
                'crossref' => 'plugins.generic.citations.options.crossref'
            ]);
            $form = new CitationsSettingsForm($this, $contextId);
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

    private function getPubId($smarty): ?string
    {
        $application = Application::getName();
        $submission = null;
        if ('ojs' === $application) {
            $submission = $smarty->getTemplateVars('article');
        } elseif ('ops' === $application) {
            $submission = $smarty->getTemplateVars('preprint');
        }

        return $submission ?? $submission->getStoredPubId('doi');
    }

}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\citations\CitationsPlugin', '\CitationsPlugin');
}
