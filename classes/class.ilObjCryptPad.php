<?php

include_once("./Services/Repository/classes/class.ilObjectPlugin.php");
require_once("./Services/Tracking/interfaces/interface.ilLPStatusPlugin.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/CryptPad/classes/class.ilObjCryptPadGUI.php");

/**
 */
class ilObjCryptPad extends ilObjectPlugin implements ilLPStatusPluginInterface
{
    /**
     * @var ?string
     */
    protected $docType;
    /**
     * @var ?string
     */
    protected $docWriteId;
    /**
     * @var ?string
     */
    protected $docReadId;

    /**
     * Constructor
     *
     * @access        public
     * @param int $a_ref_id
     */
    function __construct($a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }

    /**
     * Get type.
     */
    final function initType()
    {
        $this->setType(ilCryptPadPlugin::ID);
    }

    /**
     * Create object
     */
    function doCreate()
    {
        global $ilDB;

        $ilDB->manipulate("INSERT INTO rep_robj_xcrp_data ".
            "(id, is_online, doc_type, doc_write_id, doc_read_id) VALUES (".
            $ilDB->quote($this->getId(), "integer").",".
            $ilDB->quote(0, "integer").",".
            "null,null,null".
            ")");
    }

    /**
     * Read data from db
     */
    function doRead()
    {
        global $ilDB;

        $set = $ilDB->query("SELECT * FROM rep_robj_xcrp_data ".
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
        while ($rec = $ilDB->fetchAssoc($set))
        {
            $this->setOnline($rec["is_online"]);
            $this->setDocType($rec['doc_type']);
            $this->setDocWriteId($rec['doc_write_id']);
            $this->setDocReadId($rec['doc_read_id']);

        }
    }

    /**
     * Update data
     */
    function doUpdate()
    {
        global $ilDB;

        $ilDB->manipulate($up = "UPDATE rep_robj_xcrp_data SET ".
            " is_online = ".$ilDB->quote($this->isOnline(), "integer").", ".
            " doc_type = ".$ilDB->quote($this->getDocType(), "text").", ".
            " doc_write_id = ".$ilDB->quote($this->getDocWriteId(), "text").", ".
            " doc_read_id = ".$ilDB->quote($this->getDocReadId(), "text")." ".
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
    }

    /**
     * Delete data from db
     */
    function doDelete()
    {
        global $ilDB;

        $ilDB->manipulate("DELETE FROM rep_robj_xcrp_data WHERE ".
            " id = ".$ilDB->quote($this->getId(), "integer")
        );
    }

    /**
     * Do Cloning
     */
    function doClone($a_target_id,$a_copy_id,$new_obj)
    {
        global $ilDB;

        $new_obj->setOnline($this->isOnline());
        $new_obj->setDocType($this->getDocType());
        $new_obj->setDocWriteId($this->getDocWriteId());
        $new_obj->setDocReadId($this->getDocReadId());
        $new_obj->update();
    }


    /**
     * Set online
     *
     * @param        boolean                online
     */
    function setOnline($a_val)
    {
        $this->online = $a_val;
    }

    /**
     * Get online
     *
     * @return        boolean                online
     */
    function isOnline()
    {
        return $this->online;
    }

    /**
     * @return string|null
     */
    public function getDocType() : ?string
    {
        return $this->docType;
    }

    /**
     * @param ?string $docType
     */
    public function setDocType(?string $docType) : void
    {
        $this->docType = $docType;
    }



    /**
     * Get all user ids with LP status completed
     *
     * @return array
     */
    public function getLPCompleted() {
        return array();
    }

    /**
     * Get all user ids with LP status not attempted
     *
     * @return array
     */
    public function getLPNotAttempted() {
        return array();
    }

    /**
     * Get all user ids with LP status failed
     *
     * @return array
     */
    public function getLPFailed() {
        return array(6);
    }

    /**
     * Get all user ids with LP status in progress
     *
     * @return array
     */
    public function getLPInProgress() {
        return array();
    }

    /**
     * Get current status for given user
     *
     * @param int $a_user_id
     * @return int
     */
    public function getLPStatusForUser($a_user_id) {
        global $ilUser;
        if($ilUser->getId() == $a_user_id)
            return $_SESSION[ilObjCryptPadGUI::LP_SESSION_ID];
        else
            return ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
    }

    /**
     * @return string|null
     */
    public function getDocWriteId() : ?string
    {
        return $this->docWriteId;
    }

    /**
     * @param string|null $docWriteId
     */
    public function setDocWriteId(?string $docWriteId) : void
    {
        $this->docWriteId = $docWriteId;
    }

    /**
     * @return string|null
     */
    public function getDocReadId() : ?string
    {
        return $this->docReadId;
    }

    /**
     * @param string|null $docReadId
     */
    public function setDocReadId(?string $docReadId) : void
    {
        $this->docReadId = $docReadId;
    }
}
?>