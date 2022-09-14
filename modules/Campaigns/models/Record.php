<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Campaigns_Record_Model extends nectarcrm_Record_Model {

	/**
	 * Function to get selected ids list of related module for send email
	 * @param <String> $relatedModuleName
	 * @param <array> $excludedIds
	 * @return <array> List of selected ids
	 */
	public function getSelectedIdsList($relatedModuleName, $excludedIds = false) {
		$db = PearDatabase::getInstance();

		switch($relatedModuleName) {
			case "Leads"		: $tableName = "nectarcrm_campaignleadrel";		$fieldName = "leadid";		break;
			case "Accounts"		: $tableName = "nectarcrm_campaignaccountrel";		$fieldName = "accountid";	break;
			case 'Contacts'		: $tableName = "nectarcrm_campaigncontrel";		$fieldName = "contactid";	break;
		}

		$query = "SELECT $fieldName FROM $tableName
					INNER JOIN nectarcrm_crmentity ON $tableName.$fieldName = nectarcrm_crmentity.crmid AND nectarcrm_crmentity.deleted = ?
					WHERE campaignid = ?";
		if ($excludedIds) {
			$query .= " AND $fieldName NOT IN (". implode(',', $excludedIds) .")";
		}

		$result = $db->pquery($query, array(0, $this->getId()));
		$numOfRows = $db->num_rows($result);

		$selectedIdsList = array();
		for ($i=0; $i<$numOfRows; $i++) {
			$selectedIdsList[] = $db->query_result($result, $i, $fieldName);
		}
		return $selectedIdsList;
	}
}

