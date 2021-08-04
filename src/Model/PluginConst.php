<?php

declare(strict_types=1);

namespace CryptPad\Model;

/**
 * Class Member
 * @package CryptPad\Model
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class PluginConst
{
    /**
     * @var ?int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $value;

    /**
     * @return int|null
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
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value) : void
    {
        $this->value = $value;
    }


}