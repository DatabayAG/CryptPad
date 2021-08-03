<?php

declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace CryptPad\Repository;

use ilDBInterface;
use CryptPad\Model\Member;
use ilPDOStatement;

/**
 * Class MemberRepository
 * @author Fabian Helfer <fhelfer@databay.de>
 *
 */
class MemberRepository
{
    /**
     * @var MemberRepository
     */
    private static $instance;
    /**
     * @var ilDBInterface
     */
    protected $db;

    /**
     * @var string
     */
    private $tablename = 'rep_robj_xcrp_member';

    /**
     * @var array
     */
    private $fields = [
        'id'               => ['integer', 'int'],
        'obj_id'           => ['integer', 'int'],
        'user_id'           => ['integer', 'int'],
        'rights'           => ['text'],
    ];

    /**
     * MemberRepository constructor.
     * @param ilDBInterface|null $db
     */
    public function __construct(ilDBInterface $db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            global $DIC;
            $this->db = $DIC->database();
        }
    }

    /**
     * Returns an instance of itself
     *
     * @param ilDBInterface|null $db
     * @return static
     */
    public static function getInstance(ilDBInterface $db = null) : self
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new self($db);
    }

    /**
     * Creates a new row in the Member database table.
     * @param Member $member
     * @return Member
     */
    public function create(Member $member) : Member
    {

        if (empty($member->getId())) {
            $member->setId(($this->db->nextId($this->tablename)));
        }
        $this->db->manipulateF(
            'INSERT INTO ' . $this->tablename . ' (id, obj_id, user_id, rights) VALUES ' .
            '(%s, %s, %s, %s)',
            ['integer', 'integer', 'integer', 'text'],
            [
                $member->getId(),
                $member->getObjId(),
                $member->getUserId(),
                $member->getRights(),
            ]
        );

        return $member;
    }

    /**
     * Finds a Member by [attr => value] tupel
     * @param $attr
     * @param $value
     * @return array
     */
    public function readBy($attr, $value) : array
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename . " WHERE {$attr} = {$value}");

        return $this->readQuery($result);
    }

    /**
     * Finds a Member by multiple [attr => value] tupel in array
     * Returns all results
     * @param array $attrArray
     * @return array
     */
    public function readByAttributes(array $attrArray) : array
    {
        $attrString = '';
        foreach ($attrArray as $key => $value) {
            $attrString .= $key . ' = "' . $value . '" AND ';
        }
        $attrString = substr($attrString, 0, -4);
        $result = $this->db->query('SELECT * FROM ' . $this->tablename . ' WHERE ' . $attrString);

        return $this->readQuery($result);
    }

    /**
     * Reads all rows from the Member database table.
     * @return Member[]
     */
    public function readAll() : array
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename);

        return $this->readQuery($result);
    }

    /**
     * Finds a Member from the Database
     * @param int $id
     * @return Member
     */
    public function read(int $id) : Member
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename . " WHERE id = {$id}");

        return $this->readQuery($result)[0];
    }

    /**
     * Executes Query and returns result in array
     * @param ilPDOStatement $query
     * @return Member[]
     */
    public function readQuery(ilPDOStatement $query) : array
    {
        $data = $this->db->fetchAll($query);
        foreach ($data as $key => $value) {
            $data[$key] = new Member();
            $data[$key]->setId((int) $value['id']);
            $data[$key]->setObjId((int) $value['obj_id']);
            $data[$key]->setUserId((int) $value['user_id']);
            $data[$key]->setRights($value['rights']);
        }

        return $data;
    }

    /**
     * Updates a object with given id by [attr => value] array
     * @param int   $id
     * @param array $attr
     * @return bool
     */
    public function update(int $id, array $attr) : bool
    {
        $updateString = '';
        foreach ($attr as $key => $value) {
            $updateString .= $key . ' = "' . $value . '", ';
        }
        $updateString = substr($updateString, 0, -2);
        $affected_rows = $this->db->manipulate("UPDATE {$this->tablename} SET " .
            $updateString . " WHERE id = {$this->db->quote($id, 'integer')}");

        return 1 === $affected_rows;
    }

    /**
     * Removes a Member from the Database
     * @param int $id
     */
    public function delete(int $id) : void
    {
        $this->db->manipulate("DELETE FROM {$this->tablename} WHERE id = {$this->db->quote($id, 'integer')}");

    }
}
