<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/modules/Reports/Reports.php');

class nectarcrm_Report_Model extends Reports {

	static function getInstance($reportId = "") {
		$self = new self();
		return $self->Reports($reportId);
	}

	function Reports($reportId = "") {
		$db = PearDatabase::getInstance();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userId = $currentUser->getId();
		$currentUserRoleId = $currentUser->get('roleid');
		$subordinateRoles = getRoleSubordinates($currentUserRoleId);
		array_push($subordinateRoles, $currentUserRoleId);

		$this->initListOfModules();

		if($reportId != "") {
			// Lookup information in cache first
			$cachedInfo = VTCacheUtils::lookupReport_Info($userId, $reportId);
			$subOrdinateUsers = VTCacheUtils::lookupReport_SubordinateUsers($reportId);

			if($cachedInfo === false) {
				$ssql = "SELECT nectarcrm_reportmodules.*, nectarcrm_report.* FROM nectarcrm_report
							INNER JOIN nectarcrm_reportmodules ON nectarcrm_report.reportid = nectarcrm_reportmodules.reportmodulesid
							WHERE nectarcrm_report.reportid = ?";
				$params = array($reportId);

				require_once('include/utils/GetUserGroups.php');
				require('user_privileges/user_privileges_'.$userId.'.php');

				$userGroups = new GetUserGroups();
				$userGroups->getAllUserGroups($userId);
				$userGroupsList = $userGroups->user_groups;

				if(!empty($userGroupsList) && $currentUser->isAdminUser() == false) {
					$userGroupsQuery = " (shareid IN (".generateQuestionMarks($userGroupsList).") AND setype='groups') OR";
					foreach($userGroupsList as $group) {
						array_push($params, $group);
					}
				}

				$nonAdminQuery = " nectarcrm_report.reportid IN (SELECT reportid from nectarcrm_reportsharing
									WHERE $userGroupsQuery (shareid=? AND setype='users'))";
				if($currentUser->isAdminUser() == false) {
					$ssql .= " AND (($nonAdminQuery)
								OR nectarcrm_report.sharingtype = 'Public'
								OR nectarcrm_report.owner = ? OR nectarcrm_report.owner IN
									(SELECT nectarcrm_user2role.userid FROM nectarcrm_user2role
									INNER JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_user2role.userid
									INNER JOIN nectarcrm_role ON nectarcrm_role.roleid = nectarcrm_user2role.roleid
									WHERE nectarcrm_role.parentrole LIKE '$current_user_parent_role_seq::%') 
								OR (nectarcrm_report.reportid IN (SELECT reportid FROM nectarcrm_report_shareusers WHERE userid = ?))";
					if(!empty($userGroupsList)) {
						$ssql .= " OR (nectarcrm_report.reportid IN (SELECT reportid FROM nectarcrm_report_sharegroups 
									WHERE groupid IN (".generateQuestionMarks($userGroupsList).")))";
					}
					$ssql .= " OR (nectarcrm_report.reportid IN (SELECT reportid FROM nectarcrm_report_sharerole WHERE roleid = ?))
							   OR (nectarcrm_report.reportid IN (SELECT reportid FROM nectarcrm_report_sharers 
								WHERE rsid IN (".generateQuestionMarks($subordinateRoles).")))
							  )";
					array_push($params, $userId, $userId, $userId);
					foreach($userGroupsList as $groups) {
						array_push($params, $groups);
					}
					array_push($params, $currentUserRoleId);
					foreach($subordinateRoles as $role) {
						array_push($params, $role);
					}
				}
				$result = $db->pquery($ssql, $params);

				if($result && $db->num_rows($result)) {
					$reportModulesRow = $db->fetch_array($result);

					// Update information in cache now
					VTCacheUtils::updateReport_Info(
							$userId, $reportId, $reportModulesRow["primarymodule"],
							$reportModulesRow["secondarymodules"], $reportModulesRow["reporttype"],
							$reportModulesRow["reportname"], $reportModulesRow["description"],
							$reportModulesRow["folderid"], $reportModulesRow["owner"]
					);
				}

				$subOrdinateUsers = Array();

				$subResult = $db->pquery("SELECT userid FROM nectarcrm_user2role
									INNER JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_user2role.userid
									INNER JOIN nectarcrm_role ON nectarcrm_role.roleid = nectarcrm_user2role.roleid
									WHERE nectarcrm_role.parentrole LIKE '$current_user_parent_role_seq::%'", array());

				$numOfSubRows = $db->num_rows($subResult);

				for($i=0; $i<$numOfSubRows; $i++) {
					$subOrdinateUsers[] = $db->query_result($subResult, $i,'userid');
				}

				// Update subordinate user information for re-use
				VTCacheUtils::updateReport_SubordinateUsers($reportId, $subOrdinateUsers);

				// Re-look at cache to maintain code-consistency below
				$cachedInfo = VTCacheUtils::lookupReport_Info($userId, $reportId);
			}

			if($cachedInfo) {
				$this->primodule = $cachedInfo["primarymodule"];
				$this->secmodule = $cachedInfo["secondarymodules"];
				$this->reporttype = $cachedInfo["reporttype"];
				$this->reportname = decode_html($cachedInfo["reportname"]);
				$this->reportdescription = decode_html($cachedInfo["description"]);
				$this->folderid = $cachedInfo["folderid"];
				if($currentUser->isAdminUser() == true || in_array($cachedInfo["owner"], $subOrdinateUsers) || $cachedInfo["owner"]==$userId) {
					$this->is_editable = true;
				}else{
					$this->is_editable = false;
				}
			}
		}
		return $this;
	}

	function isEditable() {
		return $this->is_editable;
	}

	function getModulesList() {
		foreach($this->module_list as $key=>$value) {
			if(isPermitted($key,'index') == "yes") {
				$modules [$key] = vtranslate($key, $key);
			}
		}
		asort($modules);
		return $modules;
	}
}