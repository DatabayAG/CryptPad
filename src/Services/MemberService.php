<?php

declare(strict_types=1);

namespace CryptPad\Services;

use CryptPad\Repository\MemberRepository;

/**
 * Class MemberService
 * @package CryptPad\Services
 * @author Fabian Helfer <fhelfer@databay.de>
 */
class MemberService
{
    /**
     * @var MemberService
     */
    private static $instance;

    /**
     * MemberService constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns an instance of MemberService
     * @return MemberService
     */
    public static function getInstance() : self
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    public function updateMembers($ids, $rights) {
        foreach ($ids as $id) {
            MemberRepository::getInstance()->update((int) $id, ['rights' => $rights[$id]]);
        }
        return true;
    }

    public function deleteMembers($ids) {
        foreach ($ids as $id) {
            MemberRepository::getInstance()->delete((int) $id);
        }
        return true;
    }
}
