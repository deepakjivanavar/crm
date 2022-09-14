<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'include/events/VTEventHandler.inc';

class FieldEventHandler extends VTEventHandler {

	function handleEvent($eventName, $fieldEntity) {
		global $log, $adb;

		if ($eventName == 'nectarcrm.field.afterdelete') {
			$this->triggerPostDeleteEvents($fieldEntity);
		}
	}

	function triggerPostDeleteEvents($fieldEntity) {
		$db = PearDatabase::getInstance();

		$fieldId		= $fieldEntity->id;
		$fieldName		= $fieldEntity->name;
		$columnName		= $fieldEntity->column;
		$fieldLabel		= $fieldEntity->label;
		$tableName		= $fieldEntity->table;
		$typeOfData		= $fieldEntity->typeofdata;
		$fieldModuleName= $fieldEntity->getModuleName();
		$fieldType		= explode('~', $typeOfData);

		$deleteColumnName	= "$tableName:$columnName:" . $fieldName . ':' . $fieldModuleName . '_' . str_replace(' ', '_', $fieldLabel) . ':' . $fieldType[0];
		$columnCvStdFilter	= "$tableName:$columnName:" . $fieldName . ':' . $fieldModuleName . '_' . str_replace(' ', '_', $fieldLabel);
		$selectColumnName	= "$tableName:$columnName:" . $fieldModuleName . '_' . str_replace(' ', '_', $fieldLabel) . ':' . $fieldName . ':' . $fieldType[0];
		$reportSummaryColumn= "$tableName:$columnName:" . str_replace(' ', '_', $fieldLabel);

		$query = 'ALTER TABLE ' . $db->sql_escape_string($tableName) . ' DROP COLUMN ' . $db->sql_escape_string($columnName);
		$db->pquery($query, array());

		//we have to remove the entries in customview and report related tables which have this field ($colName)
		$db->pquery('DELETE FROM nectarcrm_cvcolumnlist WHERE columnname = ?', array($deleteColumnName));
		$db->pquery('DELETE FROM nectarcrm_cvstdfilter WHERE columnname = ?', array($columnCvStdFilter));
		$db->pquery('DELETE FROM nectarcrm_cvadvfilter WHERE columnname = ?', array($deleteColumnName));
		$db->pquery('DELETE FROM nectarcrm_selectcolumn WHERE columnname = ?', array($selectColumnName));
		$db->pquery('DELETE FROM nectarcrm_relcriteria WHERE columnname = ?', array($selectColumnName));
		$db->pquery('DELETE FROM nectarcrm_reportsortcol WHERE columnname = ?', array($selectColumnName));
		$db->pquery('DELETE FROM nectarcrm_reportsummary WHERE columnname LIKE ?', array('%' . $reportSummaryColumn . '%'));
		$db->pquery('DELETE FROM nectarcrm_reportdatefilter WHERE datecolumnname = ?', array($columnCvStdFilter));

		if ($fieldModuleName == 'Leads') {
			$db->pquery('DELETE FROM nectarcrm_convertleadmapping WHERE leadfid=?', array($fieldId));
		} elseif ($fieldModuleName == 'Accounts' || $fieldModuleName == 'Contacts' || $fieldModuleName == 'Potentials') {
			$params = array('Accounts' => 'accountfid', 'Contacts' => 'contactfid', 'Potentials' => 'potentialfid');
			$query = 'UPDATE nectarcrm_convertleadmapping SET ' . $params[$fieldModuleName] . '=0 WHERE ' . $params[$fieldModuleName] . '=?';
			$db->pquery($query, array($fieldId));
		}

		if (in_array($fieldEntity->uitype, array(15, 33))) {
			$db->pquery('DROP TABLE IF EXISTS nectarcrm_' . $db->sql_escape_string($columnName), array());
			$db->pquery('DROP TABLE IF EXISTS nectarcrm_' . $db->sql_escape_string($columnName) . '_seq', array()); //To Delete Sequence Table  
			$db->pquery('DELETE FROM nectarcrm_picklist_dependency WHERE sourcefield=? OR targetfield=?', array($columnName, $columnName));

            //delete from picklist tables
            $picklistResult = $db->pquery('SELECT picklistid FROM nectarcrm_picklist WHERE name = ?', array($fieldName));
            $picklistRow = $db->num_rows($picklistResult);
            if($picklistRow) {
                $picklistId = $db->query_result($picklistResult, 0, 'picklistid');
                $db->pquery('DELETE FROM nectarcrm_picklist WHERE name = ?', array($fieldName));
                $db->pquery('DELETE FROM nectarcrm_role2picklist WHERE picklistid = ?', array($picklistId));
            }

			$rolesList = array_keys(getAllRoleDetails());
			nectarcrm_Cache::flushPicklistCache($fieldName, $rolesList);
		}

		$this->triggerInventoryFieldPostDeleteEvents($fieldEntity);
	}

	public function triggerInventoryFieldPostDeleteEvents($fieldEntity) {
		$db = PearDatabase::getInstance();
		$fieldId = $fieldEntity->id;
		$fieldModuleName = $fieldEntity->getModuleName();

		if (in_array($fieldModuleName, getInventoryModules())) {

			$db->pquery('DELETE FROM nectarcrm_inventorycustomfield WHERE fieldid=?', array($fieldId));

		} else if (in_array($fieldModuleName, array('Products', 'Services'))) {

			$refFieldName			= ($fieldModuleName == 'Products') ? 'productfieldid'			: 'servicefieldid';
			$refFieldDefaultValue	= ($fieldModuleName == 'Products') ? 'productFieldDefaultValue' : 'serviceFieldDefaultValue';

			$query = "SELECT nectarcrm_inventorycustomfield.* FROM nectarcrm_inventorycustomfield
							INNER JOIN nectarcrm_field ON nectarcrm_field.fieldid = nectarcrm_inventorycustomfield.fieldid
							WHERE $refFieldName = ? AND defaultvalue LIKE ?";
			$result = $db->pquery($query, array($fieldId, '%productFieldDefaultValue%serviceFieldDefaultValue%'));

			$removeCacheModules = array();
			while($rowData = $db->fetch_row($result)) {
				$lineItemFieldModel = nectarcrm_Field_Model::getInstance($rowData['fieldid']);
				if ($lineItemFieldModel) {
					$defaultValue = $lineItemFieldModel->getDefaultFieldValue();
					if (is_array($defaultValue)) {
						$defaultValue[$refFieldDefaultValue] = '';

						if ($defaultValue['productFieldDefaultValue'] === '' && $defaultValue['serviceFieldDefaultValue'] === '') {
							$defaultValue = '';
						} else {
							$defaultValue = Zend_Json::encode($defaultValue);
						}

						$lineItemFieldModel->set('defaultvalue', $defaultValue);
						$lineItemFieldModel->save();
					}

					$removeCacheModules[$rowData['tabid']][] = $lineItemFieldModel->get('block')->id;
				}
			}

			foreach ($removeCacheModules as $tabId => $blockIdsList) {
				$moduleModel = nectarcrm_Module_Model::getInstance($tabId);
				foreach ($blockIdsList as $blockId) {
					nectarcrm_Cache::flushModuleandBlockFieldsCache($moduleModel, $blockId);
				}
			}

			$db->pquery("UPDATE nectarcrm_inventorycustomfield SET $refFieldName=? WHERE fieldid=?", array('0', $fieldId));
		}

	}
}
