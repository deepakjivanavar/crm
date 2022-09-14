<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ************************************************************************************/

class Potentials_Module_Model extends nectarcrm_Module_Model {

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
	 * Function returns number of Open Potentials in each of the sales stage
	 * @param <Integer> $owner - userid
	 * @return <Array>
	 */
	public function getPotentialsCountBySalesStage($owner, $dateFilter) {
		$db = PearDatabase::getInstance();

		if (!$owner) {
			$currenUserModel = Users_Record_Model::getCurrentUserModel();
			$owner = $currenUserModel->getId();
		} else if ($owner === 'all') {
			$owner = '';
		}

		$params = array();
		if(!empty($owner)) {
			$ownerSql =  ' AND smownerid = ? ';
			$params[] = $owner;
		}
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND closingdate BETWEEN ? AND ? ';
			$params[] = $dateFilter['start'];
			$params[] = $dateFilter['end'];
		}
        if(vtws_isRoleBasedPicklist('sales_stage')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("sales_stage",$currentUserModel->getRole(), $db);
            unset($picklistvaluesmap['Closed Won']);unset($picklistvaluesmap['Closed Lost']);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }
        
		$result = $db->pquery('SELECT COUNT(*) count, nectarcrm_potential.sales_stage FROM nectarcrm_potential
						INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
						INNER JOIN nectarcrm_sales_stage ON nectarcrm_potential.sales_stage = nectarcrm_sales_stage.sales_stage 
                        AND deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql . $dateFilterSql . ' AND nectarcrm_potential.sales_stage IN ('.  generateQuestionMarks($picklistvaluesmap).') 
					    GROUP BY sales_stage ORDER BY nectarcrm_sales_stage.sortorderid', $params);
		
		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
            // Dashboard showing UTF8 characters as encoded values
			$saleStage = decode_html($db->query_result($result, $i, 'sales_stage'));
			$response[$i][0] = vtranslate($saleStage, $this->getName());
			$response[$i][1] = $db->query_result($result, $i, 'count');
			$response[$i][2] = vtranslate($saleStage, $this->getName());
            $response[$i]['link'] = $saleStage;
		}
		return $response;
	}

	/**
	 * Function returns number of Open Potentials for each of the sales person
	 * @param <Integer> $owner - userid
	 * @return <Array>
	 */
	public function getPotentialsCountBySalesPerson() {
		$db = PearDatabase::getInstance();
		//TODO need to handle security
		$params = array();
        if(vtws_isRoleBasedPicklist('sales_stage')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("sales_stage",$currentUserModel->getRole(), $db);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }
		$result = $db->pquery('SELECT COUNT(*) AS count, concat(first_name," ",last_name) as last_name, nectarcrm_potential.sales_stage, nectarcrm_groups.groupname FROM nectarcrm_potential
						INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid AND nectarcrm_crmentity.deleted = 0
						LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid AND nectarcrm_users.status="ACTIVE"
						LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid'.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).'
						INNER JOIN nectarcrm_sales_stage ON nectarcrm_potential.sales_stage =  nectarcrm_sales_stage.sales_stage 
                        WHERE nectarcrm_potential.sales_stage IN ('.  generateQuestionMarks($picklistvaluesmap).') GROUP BY smownerid, sales_stage ORDER BY nectarcrm_sales_stage.sortorderid', $params);

		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$lastName = decode_html($row['last_name']);
			if(!$lastName) {
				$lastName = decode_html($row['groupname']);
			}
            $response[$i]['count'] = $row['count'];
            $response[$i]['last_name'] = $lastName;
            $response[$i]['link'] = decode_html($row['sales_stage']);
            $response[$i]['sales_stage'] = vtranslate(decode_html($row['sales_stage']),  $this->getName());
            //$response[$i][2] = $row['']
        }
		return $response;
	}

	/**
	 * Function returns Potentials Amount for each Sales Person
	 * @return <Array>
	 */
	function getPotentialsPipelinedAmountPerSalesPerson() {
		$db = PearDatabase::getInstance();
		//TODO need to handle security
		$params = array();
        if(vtws_isRoleBasedPicklist('sales_stage')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistvaluesmap = getAssignedPicklistValues("sales_stage",$currentUserModel->getRole(), $db);
            unset($picklistvaluesmap['Closed Won']);unset($picklistvaluesmap['Closed Lost']);
            foreach($picklistvaluesmap as $picklistValue) $params[] = $picklistValue;
        }
		$result = $db->pquery('SELECT sum(amount) AS amount, concat(first_name," ",last_name) as last_name, nectarcrm_potential.sales_stage FROM nectarcrm_potential
						INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
						INNER JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid AND nectarcrm_users.status="ACTIVE"
						AND nectarcrm_crmentity.deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).
						'INNER JOIN nectarcrm_sales_stage ON nectarcrm_potential.sales_stage =  nectarcrm_sales_stage.sales_stage 
						WHERE nectarcrm_potential.sales_stage IN ('.generateQuestionMarks($picklistvaluesmap).') 
						GROUP BY smownerid, sales_stage ORDER BY nectarcrm_sales_stage.sortorderid', $params);
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
            $row['link'] = decode_html($row['sales_stage']);
			$row['amount'] = CurrencyField::convertToUserFormat($row['amount'], null, false, true);
            $row['last_name'] = decode_html($row['last_name']);
            $row['sales_stage'] = vtranslate(decode_html($row['sales_stage']),  $this->getName());
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Function returns Total Revenue for each Sales Person
	 * @return <Array>
	 */
	function getTotalRevenuePerSalesPerson($dateFilter) {
		$db = PearDatabase::getInstance();
		//TODO need to handle security
		$params = array();
		$params[] = 'Closed Won';
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//appended time frame and converted to db time zone in showwidget.php
			$params[] = $dateFilter['start'];
			$params[] = $dateFilter['end'];
		}
		
		$result = $db->pquery('SELECT sum(amount) amount, concat(first_name," ",last_name) as last_name,nectarcrm_users.id as id,DATE_FORMAT(closingdate, "%d-%m-%Y") AS closingdate  FROM nectarcrm_potential
						INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
						INNER JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid AND nectarcrm_users.status="ACTIVE"
						AND nectarcrm_crmentity.deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).'WHERE sales_stage = ? '.' '.$dateFilterSql.' GROUP BY smownerid', $params);
		$data = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$row['amount'] = CurrencyField::convertToUserFormat($row['amount'], null, false, true);
                        $row['last_name'] = decode_html($row['last_name']);
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Function returns Top Potentials
	 * @return <Array of nectarcrm_Record_Model>
	 */
	function getTopPotentials($pagingModel) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();
		$query = "SELECT crmid, amount, potentialname, related_to FROM nectarcrm_potential
						INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
							AND deleted = 0 ".Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName())."
						WHERE sales_stage NOT IN ('Closed Won', 'Closed Lost') AND amount > 0
						ORDER BY amount DESC LIMIT ".$pagingModel->getStartIndex().", ".$pagingModel->getPageLimit()."";
		$result = $db->pquery($query, array());

		$models = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$modelInstance = nectarcrm_Record_Model::getCleanInstance('Potentials');
			$modelInstance->setId($db->query_result($result, $i, 'crmid'));
			$modelInstance->set('amount', $db->query_result($result, $i, 'amount'));
			$modelInstance->set('potentialname', $db->query_result($result, $i, 'potentialname'));
			$modelInstance->set('related_to', $db->query_result($result, $i, 'related_to'));
			$models[] = $modelInstance;
		}
		return $models;
	}

	/**
	 * Function returns Potentials Forecast Amount
	 * @return <Array>
	 */
	function getForecast($closingdateFilter,$dateFilter) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		$params = array();
		$params[] = $currentUser->getId();
		if(!empty($closingdateFilter)) {
			$closingdateFilterSql = ' AND closingdate BETWEEN ? AND ? ';
			$params[] = $closingdateFilter['start'];
			$params[] = $closingdateFilter['end'];
		}
		
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start'];
			$params[] = $dateFilter['end'];
		}
		
		$result = $db->pquery('SELECT forecast_amount, DATE_FORMAT(closingdate, "%m-%d-%Y") AS closingdate FROM nectarcrm_potential
					INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
					AND deleted = 0 AND smownerid = ? WHERE closingdate >= CURDATE() AND sales_stage NOT IN ("Closed Won", "Closed Lost")'.
					' '.$closingdateFilterSql.$dateFilterSql,
					$params);

		$forecast = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$forecast[] = $row;
		}
		return $forecast;

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
	 * Function returns Potentials Amount for each Sales Stage
	 * @return <Array>
	 */
	function getPotentialTotalAmountBySalesStage() {
		//$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

        if(vtws_isRoleBasedPicklist('sales_stage')) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $picklistValues = getAssignedPicklistValues("sales_stage",$currentUserModel->getRole(), $db);
        }
		$data = array();
		foreach ($picklistValues as $key => $picklistValue) {
			$result = $db->pquery('SELECT SUM(amount) AS amount FROM nectarcrm_potential
								   INNER JOIN nectarcrm_crmentity ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
								   AND deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).' WHERE sales_stage = ?', array($picklistValue));
			$num_rows = $db->num_rows($result);
			for($i=0; $i<$num_rows; $i++) {
				$values = array();
				$amount = $db->query_result($result, $i, 'amount');
				if(!empty($amount)){
					$values[0] = CurrencyField::convertToUserFormat($db->query_result($result, $i, 'amount'), null, false, true);
					$values[1] = vtranslate($picklistValue, $this->getName());
                    $values['link'] = $picklistValue;
					$data[] = $values;
				}
				
			}
		}
		return $data;
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
		if (in_array($sourceModule, array('Products', 'Services'))) {
			if ($sourceModule === 'Products') {
				$condition = " nectarcrm_potential.potentialid NOT IN (SELECT crmid FROM nectarcrm_seproductsrel WHERE productid = '$record')";
			} elseif ($sourceModule === 'Services') {
				$condition = " nectarcrm_potential.potentialid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";
			}

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
	 * Function returns query for module record's search
	 * @param <String> $searchValue - part of record name (label column of crmentity table)
	 * @param <Integer> $parentId - parent record id
	 * @param <String> $parentModule - parent module name
	 * @return <String> - query
	 */
	public function getSearchRecordsQuery($searchValue,$searchFields, $parentId=false, $parentModule=false) {
		if($parentId && in_array($parentModule, array('Accounts', 'Contacts'))) {
			$query = "SELECT ".implode(',',$searchFields)." FROM nectarcrm_crmentity
						INNER JOIN nectarcrm_potential ON nectarcrm_potential.potentialid = nectarcrm_crmentity.crmid
						WHERE deleted = 0 AND nectarcrm_potential.related_to = $parentId AND label like '%$searchValue%'";
			return $query;
		}
		return parent::getSearchRecordsQuery($parentId, $parentModule);
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
					'linkurl' => 'index.php?parent=Settings&module=Potentials&view=MappingDetail',
					'linkicon' => '');
			
		}
		return $settingLinks;
	}
    
    /*
     * Function to get supported utility actions for a module
     */
    function getUtilityActionsNames() {
        return array('Import', 'Export', 'DuplicatesHandling');
    }
}