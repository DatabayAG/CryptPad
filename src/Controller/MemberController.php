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
use ilTemplate;
use ilObjCryptPad;
use ilLPStatusPlugin;
use ilExportGUI;
use ilLPStatus;
use ilLPStatusWrapper;
use CryptPad\Table\MemberTable;
use CryptPad\Repository\MemberRepository;
use CryptPad\Services\MemberService;

/**
 * Class ExerciseDownload.
 *
 * @ilCtrl_isCalledBy: Cryptpad\Controller\DefaultController: ilObjPluginDispatchGUI, ilObjCryptPadGUI
 *
 * @author  Timo Müller <timomueller@databay.de>
 */
class MemberController extends RepositoryObject
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

    public function showOverviewCmd()
    {
        $this->getCoreController()->tabs->activateTab('members');
        $table = new MemberTable($this->getCoreController(), $this->plugin, MemberRepository::getInstance()->readByAttributes([
            "obj_id" => $this->getCoreController()->object->getId()
        ]));
        return $table->getHTML();
    }

    public function saveAllMembersCmd() {
        $body = $this->httpRequest->getParsedBody();
        MemberService::getInstance()->updateMembers(array_keys($body["rights"]), $body["rights"]);
        return $this->showOverviewCmd();
    }

    public function multiCommandCmd()
    {
        $body = $this->httpRequest->getParsedBody();
        $action = isset($body['table_top_cmd']) ? $body['action_2'] : $body['action'];
        $ids = $body["id"];
        if(!$ids) {
            return $this->showOverviewCmd();
        }
        switch ($action) {
            case "save":
                MemberService::getInstance()->updateMembers($ids, $body["rights"]);
                break;
            case "delete":
                MemberService::getInstance()->deleteMembers($ids);
                break;
        }

        return $this->showOverviewCmd();
    }

    public function getDefaultCommand() : string
    {
        return '';
    }
}