<?php

declare(strict_types=1);

namespace CryptPad\Model;

/**
 * Class Member
 * @package CryptPad\Model
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class Member
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $objId;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string|null
     */
    private $rights;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->objId;
    }

    /**
     * @param int $objId
     */
    public function setObjId(int $objId) : void
    {
        $this->objId = $objId;
    }

    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId) : void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getRights() : ?string
    {
        return $this->rights;
    }

    /**
     * @param string|null $rights
     */
    public function setRights(?string $rights) : void
    {
        $this->rights = $rights;
    }


}