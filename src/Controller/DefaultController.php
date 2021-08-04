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
use CryptPad\Model\PluginConst;
use CryptPad\Repository\PluginConstRepository;

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
     * @ineritdoc
     */
    protected function init() : void
    {
        parent::init();
        $this->ctrl->saveParameter($this->getCoreController(), 'ref_id');
    }

    public function showContentCmd() {

        $this->getCoreController()->tabs->activateTab("content");
        $link = $this->getCoreController()->object->getDocLink();
        if(!$link) {
            $server = PluginConstRepository::getInstance()->readBy("name", "server")[0];
            if($server) {
                $link = $server->getValue();
            } else {
                return $this->getCoreController()->getPluginObject()->txt("server-missing");
            }
        }
        return
            '<iframe
            src=' . $link . '
            width="1450" 
            height="600" 
            name="SELFHTML_in_a_box">

            <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
            Sie können die eingebettete Seite über den folgenden Verweis aufrufen: 
            <a href=' . $link . '>CryptPad</a>
            </p>
        </iframe>';
    }

    public function getDefaultCommand() : string
    {
        return '';
    }
}
