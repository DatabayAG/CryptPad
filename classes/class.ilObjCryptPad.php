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
    protected $docLink;

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
        global $ilDB, $ilUser;

        $ilDB->manipulate("INSERT INTO rep_robj_xcrp_data ".
            "(id, is_online, doc_link) VALUES (".
            $ilDB->quote($this->getId(), "integer").",".
            $ilDB->quote(0, "integer").",".
            "null".
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
            $this->setDocLink($rec['doc_link']);
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
            " doc_link = ".$ilDB->quote($this->getDocLink(), "text")." ".
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
        $new_obj->setDocLink($this->getDocLink());
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
    public function getDocLink() : ?string
    {
        return $this->docLink;
    }

    /**
     * @param string|null $docLink
     */
    public function setDocLink(?string $docLink) : void
    {
        $this->docLink = $docLink;
    }
}
?>