<?php

declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace CryptPad\Controller;

use CryptPad\Repository\PluginConstRepository;

/**
 * Class ExerciseDownload.
 *
 * @ilCtrl_isCalledBy: Cryptpad\Controller\DefaultController: ilObjPluginDispatchGUI, ilObjCryptPadGUI
 *
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class DefaultController extends Base
{
    /**
     * @ineritdoc
     */
    protected function init() : void
    {
        parent::init();
        $this->ctrl->saveParameter($this->getCoreController(), 'ref_id');
    }

    public function showContentCmd() : string
    {
        $this->getCoreController()->tabs->activateTab("content");
        $link = $this->getCoreController()->object->getDocLink();
        if (!$link) {
            $server = PluginConstRepository::getInstance()->readBy("name", "server")[0];
            if ($server) {
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
