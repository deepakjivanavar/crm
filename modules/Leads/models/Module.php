<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_Module_Model extends nectarcrm_Module_Model {
	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of nectarcrm_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$links = parent::getSideBarLinks($linkParams);

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
			$links['SIDEBARLINK'][] = nectarcrm_Link_Model::getInstanceFromValues($quickLink);
		}
		
		return $links;
	}

    /**
	 * Function returns Settings Links
	 * @return Array
	 */
	public function getSettingLinks() {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$settingLinks = parent::getSettingLinks();
		
		if($currentUserModel->isAdminUser()) {
			$settingLinks[] = array(
					'linktype' => 'LISTVIEWSETTING',
					'linklabel' => 'LBL_CUSTOM_FIELD_MAPPING',
					'linkurl' => 'index.php?parent=Settings&module=Leads&view=MappingDetail',
					'linkicon' => '');
			
		}
		return $settingLinks;
	}

    /**
    * Function returns deleted records condition
    */
    public function getDeletedRecordCondition() {
       return 'nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.converted = 0';
    }

    /**
	 * Function to get the list of recently visisted records
	 * @param <Number> $limit
	 * @return <Array> - List of nectarcrm_Record_Model or Module Specific Record Model instances
	 */
	public function getRecentRecords($limit=10) {
		$db = PearDatabase::getInstance();

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $deletedCondition = $this->getDeletedRecordCondition();
		$query = 'SELECT * FROM nectarcrm_crmentity '.
            ' INNER JOIN nectarcrm_leaddetails ON
                nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
                WHERE setype=? AND '.$deletedCondition.' AND modifiedby = ? ORDER BY modifiedtime DESC LIMIT ?';
		$params = array($this->get('name'), $currentUserModel->id, $limit);
		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$recentRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$row['id'] = $row['crmid'];
			$recentRecords[$row['id']] = $this->getRecordFromArray($row);
		}
		return $recentRecords;
	}

	/**
	 * Function returns the Number of Leads created per week
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsCreated($owner, $dateFilter) {
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

		$result = $db->pquery('SELECT COUNT(*) AS count, date(createdtime) AS time FROM nectarcrm_leaddetails
						INNER JOIN nectarcrm_crmentity ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
						AND deleted=0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).$ownerSql.' '.$dateFilterSql.' AND converted = 0 GROUP BY week(createdtime)',
					$params);

		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$response[$i][1] = $row['time'];
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Status
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsByStatus($owner,$dateFilter) {
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
        if(vtws_isRoleBasedPicklist('leadstatus')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("leadstatus",$currentUserModel->getRole(), $db);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }

		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN nectarcrm_leadstatus.leadstatus IS NULL OR nectarcrm_leadstatus.leadstatus = "" THEN "" ELSE 
						nectarcrm_leadstatus.leadstatus END AS leadstatusvalue FROM nectarcrm_leaddetails 
						INNER JOIN nectarcrm_crmentity ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.
						'INNER JOIN nectarcrm_leadstatus ON nectarcrm_leaddetails.leadstatus = nectarcrm_leadstatus.leadstatus 
                        WHERE nectarcrm_leaddetails.leadstatus IN ('.generateQuestionMarks($picklistvaluesmap).') 
						GROUP BY leadstatusvalue ORDER BY nectarcrm_leadstatus.sortorderid', $params);

		$response = array();
		
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$leadStatusVal = $row['leadstatusvalue'];
			if($leadStatusVal == '') {
				$leadStatusVal = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($leadStatusVal, $this->getName());
			$response[$i][2] = $leadStatusVal;
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Source
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsBySource($owner,$dateFilter) {
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
        if(vtws_isRoleBasedPicklist('leadsource')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("leadsource",$currentUserModel->getRole(), $db);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }
        
		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN nectarcrm_leaddetails.leadsource IS NULL OR nectarcrm_leaddetails.leadsource = "" THEN "" 
						ELSE nectarcrm_leaddetails.leadsource END AS leadsourcevalue FROM nectarcrm_leaddetails 
						INNER JOIN nectarcrm_crmentity ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.
						'INNER JOIN nectarcrm_leadsource ON nectarcrm_leaddetails.leadsource = nectarcrm_leadsource.leadsource 
                        WHERE nectarcrm_leaddetails.leadsource IN ('.generateQuestionMarks($picklistvaluesmap).') 
						GROUP BY leadsourcevalue ORDER BY nectarcrm_leadsource.sortorderid', $params);
		
		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$leadSourceVal =  $row['leadsourcevalue'];
			if($leadSourceVal == '') {
				$leadSourceVal = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($leadSourceVal, $this->getName());
			$response[$i][2] = $leadSourceVal;
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Industry
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsByIndustry($owner,$dateFilter) {
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
        if(vtws_isRoleBasedPicklist('industry')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("industry",$currentUserModel->getRole(), $db);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }
		
		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN nectarcrm_leaddetails.industry IS NULL OR nectarcrm_leaddetails.industry = "" THEN "" 
						ELSE nectarcrm_leaddetails.industry END AS industryvalue FROM nectarcrm_leaddetails 
						INNER JOIN nectarcrm_crmentity ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.'
						INNER JOIN nectarcrm_industry ON nectarcrm_leaddetails.industry = nectarcrm_industry.industry 
                        WHERE nectarcrm_leaddetails.industry IN ('.generateQuestionMarks($picklistvaluesmap).') 
						GROUP BY industryvalue ORDER BY nectarcrm_industry.sortorderid', $params);
		
		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$industyValue = $row['industryvalue'];
			if($industyValue == '') {
				$industyValue = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($industyValue, $this->getName());
			$response[$i][2] = $industyValue;
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
	 * Function to get Converted Information for selected records
	 * @param <array> $recordIdsList
	 * @return <array> converted Info
	 */
	public static function getConvertedInfo($recordIdsList = array()) {
		$convertedInfo = array();
		if ($recordIdsList) {
			$db = PearDatabase::getInstance();
			$result = $db->pquery("SELECT converted FROM nectarcrm_leaddetails WHERE leadid IN (".generateQuestionMarks($recordIdsList).")", $recordIdsList);
			$numOfRows = $db->num_rows($result);

			for ($i=0; $i<$numOfRows; $i++) {
				$convertedInfo[$recordIdsList[$i]] = $db->query_result($result, $i, 'converted');
			}
		}
		return $convertedInfo;
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
		if (in_array($sourceModule, array('Campaigns', 'Products', 'Services', 'Emails'))) {
			switch ($sourceModule) {
				case 'Campaigns'	: $tableName = 'nectarcrm_campaignleadrel';	$fieldName = 'leadid';	$relatedFieldName ='campaignid';	break;
				case 'Products'		: $tableName = 'nectarcrm_seproductsrel';		$fieldName = 'crmid';		$relatedFieldName ='productid';		break;
			}

			if ($sourceModule === 'Services') {
				$condition = " nectarcrm_leaddetails.leadid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";
			} elseif ($sourceModule === 'Emails') {
				$condition = ' nectarcrm_leaddetails.emailoptout = 0';
			} else {
				$condition = " nectarcrm_leaddetails.leadid NOT IN (SELECT $fieldName FROM $tableName WHERE $relatedFieldName = '$record')";
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

	public function getDefaultSearchField(){
		return "lastname";
	}

	/*
	 * Function to get supported utility actions for a module
	 */
	public function getUtilityActionsNames() {
		return array('Import', 'Export', 'Merge', 'ConvertLead', 'DuplicatesHandling');
	}
}