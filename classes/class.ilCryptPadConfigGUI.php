<?php

declare(strict_types=1);

use CourseAutomation\EnumTypes\ObjectStateType;
use CourseAutomation\EnumTypes\StateType;
use CourseAutomation\Form\ConfirmationDialog;
use CourseAutomation\Form\UploadImportJobForm;
use CourseAutomation\HelperClasses\CSV;
use CourseAutomation\Model\ImportJob;
use CourseAutomation\Repository\ImportJobRepository;
use CourseAutomation\Repository\JobObjectRepository;
use CourseAutomation\Services\CSVImportService;
use CourseAutomation\Services\ImportTableService;
use CourseAutomation\Services\StateUpdateService;
use CourseAutomation\Table\CourseAutomationTable;
use CourseAutomation\Table\ObjectDetailsTable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @ilCtrl_Calls ilCryptPadConfigGUI: lPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilObjGroupGUI
 * */
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

    protected $user;

    protected $ilLog;

    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->user = $DIC->user();
        $this->ilLog = $DIC->logger()->root();
        $this->request = $DIC->http()->request();
        $this->plugin = ilCryptPadPlugin::getInstance();
    }

    public function performCommand($cmd)
    {
        switch (true) {
            case method_exists($this, $cmd):
                $this->{$cmd}();
                break;
            default:
                $this->{$this->getDefaultCommand()}();
        }
    }


    public function showConfigurationGui()
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt("Configuration"));

        $text = new ilTextInputGUI($this->plugin->txt("server-address"),"server");
        $val = \CryptPad\Repository\PluginConstRepository::getInstance()->readBy('name', "server")[0];
        $val = $val ? $val->getValue() : "";
        $text->setValue($val);
        $form->addItem($text);

        $form->setFormAction($this->ctrl->getFormAction($this, "saveProperties"));
        $form->addCommandButton("saveProperties", $this->plugin->txt("update"));

        $this->tpl->setContent($form->getHTML());
    }

    public function saveProperties() {
        $server = $this->request->getParsedBody()["server"];
        $pluginConst = new \CryptPad\Model\PluginConst();
        $pluginConst->setName("server");
        $pluginConst->setValue($server);
        \CryptPad\Repository\PluginConstRepository::getInstance()->updateOrCreate($pluginConst);
        $this->showConfigurationGui();
    }

    private function getDefaultCommand()
    {
        return 'showConfigurationGui';
    }
}
