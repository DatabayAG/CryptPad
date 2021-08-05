<?php

declare(strict_types=1);

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

/** class ilCryptPadConfigGUI
 * @ilCtrl_Calls ilCryptPadConfigGUI: lPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilObjGroupGUI
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class ilCryptPadConfigGUI extends ilPluginConfigGUI
{
    /**
     * @var ilCryptPadPlugin
     */
    private $plugin;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var RequestInterface|ServerRequestInterface
     */
    private $request;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilLogger
     */
    protected $ilLog;

    /**
     * ilCryptPadConfigGUI constructor.
     */
    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->user = $DIC->user();
        $this->ilLog = $DIC->logger()->root();
        $this->request = $DIC->http()->request();
    }

    /**
     * @param $cmd
     */
    public function performCommand($cmd) : void
    {
        try {
            $this->plugin = ilCryptPadPlugin::getInstance();
            $this->plugin->registerAutoloader();
        } catch (Throwable $e) {
            ilUtil::sendFailure("Plugin has to be activated first");
            return;
        }
        switch (true) {
            case method_exists($this, $cmd):
                $this->{$cmd}();
                break;
            default:
                $this->{$this->getDefaultCommand()}();
        }
    }


    public function showConfigurationGui() : void
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin_object->txt("Configuration"));

        $text = new ilTextInputGUI($this->plugin_object->txt("server-address"), "server");
        $val = \CryptPad\Repository\PluginConstRepository::getInstance()->readBy('name', "server")[0];
        $val = $val ? $val->getValue() : "";
        $text->setValue($val);
        $form->addItem($text);

        $form->setFormAction($this->ctrl->getFormAction($this, "saveProperties"));
        $form->addCommandButton("saveProperties", $this->plugin->txt("update"));

        $this->tpl->setContent($form->getHTML());
    }

    public function saveProperties() : void
    {
        $server = $this->request->getParsedBody()["server"];
        $pluginConst = new \CryptPad\Model\PluginConst();
        $pluginConst->setName("server");
        $pluginConst->setValue($server);
        \CryptPad\Repository\PluginConstRepository::getInstance()->updateOrCreate($pluginConst);
        $this->showConfigurationGui();
    }

    private function getDefaultCommand() : string
    {
        return 'showConfigurationGui';
    }
}
