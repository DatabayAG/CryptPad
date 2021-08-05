<?php
include_once("./Services/Repository/classes/class.ilObjectPluginAccess.php");

/**
 * Access/Condition checking for Example object
 *
 * class ilObjCryptPadAccess
 * @author Fabian Helfer <fhelfer@databay.de>
 * @version $Id$
 */
class ilObjCryptPadAccess extends ilObjectPluginAccess
{

    /**
     * Checks whether a user may invoke a command or not
     * (this method is called by ilAccessHandler::checkAccess)
     *
     * Please do not check any preconditions handled by
     * ilConditionHandler here. Also don't do usual RBAC checks.
     *
     * @param	string	$a_cmd		command (not permission!)
     * @param	string	$a_permission	permission
     * @param	int		$a_ref_id		reference id
     * @param	int		$a_obj_id		object id
     * @param	int		$a_user_id		user id (default is current user)
     *
     * @return	boolean		true, if everything is ok
     */
    public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = "") : bool
    {
        global $ilUser, $ilAccess;

        if ($a_user_id == "") {
            $a_user_id = $ilUser->getId();
        }

        switch ($a_permission) {
            case "read":
                if (!self::checkOnline($a_obj_id) &&
                    !$ilAccess->checkAccessOfUser($a_user_id, "write", "", $a_ref_id)) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Check online status of example object
     */
    public static function checkOnline($a_id) : bool
    {
        global $ilDB;

        $set = $ilDB->query(
            "SELECT is_online FROM rep_robj_xcrp_data " .
            " WHERE id = " . $ilDB->quote($a_id, "integer")
        );
        $rec = $ilDB->fetchAssoc($set);
        return (boolean) $rec["is_online"];
    }
}
