<?php

declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace CryptPad\Controller;

use ilExerciseHandlerGUI;
use ilObjectFactory;
use ilObjExercise;
use ilObjExerciseGUI;
use ilUtil;
use ilPropertyFormGUI;
use ilTextInputGUI;
use ilCheckboxInputGUI;
use DOMDocument;
use ilTemplate;
use ilObjCryptPad;
use ilLPStatusPlugin;
use ilExportGUI;
use ilLPStatus;
use ilLPStatusWrapper;

/**
 * Class ExerciseDownload.
 *
 * @author  Timo Müller <timomueller@databay.de>
 */
class DefaultController extends RepositoryObject
{
    /**
     * @var \ilCryptPadPlugin
     */
    protected $plugin;

    public function getObjectGuiClass() : string
    {
        return \ilObjCryptPadGUI::class;
    }

    /**
     * @ineritdoc
     */
    public function getObjectGuiConstructorParams() : array
    {

    }

    protected function beforeLocatorIsBuild() : void
    {

    }

    /**
     * @ineritdoc
     */
    protected function init() : void
    {
        parent::init();
        $this->plugin = \ilCryptPadPlugin::getInstance();
        $this->ctrl->saveParameter($this->getCoreController(), 'ref_id');
    }

/**
* Edit Properties. This commands uses the form class to display an input form.
*/
    protected function editProperties()
    {
        $this->tabs->activateTab("properties");
        $form = $this->initPropertiesForm();
        $this->addValuesToForm($form);
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @return ilPropertyFormGUI
     */
    protected function initPropertiesForm() {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt("obj_xtst"));

        $title = new ilTextInputGUI($this->plugin->txt("title"), "title");
        $title->setRequired(true);
        $form->addItem($title);

        $description = new ilTextInputGUI($this->plugin->txt("description"), "description");
        $form->addItem($description);

        $online = new ilCheckboxInputGUI($this->plugin->txt("online"), "online");
        $form->addItem($online);

        $form->setFormAction($this->ctrl->getFormAction($this, "saveProperties"));
        $form->addCommandButton("saveProperties", $this->plugin->txt("update"));

        return $form;
    }

    /**
     * @param $form ilPropertyFormGUI
     */
    protected function addValuesToForm(&$form) {
        $form->setValuesByArray(array(
            "title" => $this->getCoreController()->object->getTitle(),
            "description" => $this->getCoreController()->object->getDescription(),
            "online" => $this->getCoreController()->object->isOnline(),
        ));
    }

    /**
     *
     */
    protected function saveProperties() {
        $form = $this->initPropertiesForm();
        $form->setValuesByPost();
        if($form->checkInput()) {
            $this->fillObject($this->getCoreController()->object, $form);
            $this->getCoreController()->object->update();
            ilUtil::sendSuccess($this->plugin->txt("update_successful"), true);
            $this->ctrl->redirect($this, "editProperties");
        }

    return $form->getHTML();
    }


    public function showContentCmd() {

        $dom = new DOMDocument();
        $dom->loadHTMLFile('http://localhost:3000');
        $html = $dom->saveHTML();
        $body = $dom->getElementsByTagName('body')->item(0);
        return
            '<iframe
            src="http://localhost:3000"
            width="1450" 
            height="600" 
            name="SELFHTML_in_a_box">

            <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
            Sie können die eingebettete Seite über den folgenden Verweis aufrufen: 
            <a href="https://wiki.selfhtml.org/wiki/Startseite">SELFHTML</a>
            </p>
        </iframe>';

        $this->getCoreController()->tabs->activateTab("content");
        /** @var ilTemplate $template */
        $template = $this->plugin->getTemplate("tpl.content.html");
        /** @var ilObjCryptPad $object */
        $object = $this->getCoreController()->object;
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
        $progress = new ilLPStatusPlugin($this->getCoreController()->object->getId());
        $status = $progress->determineStatus($this->getCoreController()->object->getId(), $ilUser->getId());
        $template->setVariable("LP_STATUS", $this->plugin->txt("lp_status_".$status));
        $template->setVariable("LP_INFO", $this->plugin->txt("lp_status_info"));

        return $template->get();
    }

    /**
     * @param $object ilObjCryptPad
     * @param $form ilPropertyFormGUI
     */
    private function fillObject($object, $form) {
        $object->setTitle($form->getInput('title'));
        $object->setDescription($form->getInput('description'));
        $object->setOnline($form->getInput('online'));
    }

    protected function showExport() {
        require_once("./Services/Export/classes/class.ilExportGUI.php");
        $export = new ilExportGUI($this);
        $export->addFormat("xml");
        $ret = $this->ctrl->forwardCommand($export);

    }

    private function setStatusToCompleted() {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_COMPLETED_NUM);
    }

    private function setStatusAndRedirect($status) {
        global $ilUser;
        $_SESSION[$this->getCoreController()::LP_SESSION_ID] = $status;
        ilLPStatusWrapper::_updateStatus($this->getCoreController()->object->getId(), $ilUser->getId());
        $this->ctrl->redirect($this, $this->getStandardCmd());
    }

    protected function setStatusToFailed() {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_FAILED_NUM);
    }

    protected function setStatusToInProgress() {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_IN_PROGRESS_NUM);
    }

    protected function setStatusToNotAttempted() {
        $this->setStatusAndRedirect(ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM);
    }

    public function getDefaultCommand() : string
    {
        return '';
    }
}
