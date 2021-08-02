<?php

declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace CryptPad\Utility;

use ILIAS\DI\Container;
use CryptPad\Controller\Base;

/**
 * Class Dispatcher.
 *
 * @author  Timo Müller <timomueller@databay.de>
 */
class Dispatcher
{
    /** @var self */
    private static $instance = null;
    /** @var \ilObjCryptPadGUI */
    private $coreController;
    /** @var string */
    private $defaultController = '';
    /** @var Container */
    private $dic;

    private function __clone()
    {
    }

    /**
     * Dispatcher constructor.
     */
    private function __construct(\ilObjCryptPadGUI $baseController, string $defaultController = '')
    {
        $this->coreController = $baseController;
        $this->defaultController = $defaultController;
    }

    public function setDic(Container $dic) : void
    {
        $this->dic = $dic;
    }

    public static function getInstance(\ilObjCryptPadGUI $baseController) : self
    {
        if (null === self::$instance) {
            self::$instance = new self($baseController);
        }

        return self::$instance;
    }

    public function dispatch(string $cmd) : string
    {
        switch ($cmd)
        {
            case "editProperties":   // list all commands that need write permission here
            case "updateProperties":
            case "saveProperties":
            case "showExport":
            case "showContent":   // list all commands that need read permission here
            case "setStatusToCompleted":
            case "setStatusToFailed":
            case "setStatusToInProgress":
            case "setStatusToNotAttempted":
                $cmd = 'DefaultController.' . $cmd;
            case "":
                $cmd = 'DefaultController.showContent';
        }

        $controller = $this->getController($cmd);
        $command = $this->getCommand($cmd);
        $controller = $this->instantiateController($controller);

        return $controller->$command();
    }

    protected function getController(string $cmd) : string
    {
        $parts = explode('.', $cmd);

        if (count($parts) >= 1) {
            return $parts[0];
        }

        return $this->defaultController ?: 'Error';
    }

    protected function getCommand(string $cmd) : string
    {
        $parts = explode('.', $cmd);

        if (2 === count($parts)) {
            $cmd = $parts[1];

            return $cmd . 'Cmd';
        }

        return '';
    }

    protected function instantiateController(string $controller) : Base
    {
        $class = "CryptPad\\Controller\\$controller";

        return new $class($this->getCoreController(), $this->dic);
    }

    protected function getControllerPath() : string
    {
        $path = $this->getCoreController()->getPluginObject()->getDirectory() .
            DIRECTORY_SEPARATOR .
            'src' .
            DIRECTORY_SEPARATOR .
            'Controller' .
            DIRECTORY_SEPARATOR;

        return $path;
    }

    protected function requireController(string $controller) : void
    {
        require_once $this->getControllerPath() . $controller . '.php';
    }

    public function getCoreController() : \ilObjCryptPadGUI
    {
        return $this->coreController;
    }

    public function setCoreController(\ilObjCryptPadGUI $coreController) : void
    {
        $this->coreController = $coreController;
    }
}
