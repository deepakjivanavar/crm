<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'vtlib/nectarcrm/Utils.php';

class iCalLastImport {

	var $tableName = 'nectarcrm_ical_import';
	var $fields = array('id', 'userid', 'entitytype', 'crmid');
	var $fieldData = array();
	
	function  __construct() {
	}

	function clearRecords($userId) {
		$adb = PearDatabase::getInstance();
		if(nectarcrm_Utils::CheckTable($this->tableName)) {
			$adb->pquery('DELETE FROM '.$this->tableName .' WHERE userid = ?', array($userId));
		}
	}

	function setFields($data) {
		if(!empty($data)) {
			foreach($data as $name => $value) {
				$this->fieldData[$name] = $value;
			}
		}
	}

	function save() {
		$adb = PearDatabase::getInstance();

		if(count($this->fieldData) == 0) return;
		
		if(!nectarcrm_Utils::CheckTable($this->tableName)) {
			nectarcrm_Utils::CreateTable(
				$this->tableName,
				"(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					userid INT NOT NULL,
					entitytype VARCHAR(200) NOT NULL,
					crmid INT NOT NULL)",
				true);
		}

		$fieldNames = array_keys($this->fieldData);
		$fieldValues = array_values($this->fieldData);
		$adb->pquery('INSERT INTO '.$this->tableName.'('. implode(',',$fieldNames) .') VALUES ('. generateQuestionMarks($fieldValues) .')',
				array($fieldValues));
	}

	function undo($moduleName, $userId) {
		$adb = PearDatabase::getInstance();
		if(nectarcrm_Utils::CheckTable($this->tableName)) {
			$result = $adb->pquery('UPDATE nectarcrm_crmentity SET deleted=1 WHERE crmid IN
								(SELECT crmid FROM '.$this->tableName .' WHERE userid = ? AND entitytype = ?)',
						array($userId, $moduleName));
			return $adb->getAffectedRowCount($result);
		}
	}
}
?>
