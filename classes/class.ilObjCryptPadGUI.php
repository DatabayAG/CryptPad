<?php

use CryptPad\Utility\Dispatcher;

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
    final public function setPluginObject($a_val)
    {
        $this->plugin_object = $a_val;
    }


    /**
     * @return ilCryptPadPlugin
     */
    final public function getPluginObject()
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
    final function getType()
    {
        return ilCryptPadPlugin::ID;
    }

    /**
     * Handles all commmands of this class, centralizes permission checks
     */
    function performCommand($cmd)
    {
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
        if (version_compare(ILIAS_VERSION_NUMERIC, '6.0', '>=')) {
            $this->dic->ui()->mainTemplate()->printToStdOut();
        } else {
            $this->dic->ui()->mainTemplate()->show();
        }
    }

    /**
     * After object has been created -> jump to this command
     */
    function getAfterCreationCmd()
    {
        return "editProperties";
    }

    /**
     * Get standard command
     */
    function getStandardCmd()
    {
        return "showContent";
    }

//
// DISPLAY TABS
//

    /**
     * Set tabs
     */
    function setTabs()
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
            $this->tabs->addTab("export", $this->txt("export"), $ilCtrl->getLinkTargetByClass("ilexportgui", ""));
        }

        // standard permission tab
        $this->addPermissionTab();
        $this->activateTab();
    }

    /**
     * We need this method if we can't access the tabs otherwise...
     */
    private function activateTab() {
        $next_class = $this->ctrl->getCmdClass();

        switch($next_class) {
            case 'ilexportgui':
                $this->tabs->activateTab("export");
                break;
        }

        return;
    }


}
?>