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

        $url = "http://localhost:3000/";
        $type = $this->getCoreController()->object->getDocType();
        $id = $this->getCoreController()->object->getDocId();
        if($type & $id) {
            $url.= $type . '/#/2/' . $type . '/edit/' . $id;
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

    public function getDefaultCommand() : string
    {
        return '';
    }
}
