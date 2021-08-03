<?php

declare(strict_types=1);

namespace CryptPad\Services;

/**
 * Class CryptPadService
 * @package CryptPad\Services
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class CryptPadService
{
    /**
     * TODO: replace
     *  CryptPadService => Class Name
     *  CryptPad => Package Root
     * @var CryptPadService
     */
    private static $instance;

    /**
     * CryptPadServiceService constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns an instance of CryptPadService
     * @return CryptPadService
     */
    public static function getInstance() : self
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    /**
     * Returns a Server where Cryptpad is running
     * @return string
     */
    public function getServer() : string
    {
        return 'localhost:3000';
    }
}
