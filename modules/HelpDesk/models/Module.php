<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ************************************************************************************/

class HelpDesk_Module_Model extends nectarcrm_Module_Model {

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
	 * Function to get Settings links for admin user
	 * @return Array
	 */
	public function getSettingLinks() {
		$settingsLinks = parent::getSettingLinks();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if ($currentUserModel->isAdminUser()) {
			$settingsLinks[] = array(
				'linktype' => 'LISTVIEWSETTING',
				'linklabel' => 'LBL_EDIT_MAILSCANNER',
				'linkurl' =>'index.php?parent=Settings&module=MailConverter&view=List',
				'linkicon' => ''
			);
		}
		return $settingsLinks;
	}


	/**
	 * Function returns Tickets grouped by Status
	 * @param type $data
	 * @return <Array>
	 */
	public function getOpenTickets() {
		$db = PearDatabase::getInstance();
		//TODO need to handle security
		$params = array();
		if(vtws_isRoleBasedPicklist('ticketstatus')) {
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$picklistvaluesmap = getAssignedPicklistValues("ticketstatus",$currentUserModel->getRole(), $db);
			if(in_array('Open', $picklistvaluesmap)) $params[] = 'Open';
		}
		if(count($params) > 0) {
		$result = $db->pquery('SELECT count(*) AS count, COALESCE(nectarcrm_groups.groupname,concat(nectarcrm_users.first_name, " " ,nectarcrm_users.last_name)) as name, COALESCE(nectarcrm_groups.groupid,nectarcrm_users.id) as id  FROM nectarcrm_troubletickets
						INNER JOIN nectarcrm_crmentity ON nectarcrm_troubletickets.ticketid = nectarcrm_crmentity.crmid
						LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid AND nectarcrm_users.status="ACTIVE"
						LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
						'.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).
						' WHERE nectarcrm_troubletickets.status = ? AND nectarcrm_crmentity.deleted = 0 GROUP BY smownerid', $params);
		}
		$data = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
						$row['name'] = decode_html($row['name']);
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Function returns Tickets grouped by Status
	 * @param type $data
	 * @return <Array>
	 */
	public function getTicketsByStatus($owner, $dateFilter) {
		$db = PearDatabase::getInstance();

		$ownerSql = $this->getOwnerWhereConditionForDashBoards($owner);
		if(!empty($ownerSql)) {
			$ownerSql = ' AND '.$ownerSql;
		}

		$params = array();
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//appended time frame and converted to db time zone in showwidget.php
			$params[] = $dateFilter['start'];
			$params[] = $dateFilter['end'];
		}
		if(vtws_isRoleBasedPicklist('ticketstatus')) {
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$picklistvaluesmap = getAssignedPicklistValues("ticketstatus",$currentUserModel->getRole(), $db);
			foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
		}

		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN nectarcrm_troubletickets.status IS NULL OR nectarcrm_troubletickets.status = "" THEN "" ELSE nectarcrm_troubletickets.status END AS statusvalue 
							FROM nectarcrm_troubletickets INNER JOIN nectarcrm_crmentity ON nectarcrm_troubletickets.ticketid = nectarcrm_crmentity.crmid AND nectarcrm_crmentity.deleted=0
							'.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.
							' INNER JOIN nectarcrm_ticketstatus ON nectarcrm_troubletickets.status = nectarcrm_ticketstatus.ticketstatus 
							WHERE nectarcrm_troubletickets.status IN ('.generateQuestionMarks($picklistvaluesmap).') 
							GROUP BY statusvalue ORDER BY nectarcrm_ticketstatus.sortorderid', $params);

		$response = array();

		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$ticketStatusVal = $row['statusvalue'];
			if($ticketStatusVal == '') {
				$ticketStatusVal = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($ticketStatusVal, $this->getName());
			$response[$i][2] = $ticketStatusVal;
		}
		return $response;
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
								AND nectarcrm_seactivityrel.crmid = ".$recordId;

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
		} else {
			$query = parent::getRelationQuery($recordId, $functionName, $relatedModule, $relationId);
		}

		return $query;
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
		if (in_array($sourceModule, array('Assets', 'Project', 'ServiceContracts', 'Services'))) {
			$condition = " nectarcrm_troubletickets.ticketid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";
			$pos = stripos($listQuery, 'where');

			if ($pos) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery . ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}

	 /**
	 * Function to get list of field for header view
	 * @return <Array> list of field models <nectarcrm_Field_Model>
	 */
	function getConfigureRelatedListFields(){
		$summaryViewFields = $this->getSummaryViewFieldsList();
		$headerViewFields = $this->getHeaderViewFieldsList();
		$allRelationListViewFields = array_merge($headerViewFields,$summaryViewFields);
		$relatedListFields = array();
		if(count($allRelationListViewFields) > 0) {
			foreach ($allRelationListViewFields as $key => $field) {
				$relatedListFields[$field->get('column')] = $field->get('name');
			}
		}

		if(count($relatedListFields)>0) {
			$nameFields = $this->getNameFields();
			foreach($nameFields as $fieldName){
				if(!$relatedListFields[$fieldName]) {
					$fieldModel = $this->getField($fieldName);
					$relatedListFields[$fieldModel->get('column')] = $fieldModel->get('name');
				}
			}
		}

		return $relatedListFields;
	}
}
