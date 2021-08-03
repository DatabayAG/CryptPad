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

    public function showOverviewCmd() {
        return 'Hello World';
    }

    public function getDefaultCommand() : string
    {
        return '';
    }
}
