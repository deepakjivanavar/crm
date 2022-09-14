<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ************************************************************************************/

class Accounts_Module_Model extends nectarcrm_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of nectarcrm_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

		$quickLink = array(
			'linktype' => 'SIDEBARLINK',
			'linklabel' => 'LBL_DASHBOARD',
			'linkurl' => $this->getDashBoardUrl(),
			'linkicon' => '',
		);

		//Check profile permissions for Dashboards
		$moduleModel = nectarcrm_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			$parentQuickLinks['SIDEBARLINK'][] = nectarcrm_Link_Model::getInstanceFromValues($quickLink);
		}
		
		return $parentQuickLinks;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if (($sourceModule == 'Accounts' && $field == 'account_id' && $record)
				|| in_array($sourceModule, array('Campaigns', 'Products', 'Services', 'Emails'))) {

			if ($sourceModule === 'Campaigns') {
				$condition = " nectarcrm_account.accountid NOT IN (SELECT accountid FROM nectarcrm_campaignaccountrel WHERE campaignid = '$record')";
			} elseif ($sourceModule === 'Products') {
				$condition = " nectarcrm_account.accountid NOT IN (SELECT crmid FROM nectarcrm_seproductsrel WHERE productid = '$record')";
			} elseif ($sourceModule === 'Services') {
				$condition = " nectarcrm_account.accountid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";
			} elseif ($sourceModule === 'Emails') {
				$condition = ' nectarcrm_account.emailoptout = 0';
			} else {
				$condition = " nectarcrm_account.accountid != '$record'";
			}

			$position = stripos($listQuery, 'where');
			if($position) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery. ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}

	/**
	 * Function to get relation query for particular module with function name
	 * @param <record> $recordId
	 * @param <String> $functionName
	 * @param nectarcrm_Module_Model $relatedModule
	 * @return <String>
	 */
	public function getRelationQuery($recordId, $functionName, $relatedModule, $relationId) {
		if ($functionName === 'get_activities') {
			$focus = CRMEntity::getInstance($this->getName());
			$focus->id = $recordId;
			$entityIds = $focus->getRelatedContactsIds();
			$entityIds = implode(',', $entityIds);

			$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

			$query = "SELECT CASE WHEN (nectarcrm_users.user_name not like '') THEN $userNameSql ELSE nectarcrm_groups.groupname END AS user_name,
						nectarcrm_crmentity.*, nectarcrm_activity.activitytype, nectarcrm_activity.subject, nectarcrm_activity.date_start, nectarcrm_activity.time_start,
						nectarcrm_activity.recurringtype, nectarcrm_activity.due_date, nectarcrm_activity.time_end, nectarcrm_activity.visibility, nectarcrm_seactivityrel.crmid AS parent_id,
						CASE WHEN (nectarcrm_activity.activitytype = 'Task') THEN (nectarcrm_activity.status) ELSE (nectarcrm_activity.eventstatus) END AS status
						FROM nectarcrm_activity
						INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
						LEFT JOIN nectarcrm_seactivityrel ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
						LEFT JOIN nectarcrm_cntactivityrel ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
						LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
						LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
							WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_activity.activitytype <> 'Emails'
								AND (nectarcrm_seactivityrel.crmid = ".$recordId;
			if($entityIds) {
				$query .= " OR nectarcrm_cntactivityrel.contactid IN (".$entityIds."))";
			} else {
				$query .= ")";
			}

			$relatedModuleName = $relatedModule->getName();
			$query .= $this->getSpecificRelationQuery($relatedModuleName);
			$nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
			if ($nonAdminQuery) {
				$query = appendFromClauseToQuery($query, $nonAdminQuery);

				if(trim($nonAdminQuery)) {
					$relModuleFocus = CRMEntity::getInstance($relatedModuleName);
					$condition = $relModuleFocus->buildWhereClauseConditionForCalendar();
					if($condition) {
						$query .= ' AND '.$condition;
					}
				}
			}

			// There could be more than one contact for an activity.
			$query .= ' GROUP BY nectarcrm_activity.activityid';
		} else {
			$query = parent::getRelationQuery($recordId, $functionName, $relatedModule, $relationId);
		}

		return $query;
	}

	/**
	 * Function returns the Calendar Events for the module
	 * @param <String> $mode - upcoming/overdue mode
	 * @param <nectarcrm_Paging_Model> $pagingModel - $pagingModel
	 * @param <String> $user - all/userid
	 * @param <String> $recordId - record id
	 * @return <Array>
	 */
	function getCalendarActivities($mode, $pagingModel, $user, $recordId = false) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		if (!$user) {
			$user = $currentUser->getId();
		}

		$nowInUserFormat = nectarcrm_Datetime_UIType::getDisplayDateTimeValue(date('Y-m-d H:i:s'));
		$nowInDBFormat = nectarcrm_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
		list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);

		$focus = CRMEntity::getInstance($this->getName());
		$focus->id = $recordId;
		$entityIds = $focus->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$query = "SELECT DISTINCT nectarcrm_crmentity.crmid, (CASE WHEN (crmentity2.crmid not like '') THEN crmentity2.crmid ELSE crmentity3.crmid END) AS parent_id, 
					(CASE WHEN (crmentity2.setype not like '') then crmentity2.setype ELSE crmentity3.setype END) AS crmentity2module, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.setype, nectarcrm_activity.* FROM nectarcrm_activity
					INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
					LEFT JOIN nectarcrm_seactivityrel ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
					LEFT JOIN nectarcrm_cntactivityrel ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
					LEFT JOIN nectarcrm_crmentity as crmentity2 on (nectarcrm_seactivityrel.crmid = crmentity2.crmid AND nectarcrm_seactivityrel.crmid IS NOT NULL AND crmentity2.deleted = 0)
					LEFT JOIN nectarcrm_crmentity as crmentity3 on (nectarcrm_cntactivityrel.contactid = crmentity3.crmid AND nectarcrm_cntactivityrel.contactid IS NOT NULL AND crmentity3.deleted = 0)
					LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";

		$query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Calendar');

		$query .= " WHERE nectarcrm_crmentity.deleted=0
					AND (nectarcrm_activity.activitytype NOT IN ('Emails'))
					AND (nectarcrm_activity.status is NULL OR nectarcrm_activity.status NOT IN ('Completed', 'Deferred', 'Cancelled'))
					AND (nectarcrm_activity.eventstatus is NULL OR nectarcrm_activity.eventstatus NOT IN ('Held', 'Cancelled'))";

		if(!$currentUser->isAdminUser()) {
			$moduleFocus = CRMEntity::getInstance('Calendar');
			$condition = $moduleFocus->buildWhereClauseConditionForCalendar();
			if($condition) {
				$query .= ' AND '.$condition;
			}
		}

		if ($mode === 'upcoming') {
			$query .= " AND CASE WHEN nectarcrm_activity.activitytype='Task' THEN due_date >= '$currentDate' ELSE CONCAT(due_date,' ',time_end) >= '$nowInDBFormat' END";
		} elseif ($mode === 'overdue') {
			$query .= " AND CASE WHEN nectarcrm_activity.activitytype='Task' THEN due_date < '$currentDate' ELSE CONCAT(due_date,' ',time_end) < '$nowInDBFormat' END";
		}

		$params = array();

		if ($recordId) {
			$query .= " AND (nectarcrm_seactivityrel.crmid = ?";
			array_push($params, $recordId);
			if ($entityIds) {
				$query .= " OR nectarcrm_cntactivityrel.contactid IN (" . $entityIds . "))";
			} else {
				$query .= ")";
			}
		}

		if ($user != 'all' && $user != '') {
			$query .= " AND nectarcrm_crmentity.smownerid = ?";
			array_push($params, $user);
		}

		$query .= " ORDER BY date_start, time_start LIMIT " . $pagingModel->getStartIndex() . ", " . ($pagingModel->getPageLimit() + 1);

		$result = $db->pquery($query, $params);
		$numOfRows = $db->num_rows($result);

		$groupsIds = nectarcrm_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
		$activities = array();
		$recordsToUnset = array();
		for ($i = 0; $i < $numOfRows; $i++) {
			$newRow = $db->query_result_rowdata($result, $i);
			$model = nectarcrm_Record_Model::getCleanInstance('Calendar');
			$ownerId = $newRow['smownerid'];
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$visibleFields = array('activitytype', 'date_start', 'time_start', 'due_date', 'time_end', 'assigned_user_id', 'visibility', 'smownerid', 'crmid');
			$visibility = true;
			if (in_array($ownerId, $groupsIds)) {
				$visibility = false;
			} else if ($ownerId == $currentUser->getId()) {
				$visibility = false;
			}
			if (!$currentUser->isAdminUser() && $newRow['activitytype'] != 'Task' && $newRow['visibility'] == 'Private' && $ownerId && $visibility) {
				foreach ($newRow as $data => $value) {
					if (in_array($data, $visibleFields) != -1) {
						unset($newRow[$data]);
					}
				}
				$newRow['subject'] = vtranslate('Busy', 'Events') . '*';
			}
			if ($newRow['activitytype'] == 'Task') {
				unset($newRow['visibility']);

				$due_date = $newRow["due_date"];
				$dayEndTime = "23:59:59";
				$EndDateTime = nectarcrm_Datetime_UIType::getDBDateTimeValue($due_date . " " . $dayEndTime);
				$dueDateTimeInDbFormat = explode(' ', $EndDateTime);
				$dueTimeInDbFormat = $dueDateTimeInDbFormat[1];
				$newRow['time_end'] = $dueTimeInDbFormat;
			}

			if ($newRow['crmentity2module'] == 'Contacts') {
				$newRow['contact_id'] = $newRow['parent_id'];
				unset($newRow['parent_id']);
			}
			$model->setData($newRow);
			$model->setId($newRow['crmid']);
			$activities[$newRow['crmid']] = $model;
			if (!$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($newRow['crmid']) == 'no') {
				$recordsToUnset[] = $newRow['crmid'];
			}
		}

		$pagingModel->calculatePageRange($activities);
		if ($numOfRows > $pagingModel->getPageLimit()) {
			array_pop($activities);
			$pagingModel->set('nextPageExists', true);
		} else {
			$pagingModel->set('nextPageExists', false);
		}
		//after setting paging model, unsetting the records which has no permissions
		foreach ($recordsToUnset as $record) {
			unset($activities[$record]);
		}
		return $activities;
	}
}
