<?php

declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace CryptPad\Utility;

use ilCtrl;
use ilObject;
use ilObjectDataCache;
use ilObjectFactory;
use ilObjUser;
use ilUtil;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

/**
 * Trait HttpContext.
 *
 * @author  Timo Müller <timomueller@databay.de>
 */
trait HttpContext
{
    /** @var ilObjectDataCache */
    protected $objectCache;
    /** @var ServerRequestInterface */
    protected $httpRequest;
    /** @var ilCtrl */
    protected $ctrl;

    final public function isBaseClass(string $class) : bool
    {
        $baseClass = (string) ($this->httpRequest->getQueryParams()['baseClass'] ?? '');

        return strtolower($class) === strtolower($baseClass);
    }

    final public function hasBaseClass() : bool
    {
        return isset($this->httpRequest->getQueryParams()['baseClass']);
    }

    final public function isCommandClass(string $class) : bool
    {
        $cmdClass = (string) ($this->httpRequest->getQueryParams()['cmdClass'] ?? '');

        return strtolower($class) === strtolower($cmdClass);
    }

    final public function hasCommandClass() : bool
    {
        return isset($this->httpRequest->getQueryParams()['cmdClass']);
    }

    /**
     * @param string[] $cmdClasses
     */
    final public function isOneOfCommandClasses(array $cmdClasses) : bool
    {
        if (! $this->hasCommandClass()) {
            return false;
        }

        return in_array(
            strtolower($this->httpRequest->getQueryParams()['cmdClass']),
            array_map(
                'strtolower',
                $cmdClasses
            )
        );
    }

    /**
     * @param string[] $commands
     */
    final public function isOneOfCommands(array $commands) : bool
    {
        return in_array(
            strtolower((string) $this->ctrl->getCmd()),
            array_map(
                'strtolower',
                $commands
            )
        );
    }

    /**
     * @param string[] $commands
     */
    final public function isOneOfPluginCommandsLike(array $commands) : bool
    {
        return count(array_filter($commands, function (string $command) : bool {
            if (class_exists($command)) {
                $command = (new ReflectionClass($command))->getShortName();
            }

            return false !== strpos(strtolower((string) $this->ctrl->getCmd()), strtolower($command));
        })) > 0;
    }

    final public function getRefId() : int
    {
        $refId = (int) ($this->httpRequest->getQueryParams()['ref_id'] ?? 0);

        return $refId;
    }

    final public function getExerciseRefId() : int
    {
        $refId = $this->getRefId();

        if ($refId <= 0) {
            $refId = $this->getTargetRefId();
        }

        return $refId;
    }


    final public function getPartId() : int
    {
        global $DIC;

        $partId = (int) ($this->httpRequest->getParsedBody()['part_id'] ?? 0);
        if (0 === $partId) {
            $partId = (int) ($this->httpRequest->getQueryParams()['part_id'] ?? 0);
        }

        if (0 === $partId) {
            $exercise = ilObjectFactory::getInstanceByRefId($this->getExerciseRefId());
            $members = $exercise->members_obj->getMembers();

            if (count($members) > 0) {
                $mems = [];
                foreach ($members as $mem_id) {
                    if ('usr' === ilObject::_lookupType($mem_id)) {
                        $name = ilObjUser::_lookupName($mem_id);
                        if ('' !== trim($name['login'])) {
                            $mems[$mem_id] = $name;
                        }
                    }
                }

                $mems = ilUtil::sortArray($mems, 'lastname', 'asc', false, true);

                if (count($mems) > 0) {
                    $partId = key($mems);
                }
            }
        }

        return $partId;
    }

    final public function getTargetRefId() : int
    {
        $target = ((string) $this->httpRequest->getQueryParams()['target'] ?? '');
        if (preg_match('/^[a-zA-Z0-9]+_(\d+)$/', $target, $matches)) {
            if (isset($matches[1]) && is_numeric($matches[1]) && $matches[1] > 0) {
                return (int) $matches[1];
            }
        }

        return 0;
    }

    final public function isObjectOfId(int $objId) : bool
    {
        $refId = $this->getRefId();
        if ($refId <= 0) {
            return false;
        }

        return (int) $this->objectCache->lookupObjId($refId) === $objId;
    }

    final public function isObjectOfType(string $type) : bool
    {
        $refId = $this->getRefId();
        if ($refId <= 0) {
            return false;
        }

        $objId = (int) $this->objectCache->lookupObjId($refId);

        return $this->objectCache->lookupType($objId) === $type;
    }

    final public function isTargetObjectOfType(string $type) : bool
    {
        $refId = $this->getTargetRefId();
        if ($refId <= 0) {
            return false;
        }

        $objId = (int) $this->objectCache->lookupObjId($refId);

        return $this->objectCache->lookupType($objId) === $type;
    }
}
