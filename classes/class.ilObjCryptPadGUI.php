<?php

use CryptPad\Utility\Dispatcher;
use CryptPad\Table\MemberTable;
use CryptPad\Repository\MemberRepository;

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
    protected function afterConstructor()
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
    final public function setPluginObject($a_val) : void
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

    public function executeCommand() {
        global $tpl;


        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class) {
            case 'ilexportgui':
                // only if plugin supports it?
                $tpl->setTitle($this->object->getTitle());
                $tpl->setTitleIcon(ilObject::_getIcon($this->object->getId()));
                $this->setLocator();
                $tpl->getStandardTemplate();
                $this->setTabs();
                include_once './Services/Export/classes/class.ilExportGUI.php';
                $this->tabs->activateTab("export");
                $exp = new ilExportGUI($this);
                $exp->addFormat('xml');
                $this->ctrl->forwardCommand($exp);
                $tpl->show();
                return;
                break;
        }

        $return_value = parent::executeCommand();

        return $return_value;
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
            case "showExport":
                $this->checkPermission("write");
                $this->$cmd();
                break;
            case "setStatusToCompleted":
            case "setStatusToFailed":
            case "setStatusToInProgress":
            case "setStatusToNotAttempted":
                $this->checkPermission("read");
                $this->$cmd();
                break;
            case "showContent":
                $cmd = 'DefaultController' . $cmd;
            default:
                $this->setPluginObject(ilCryptPadPlugin::getInstance());
                $nextClass = $this->dic->ctrl()->getNextClass();
                switch (strtolower($nextClass)) {
                    default:
                        $dispatcher = Dispatcher::getInstance($this);
                        $dispatcher->setDic($this->dic);

                        $response = $dispatcher->dispatch($this->dic->ctrl()->getCmd());
                        break;
                }
                $this->tpl->setContent($response);
//                if (version_compare(ILIAS_VERSION_NUMERIC, '6.0', '>=')) {
//                    $this->dic->ui()->mainTemplate()->printToStdOut();
//                } else {
//                    $this->dic->ui()->mainTemplate()->show();
//                }
        }
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
    public function setTabs(): void
    {
        global $ilCtrl, $ilAccess;

        // tab for the "show content" command
        if ($ilAccess->checkAccess("read", "", $this->object->getRefId()))
        {
            $this->tabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, "showContent"));
        }

        // standard info screen tab
        $this->addInfoTab();

        // a "properties" tab
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
        {
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

        switch($next_class) {
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
        if($form->checkInput()) {
            $this->fillObject($this->object, $form);
            $this->object->update();
            ilUtil::sendSuccess($this->plugin->txt("update_successful"), true);
            $this->ctrl->redirect($this, "editProperties");
        }
        $this->tpl->setContent($form->getHTML());
    }

    protected function showContent() : void
    {
        $this->tabs->activateTab("content");
        /** @var ilTemplate $template */
        $template = $this->plugin->getTemplate("tpl.content.html");
        /** @var ilObjCryptPad $object */
        $object = $this->object;
        $template->setVariable("TITLE", $object->getTitle());
        $template->setVariable("DESCRIPTION", $object->getDescription());
        $template->setVariable("ONLINE_STATUS", $object->isOnline()?"Online":"Offline");
        $template->setVariable("ONLINE_COLOR", $object->isOnline()?"green":"red");

        $template->setVariable("SET_COMPLETED", $this->ctrl->getLinkTarget($this, "setStatusToCompleted"));
        $template->setVariable("SET_COMPLETED_TXT", $this->plugin->txt("set_completed"));

        $template->setVariable("SET_NOT_ATTEMPTED", $this->ctrl->getLinkTarget($this, "setStatusToNotAttempted"));
        $template->setVariable("SET_NOT_ATTEMPTED_TXT", $this->plugin->txt("set_not_attempted"));

        $template->setVariable("SET_FAILED", $this->ctrl->getLinkTarget($this, "setStatusToFailed"));
        $template->setVariable("SET_FAILED_TXT", $this->plugin->txt("set_failed"));

        $template->setVariable("SET_IN_PROGRESS", $this->ctrl->getLinkTarget($this, "setStatusToInProgress"));
        $template->setVariable("SET_IN_PROGRESS_TXT", $this->plugin->txt("set_in_progress"));

        global $ilUser;
        $progress = new ilLPStatusPlugin($this->object->getId());
        $status = $progress->determineStatus($this->object->getId(), $ilUser->getId());
        $template->setVariable("LP_STATUS", $this->plugin->txt("lp_status_".$status));
        $template->setVariable("LP_INFO", $this->plugin->txt("lp_status_info"));

        $this->tpl->setContent($template->get());
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

    protected function showExport() : void
    {
        require_once("./Services/Export/classes/class.ilExportGUI.php");
        $export = new ilExportGUI($this);
        $export->addFormat("xml");
        $ret = $this->ctrl->forwardCommand($export);

    }


    private function setStatusToCompleted() : void
    {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_COMPLETED_NUM);
    }

    private function setStatusAndRedirect($status) : void
    {
        global $ilUser;
        $_SESSION[self::LP_SESSION_ID] = $status;
        ilLPStatusWrapper::_updateStatus($this->object->getId(), $ilUser->getId());
        $this->ctrl->redirect($this, $this->getStandardCmd());
    }

    protected function setStatusToFailed() : void
    {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_FAILED_NUM);
    }

    protected function setStatusToInProgress() : void
    {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_IN_PROGRESS_NUM);
    }

    protected function setStatusToNotAttempted() : void
    {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM);
    }



}
?>