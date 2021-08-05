<?php

use CryptPad\Utility\Dispatcher;
use CryptPad\Table\MemberTable;
use CryptPad\Repository\MemberRepository;
use CryptPad\Repository\PluginConstRepository;

include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");
require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
require_once("./Services/Form/classes/class.ilTextInputGUI.php");
require_once("./Services/Form/classes/class.ilCheckboxInputGUI.php");
require_once("./Services/Tracking/classes/class.ilLearningProgress.php");
require_once("./Services/Tracking/classes/class.ilLPStatusWrapper.php");
require_once("./Services/Tracking/classes/status/class.ilLPStatusPlugin.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/CryptPad/classes/class.ilCryptPadPlugin.php");
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * class ilObjCryptPadGUI
 * @author Fabian Helfer <fhelfer@databay.de>
 * @ilCtrl_isCalledBy ilObjCryptPadGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
 * @ilCtrl_Calls ilObjCryptPadGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilExportGUI
 */
class ilObjCryptPadGUI extends ilObjectPluginGUI
{
    const LP_SESSION_ID = 'xcrp_lp_session_state';

    private $dic;

    /** @var  ilCryptPadPlugin */
    private $plugin_object;

    /** @var  ilCtrl */
    protected $ctrl;

    /** @var  ilTabsGUI */
    public $tabs;

    /** @var  ilTemplate */
    public $tpl;

    /**
     * Initialisation
     */
    protected function afterConstructor() : void
    {
        global $ilCtrl, $ilTabs, $tpl, $DIC;
        $this->dic = $DIC;
        $this->ctrl = $ilCtrl;
        $this->tabs = $ilTabs;
        $this->tpl = $tpl;
    }

    /**
     * @param ilCryptPadPlugin $a_val
     */
    final public function setPluginObject(ilCryptPadPlugin $a_val) : void
    {
        $this->plugin_object = $a_val;
    }


    /**
     * @return ilCryptPadPlugin
     */
    final public function getPluginObject() : ilCryptPadPlugin
    {
        return $this->plugin_object;
    }

    public function executeCommand()
    {
        return parent::executeCommand();
    }

    /**
     * Get type.
     */
    final public function getType() : string
    {
        return ilCryptPadPlugin::ID;
    }

    /**
     * Handles all commmands of this class, centralizes permission checks
     */
    public function performCommand($cmd) : void
    {
        switch ($cmd) {
            case "editProperties":   // list all commands that need write permission here
            case "updateProperties":
            case "saveProperties":
                $this->checkPermission("write");
                $this->$cmd();
                break;
            case "showContent":
                $this->checkPermission("read");
                $this->$cmd();

        }
    }

    public function showContent() : void
    {
        $this->tabs->activateTab("content");
        $link = $this->object->getDocLink();
        if (!$link) {
            $server = PluginConstRepository::getInstance()->readBy("name", "server")[0];
            if ($server) {
                $link = $server->getValue();
            } else {
                $this->tpl->setContent($this->getPluginObject()->txt("server-missing"));
            }
        }

        $width = PluginConstRepository::getInstance()->readBy("name", "width")[0];
        $height = PluginConstRepository::getInstance()->readBy("name", "height")[0];
        $width = $width && $width->getValue() !== "" ? $width->getValue(): '1450';
        $height = $height && $height->getValue() !== "" ? $height->getValue(): '600';

        $template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/CryptPad/templates/tpl.il_xcrp_cryptpad_iframe.html', true, true);
        $template->setCurrentBlock("default");
        $template->setVariable("VAL_URL", $link);
        $template->setVariable("VAL_WIDTH", $width);
        $template->setVariable("VAL_HEIGHT", $height);
        $template->parseCurrentBlock();

        $this->tpl->setContent($template->get());
    }


    /**
     * After object has been created -> jump to this command
     */
    public function getAfterCreationCmd() : string
    {
        return "editProperties";
    }

    /**
     * Get standard command
     */
    public function getStandardCmd() : string
    {
        return "showContent";
    }

//
    // DISPLAY TABS
//

    /**
     * Set tabs
     */
    public function setTabs() : void
    {
        global $ilCtrl, $ilAccess;

        // tab for the "show content" command
        if ($ilAccess->checkAccess("read", "", $this->object->getRefId())) {
            $this->tabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, "showContent"));
        }

        // standard info screen tab
        $this->addInfoTab();

        // a "properties" tab
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())) {
            $this->tabs->addTab("properties", $this->txt("properties"), $ilCtrl->getLinkTarget($this, "editProperties"));
        }

        // standard permission tab
        $this->addPermissionTab();
        $this->activateTab();
    }

    /**
     * We need this method if we can't access the tabs otherwise...
     */
    private function activateTab() : void
    {
        $next_class = $this->ctrl->getCmdClass();

        switch ($next_class) {
            case 'ilexportgui':
                $this->tabs->activateTab("export");
                break;
        }

        return;
    }

    /**
     * Edit Properties. This commands uses the form class to display an input form.
     */
    protected function editProperties() : void
    {
        $this->tabs->activateTab("properties");
        $form = $this->initPropertiesForm();
        $this->addValuesToForm($form);
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @return ilPropertyFormGUI
     */
    protected function initPropertiesForm() : ilPropertyFormGUI
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt("obj_xcrp"));

        $title = new ilTextInputGUI($this->plugin->txt("title"), "title");
        $title->setRequired(true);
        $form->addItem($title);

        $description = new ilTextInputGUI($this->plugin->txt("description"), "description");
        $form->addItem($description);

        $online = new ilCheckboxInputGUI($this->plugin->txt("online"), "online");
        $form->addItem($online);


        $docLink = new ilTextInputGUI($this->plugin->txt("docLink"), 'docLink');
        $docLink->setInfo($this->plugin->txt("explanation-doc-link") . '<br>' . $this->plugin->txt("info-doc-link"));
        $form->addItem($docLink);

        $form->setFormAction($this->ctrl->getFormAction($this, "saveProperties"));
        $form->addCommandButton("saveProperties", $this->plugin->txt("update"));

        return $form;
    }

    /**
     * @param $form ilPropertyFormGUI
     */
    protected function addValuesToForm(&$form) : void
    {
        $form->setValuesByArray(array(
            "title" => $this->object->getTitle(),
            "description" => $this->object->getDescription(),
            "online" => $this->object->isOnline(),
            'docLink' => $this->object->getDocLink(),

        ));
    }
    /**
     *
     */
    protected function saveProperties() : void
    {
        $form = $this->initPropertiesForm();
        $form->setValuesByPost();
        if ($form->checkInput()) {
            $this->fillObject($this->object, $form);
            $this->object->update();
            ilUtil::sendSuccess($this->plugin->txt("update_successful"), true);
            $this->ctrl->redirect($this, "editProperties");
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @param $object ilObjCryptPad
     * @param $form ilPropertyFormGUI
     */
    private function fillObject(ilObjCryptPad $object, ilPropertyFormGUI $form) : void
    {
        $object->setTitle($form->getInput('title'));
        $object->setDescription($form->getInput('description'));
        $object->setOnline($form->getInput('online'));
        $object->setDocLink($form->getInput('docLink'));
    }
}
