<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_Record_Model extends nectarcrm_Record_Model {

	/**
	 * Function returns the url for converting lead
	 */
	function getConvertLeadUrl() {
		return 'index.php?module='.$this->getModuleName().'&view=ConvertLead&record='.$this->getId();
	}

	/**
	 * Static Function to get the list of records matching the search key
	 * @param <String> $searchKey
	 * @return <Array> - List of nectarcrm_Record_Model or Module Specific Record Model instances
	 */
	public static function getSearchResult($searchKey, $module=false) {
		$db = PearDatabase::getInstance();

		$deletedCondition = $this->getModule()->getDeletedRecordCondition();
		$query = 'SELECT * FROM nectarcrm_crmentity
                    INNER JOIN nectarcrm_leaddetails ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid
                    WHERE label LIKE ? AND '.$deletedCondition;
		$params = array("%$searchKey%");
		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$moduleModels = array();
		$matchingRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$row['id'] = $row['crmid'];
			$moduleName = $row['setype'];
			if(!array_key_exists($moduleName, $moduleModels)) {
				$moduleModels[$moduleName] = nectarcrm_Module_Model::getInstance($moduleName);
			}
			$moduleModel = $moduleModels[$moduleName];
			$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'Record', $moduleName);
			$recordInstance = new $modelClassName();
			$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
		}
		return $matchingRecords;
	}

	/**
	 * Function returns Account fields for Lead Convert
	 * @return Array
	 */
	function getAccountFieldsForLeadConvert() {
		$accountsFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Accounts';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'CreateView')) {
			return;
		}

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();
            //Fields that need to be shown
            $complusoryFields = array('industry');
			foreach ($fieldModels as $fieldName => $fieldModel) {
				if($fieldModel->isMandatory() && $fieldName != 'assigned_user_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }
					$leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    if($this->get($leadMappedField)) {
                        $fieldModel->set('fieldvalue', $this->get($leadMappedField));
                    } else {
                        $fieldModel->set('fieldvalue', $fieldModel->getDefaultFieldValue());
                    } 
					$accountsFields[] = $fieldModel;
				}
			}
            foreach($complusoryFields as $complusoryField) {
                $fieldModel = nectarcrm_Field_Model::getInstance($complusoryField, $moduleModel);
				if($fieldModel->getPermissions('readwrite') && $fieldModel->isEditable()) {
                    $industryFieldModel = $moduleModel->getField($complusoryField);
                    $industryLeadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
                    if($this->get($industryLeadMappedField)) {
                        $industryFieldModel->set('fieldvalue', $this->get($industryLeadMappedField));
                    } else {
                        $industryFieldModel->set('fieldvalue', $industryFieldModel->getDefaultFieldValue());
                    }
                    $accountsFields[] = $industryFieldModel;
                }
            }
		}
		return $accountsFields;
	}

	/**
	 * Function returns Contact fields for Lead Convert
	 * @return Array
	 */
	function getContactFieldsForLeadConvert() {
		$contactsFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Contacts';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'CreateView')) {
			return;
		}

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();
            $complusoryFields = array('firstname', 'email');
            foreach($fieldModels as $fieldName => $fieldModel) {
                if($fieldModel->isMandatory() &&  $fieldName != 'assigned_user_id' && $fieldName != 'account_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }

                    $leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    $fieldValue = $this->get($leadMappedField);
                    if ($fieldName === 'account_id') {
                        $fieldValue = $this->get('company');
                    }
                    if($fieldValue) {
                        $fieldModel->set('fieldvalue', $fieldValue);
                    } else {
                        $fieldModel->set('fieldvalue', $fieldModel->getDefaultFieldValue());
                    }
                    $contactsFields[] = $fieldModel;
                }
            }

			foreach($complusoryFields as $complusoryField) {
                $fieldModel = nectarcrm_Field_Model::getInstance($complusoryField, $moduleModel);
				if($fieldModel->getPermissions('readwrite') && $fieldModel->isEditable()) {
					$leadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
					$fieldModel = $moduleModel->getField($complusoryField);
                    if($this->get($leadMappedField)) {
                        $fieldModel->set('fieldvalue', $this->get($leadMappedField));
                    } else {
                        $fieldModel->set('fieldvalue', $fieldModel->getDefaultFieldValue());
                    }
					$contactsFields[] = $fieldModel;
				}
			}
		}
		return $contactsFields;
	}

	/**
	 * Function returns Potential fields for Lead Convert
	 * @return Array
	 */
	function getPotentialsFieldsForLeadConvert() {
		$potentialFields = array();
		$privilegeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = 'Potentials';

		if(!Users_Privileges_Model::isPermitted($moduleName, 'CreateView')) {
			return;
		}

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if ($moduleModel->isActive()) {
			$fieldModels = $moduleModel->getFields();

            $complusoryFields = array('amount');
			foreach($fieldModels as $fieldName => $fieldModel) {
				if($fieldModel->isMandatory() &&  $fieldName != 'assigned_user_id' && $fieldName != 'related_to'
						&& $fieldName != 'contact_id') {
                    $keyIndex = array_search($fieldName,$complusoryFields);
                    if($keyIndex !== false) {
                        unset($complusoryFields[$keyIndex]);
                    }
					$leadMappedField = $this->getConvertLeadMappedField($fieldName, $moduleName);
                    if($this->get($leadMappedField)) { 
                        $fieldModel->set('fieldvalue', $this->get($leadMappedField));
                    } else {
                        $fieldModel->set('fieldvalue', $fieldModel->getDefaultFieldValue());
                    }
					$potentialFields[] = $fieldModel;
				}
			}
            foreach($complusoryFields as $complusoryField) {
                $fieldModel = nectarcrm_Field_Model::getInstance($complusoryField, $moduleModel);
                if($fieldModel->getPermissions('readwrite') && $fieldModel->isEditable()) {
                    $fieldModel = $moduleModel->getField($complusoryField);
                    $amountLeadMappedField = $this->getConvertLeadMappedField($complusoryField, $moduleName);
                    if($this->get($amountLeadMappedField)) {
                        $fieldModel->set('fieldvalue', $this->get($amountLeadMappedField));
                    } else {
                        $fieldModel->set('fieldvalue', $fieldModel->getDefaultFieldValue());
                    }
                    $potentialFields[] = $fieldModel;
                }
            }
		}
		return $potentialFields;
	}

	/**
	 * Function returns field mapped to Leads field, used in Lead Convert for settings the field values
	 * @param <String> $fieldName
	 * @return <String>
	 */
	function getConvertLeadMappedField($fieldName, $moduleName) {
		$mappingFields = $this->get('mappingFields');

		if (!$mappingFields) {
			$db = PearDatabase::getInstance();
			$mappingFields = array();

			$result = $db->pquery('SELECT * FROM nectarcrm_convertleadmapping', array());
			$numOfRows = $db->num_rows($result);

			$accountInstance = nectarcrm_Module_Model::getInstance('Accounts');
			$accountFieldInstances = $accountInstance->getFieldsById();

			$contactInstance = nectarcrm_Module_Model::getInstance('Contacts');
			$contactFieldInstances = $contactInstance->getFieldsById();

			$potentialInstance = nectarcrm_Module_Model::getInstance('Potentials');
			$potentialFieldInstances = $potentialInstance->getFieldsById();

			$leadInstance = nectarcrm_Module_Model::getInstance('Leads');
			$leadFieldInstances = $leadInstance->getFieldsById();

			for($i=0; $i<$numOfRows; $i++) {
				$row = $db->query_result_rowdata($result,$i);
				if(empty($row['leadfid'])) continue;

				$leadFieldInstance = $leadFieldInstances[$row['leadfid']];
				if(!$leadFieldInstance) continue;

				$leadFieldName = $leadFieldInstance->getName();
				$accountFieldInstance = $accountFieldInstances[$row['accountfid']];
				if ($row['accountfid'] && $accountFieldInstance) {
					$mappingFields['Accounts'][$accountFieldInstance->getName()] = $leadFieldName;
				}
				$contactFieldInstance = $contactFieldInstances[$row['contactfid']];
				if ($row['contactfid'] && $contactFieldInstance) {
					$mappingFields['Contacts'][$contactFieldInstance->getName()] = $leadFieldName;
				}
				$potentialFieldInstance = $potentialFieldInstances[$row['potentialfid']];
				if ($row['potentialfid'] && $potentialFieldInstance) {
					$mappingFields['Potentials'][$potentialFieldInstance->getName()] = $leadFieldName;
				}
			}
			$this->set('mappingFields', $mappingFields);
		}
		return $mappingFields[$moduleName][$fieldName];
	}

	/**
	 * Function returns the fields required for Lead Convert
	 * @return <Array of nectarcrm_Field_Model>
	 */
	function getConvertLeadFields() {
		$convertFields = array();
		$accountFields = $this->getAccountFieldsForLeadConvert();
		if(!empty($accountFields)) {
			$convertFields['Accounts'] = $accountFields;
		}

		$contactFields = $this->getContactFieldsForLeadConvert();
		if(!empty($contactFields)) {
			$convertFields['Contacts'] = $contactFields;
		}

		$potentialsFields = $this->getPotentialsFieldsForLeadConvert();
		if(!empty($potentialsFields)) {
			$convertFields['Potentials'] = $potentialsFields;
		}
		return $convertFields;
	}

	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
	function getCreateEventUrl() {
		$calendarModuleModel = nectarcrm_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function returns the url for create todo
	 * @return <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = nectarcrm_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
	}
    
    /**
	 * Function to check whether the lead is converted or not
	 * @return True if the Lead is Converted false otherwise.
	 */
    function isLeadConverted() {
        $db = PearDatabase::getInstance();
        $id = $this->getId();
        $sql = "select converted from nectarcrm_leaddetails where converted = 1 and leadid=?";
        $result = $db->pquery($sql,array($id));
        $rowCount = $db->num_rows($result);
        if($rowCount > 0){
            return true;
        }
        return false;
    }

}
