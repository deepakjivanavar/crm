<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_CronTasks_Module_Model extends Settings_nectarcrm_Module_Model {

	var $baseTable = 'nectarcrm_cron_task';
	var $baseIndex = 'id';
	var $listFields = array('sequence' => 'Sequence', 'name' => 'Cron Job', 'frequency' => 'Frequency(H:M)', 'status' => 'Status', 'laststart' => 'Last Start', 'lastend' => 'Last End');
	var $nameFields = array('');
	var $name = 'CronTasks';

	/**
	 * Function to get editable fields from this module
	 * @return <Array> List of fieldNames
	 */
	public function getEditableFieldsList() {
		return array('frequency', 'status');
	}

	/**
	 * Function to update sequence of several records
	 * @param <Array> $sequencesList
	 */
	public function updateSequence($sequencesList) {
		$db = PearDatabase::getInstance();

		$updateQuery = "UPDATE nectarcrm_cron_task SET sequence = CASE";

		foreach ($sequencesList as $sequence => $recordId) {
			$updateQuery .= " WHEN id = $recordId THEN $sequence ";
		}
		$updateQuery .= " END";
		$db->pquery($updateQuery, array());
	}
	
	public function hasCreatePermissions() {
		return false;
	}
	
	public function isPagingSupported() {
		return false;
	}

}
