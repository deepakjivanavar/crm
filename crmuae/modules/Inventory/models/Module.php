<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
/**
 * Inventory Module Model Class
 */
class Inventory_Module_Model extends nectarcrm_Module_Model {

	/**
	 * Function to check whether the module is an entity type module or not
	 * @return <Boolean> true/false
	 */
	public function isQuickCreateSupported(){
		//SalesOrder module is not enabled for quick create
		return false;
	}
	
	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return true;
	}

	public function isCommentEnabled() {
		return true;
	}

	static function getAllCurrencies() {
		return getAllCurrencies();
	}

	static function getAllProductTaxes() {
		$taxes = array();
		$availbleTaxes = getAllTaxes('available');
		foreach ($availbleTaxes as $taxInfo) {
			if ($taxInfo['method'] === 'Deducted') {
				continue;
			}
			$taxInfo['compoundon'] = Zend_Json::decode(html_entity_decode($taxInfo['compoundon']));
			$taxInfo['regions'] = Zend_Json::decode(html_entity_decode($taxInfo['regions']));
			$taxes[$taxInfo['taxid']] = $taxInfo;
		}
		return $taxes;
	}

	static function getAllShippingTaxes() {
		return Inventory_Charges_Model::getChargeTaxesList();
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
	 * Function returns export query
	 * @param <String> $where
	 * @return <String> export query
	 */
	public function getExportQuery($focus, $query) {
		$baseTableName = $focus->table_name;
		$splitQuery = preg_split('/ FROM /i', $query);
		$columnFields = explode(',', $splitQuery[0]);
		foreach ($columnFields as $key => &$value) {
			if($value == ' nectarcrm_inventoryproductrel.discount_amount'){
				$value = ' nectarcrm_inventoryproductrel.discount_amount AS item_discount_amount';
			} else if($value == ' nectarcrm_inventoryproductrel.discount_percent'){
				$value = ' nectarcrm_inventoryproductrel.discount_percent AS item_discount_percent';
			} else if($value == " $baseTableName.currency_id"){
				$value = ' nectarcrm_currency_info.currency_name AS currency_id';
			}
		}
		$joinSplit = preg_split('/ WHERE /i',$splitQuery[1]);
		$joinSplit[0] .= " LEFT JOIN nectarcrm_currency_info ON nectarcrm_currency_info.id = $baseTableName.currency_id";
		$splitQuery[1] = $joinSplit[0] . ' WHERE ' .$joinSplit[1];

		$query = implode(',', $columnFields).' FROM ' . $splitQuery[1];
		
		return $query;
	}

	/*
	 * Function to get supported utility actions for a module
	 */
	function getUtilityActionsNames() {
		return array('Import', 'Export');
	}
}
