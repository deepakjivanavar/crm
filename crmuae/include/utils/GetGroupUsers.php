<?php
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
*
 ********************************************************************************/


/** Class to retreive all the nectarcrm_users present in a group 
 *
 */
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/GetParentGroups.php');

class GetGroupUsers {

	var $group_users = array();
	var $group_subgroups = array();
	static $groupIdsList = array();

	/** to get all the nectarcrm_users and nectarcrm_groups of the specified group
	 * @params $groupId --> Group Id :: Type Integer
	 * @returns the nectarcrm_users present in the group in the variable $parent_groups of the class
	 * @returns the sub nectarcrm_groups present in the group in the variable $group_subgroups of the class
	 */
	function getAllUsersInGroup($groupid) {
		global $adb, $log;
		$log->debug("Entering getAllUsersInGroup(" . $groupid . ") method...");
		GetGroupUsers::$groupIdsList[$groupid] = $groupid;
		//Retreiving from the user2grouptable
		$query = "select * from nectarcrm_users2group where groupid=?";
		$result = $adb->pquery($query, array($groupid));
		$num_rows = $adb->num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$now_user_id = $adb->query_result($result, $i, 'userid');
			if (!in_array($now_user_id, $this->group_users)) {
				$this->group_users[] = $now_user_id;
			}
		}


		//Retreiving from the nectarcrm_group2role
		$query = "select * from nectarcrm_group2role where groupid=?";
		$result = $adb->pquery($query, array($groupid));
		$num_rows = $adb->num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$now_role_id = $adb->query_result($result, $i, 'roleid');
			$now_role_users = array();
			$now_role_users = getRoleUsers($now_role_id);

			foreach ($now_role_users as $now_role_userid => $now_role_username) {
				if (!in_array($now_role_userid, $this->group_users)) {
					$this->group_users[] = $now_role_userid;
				}
			}
		}

		//Retreiving from the nectarcrm_group2rs
		$query = "select * from nectarcrm_group2rs where groupid=?";
		$result = $adb->pquery($query, array($groupid));
		$num_rows = $adb->num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$now_rs_id = $adb->query_result($result, $i, 'roleandsubid');
			$now_rs_users = getRoleAndSubordinateUsers($now_rs_id);
			foreach ($now_rs_users as $now_rs_userid => $now_rs_username) {
				if (!in_array($now_rs_userid, $this->group_users)) {
					$this->group_users[] = $now_rs_userid;
				}
			}
		}
		//Retreving from group2group
		$query = "select * from nectarcrm_group2grouprel where groupid=?";
		$result = $adb->pquery($query, array($groupid));
		$num_rows = $adb->num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$now_grp_id = $adb->query_result($result, $i, 'containsgroupid');


			$focus = new GetGroupUsers();
			$focus->getAllUsersInGroup($now_grp_id);
			$now_grp_users = $focus->group_users;
			$now_grp_grps = $focus->group_subgroups;
			if (!array_key_exists($now_grp_id, $this->group_subgroups)) {
				$this->group_subgroups[$now_grp_id] = $now_grp_users;
			}



			foreach ($focus->group_users as $temp_user_id) {
				if (!in_array($temp_user_id, $this->group_users)) {
					$this->group_users[] = $temp_user_id;
				}
			}


			foreach ($focus->group_subgroups as $temp_grp_id => $users_array) {
				if (!array_key_exists($temp_grp_id, $this->group_subgroups)) {
					$this->group_subgroups[$temp_grp_id] = $focus->group_users;
				}
			}
		}
		$log->debug("Exiting getAllUsersInGroup method...");
	}

}

?>
