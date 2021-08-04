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
use CryptPad\Services\CryptPadService;
use CryptPad\Repository\MemberRepository;
use ilConfirmationGUI;
use CryptPad\Model\Member;

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
        $this->getCoreController()->tabs->activateTab('content');
        $memberRepository = MemberRepository::getInstance();
        $member = $memberRepository->readByAttributes([
            'obj_id' =>$this->getCoreController()->object->getId(),
            'user_id' => $this->user->getId()
        ])[0];
        if (!$member && $this->user->getId() != ilObjCryptPad::_lookupOwner($this->getCoreController()->object->getId())) {
            $form = new \ilConfirmationGUI();

            $form->setHeaderText($this->plugin->txt("request-join"));
            $form->setConfirm($this->lng->txt('join'), 'DefaultController.join');
            $form->setCancel($this->lng->txt('cancel'), 'DefaultController.back');
            $form->setFormAction($this->ctrl->getFormAction($this->getCoreController(), "DefaultController.join"));
            return $form->getHTML();
        }

        if($member && !$member->getRights() && $this->user->getId() != ilObjCryptPad::_lookupOwner($this->getCoreController()->object->getId())) {
            return $this->plugin->txt('wrong-access-rights');

        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/pad/');
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        curl_close($ch);

        $dom = new DOMDocument();
        $dom->loadHTMLFile('http://localhost:3000');
        $html = $dom->saveHTML();
        $body = $dom->getElementsByTagName('body')->item(0);


        $cryptPadService = CryptPadService::getInstance();
        $url = "http://" . $cryptPadService->getServer() . "/";
        $type = $this->getCoreController()->object->getDocType();
        $readid = $this->getCoreController()->object->getDocReadId();
        $writeid = $this->getCoreController()->object->getDocWriteId();
        if($type & ($writeid & $readid)) {
            if($member->getRights() === "write") {
                $url.= $type . '/#/2/' . $type . '/edit/' . $writeid;
            } elseif ($member->getRights() === "read") {
                $url.= $type . '/#/2/' . $type . '/view/' . $readid;
            } else {
                return $this->plugin->txt('wrong-access-rights');
            }
        }


        return
            '<iframe
            src=' . $url . '
            width="1450" 
            height="600" 
            name="SELFHTML_in_a_box">

            <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
            Sie können die eingebettete Seite über den folgenden Verweis aufrufen: 
            <a href=' . $url . '>SELFHTML</a>
            </p>
        </iframe>';
    }

    public function joinCmd() {
        $member = new Member();
        $member->setUserId($this->user->getId());
        $member->setObjId($this->getCoreController()->object->getId());
        $member= MemberRepository::getInstance()->create($member);

        if(!$member) {
            ilUtil::sendFailure("join-failed");
        }
        $this->ctrl->redirect($this->getCoreController(), "");
    }

    public function backCmd() {
        global $DIC;
        $tree = $DIC['tree'];

        $this->ctrl->setParameterByClass(
            "ilrepositorygui",
            "ref_id",
            $tree->getParentId($this->getRefId())
        );
        $this->ctrl->redirectByClass("ilrepositorygui", "");
    }

    public function getDefaultCommand() : string
    {
        return '';
    }
}
