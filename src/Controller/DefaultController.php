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
 * @ilCtrl_isCalledBy: Cryptpad\Controller\DefaultController: ilObjPluginDispatchGUI, ilObjCryptPadGUI
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

    public function getDefaultCommand() : string
    {
        return '';
    }
}
