<?php

declare(strict_types=1);

namespace CryptPad\Repository;

use ilDBInterface;
use CryptPad\Model\PluginConst;
use ilPDOStatement;

/**
 * Class PluginConstRepository
 * @author Fabian Helfer <fhelfer@databay.de>
 *
 */
class PluginConstRepository
{
    /**
     * @var PluginConstRepository
     */
    private static $instance;
    /**
     * @var ilDBInterface
     */
    protected $db;

    /**
     * @var string
     */
    private $tablename = 'rep_robj_xcrp_const';

    /**
     * @var array
     */
    private $fields = [
        'id' => ['integer', 'int'],
        'name' => ['text'],
        'value' => ['text'],
    ];

    /**
     * PluginConstRepository constructor.
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
     * Creates a new row in the PluginConst database table.
     * @param PluginConst $pluginConst
     * @return PluginConst
     */
    public function create(PluginConst $pluginConst) : PluginConst
    {
        if (empty($pluginConst->getId())) {
            $pluginConst->setId(((int) $this->db->nextId($this->tablename)));
        }
        $this->db->manipulateF(
            'INSERT INTO ' . $this->tablename . ' (id, name, value) VALUES ' .
            '(%s, %s, %s)',
            ['integer', 'text', 'text'],
            [
                $pluginConst->getId(),
                $pluginConst->getName(),
                $pluginConst->getValue(),
            ]
        );

        return $pluginConst;
    }

    /**
     * Finds a PluginConst by [attr => value] tupel
     * @param $attr
     * @param $value
     * @return array
     */
    public function readBy($attr, $value) : array
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename . " WHERE {$attr} = "
        . $this->db->quote($value, "string"));

        return $this->readQuery($result);
    }

    /**
     * Finds a PluginConst by multiple [attr => value] tupel in array
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
     * Reads all rows from the PluginConst database table.
     * @return PluginConst[]
     */
    public function readAll() : array
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename);

        return $this->readQuery($result);
    }

    /**
     * Finds a PluginConst from the Database
     * @param int $id
     * @return PluginConst
     */
    public function read(int $id) : PluginConst
    {
        $result = $this->db->query('SELECT * FROM ' . $this->tablename . " WHERE id = {$id}");

        return $this->readQuery($result)[0];
    }

    /**
     * Executes Query and returns result in array
     * @param ilPDOStatement $query
     * @return PluginConst[]
     */
    public function readQuery(ilPDOStatement $query) : array
    {
        $data = $this->db->fetchAll($query);
        foreach ($data as $key => $value) {
            $data[$key] = new PluginConst();
            $data[$key]->setId((int) $value['id']);
            $data[$key]->setName($value['name']);
            $data[$key]->setValue($value['value']);
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
     * @param PluginConst $pluginConst
     */
    public function updateOrCreate(PluginConst $pluginConst) : void
    {
        $pluginConstGet = $this->readBy("name", $pluginConst->getName())[0];
        if ($pluginConstGet) {
            self::getInstance()->update($pluginConstGet->getId(), ['value' => $pluginConst->getValue()]);
        } else {
            self::getInstance()->create($pluginConst);
        }
    }

    /**
     * Removes a PluginConst from the Database
     * @param int $id
     */
    public function delete(int $id) : void
    {
        $this->db->manipulate("DELETE FROM {$this->tablename} WHERE id = {$this->db->quote($id, 'integer')}");
    }
}
