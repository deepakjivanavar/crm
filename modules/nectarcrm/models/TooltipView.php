<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

vimport('~~/include/Webservices/Query.php');

class nectarcrm_TooltipView_Model extends nectarcrm_DetailRecordStructure_Model {

	protected $fields = false;

	/**
	 * Function to set the module instance
	 * @param <nectarcrm_Module_Model> $moduleInstance - module model
	 * @return nectarcrm_DetailView_Model>
	 */
	public function setModule($moduleInstance) {
		$this->module = $moduleInstance;
		$this->fields = $this->module->getSummaryViewFieldsList();
		if (empty($this->fields)) {
			$this->fields = $this->module->getMandatoryFieldModels();
		}
		return $this;
	}
	
	/**
	 * Function to get list of tooltip enabled field model.
	 * @return <nectarcrm_Field_Model>
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Function to load record
	 * @param <Number> $recordId
	 * @return <nectarcrm_Record_Model>
	 */
	protected function loadRecord($recordId) {
		$moduleName = $this->module->getName();
		
		// Preparation to pull required tool-tip field values.
		$referenceFields = array(); $fieldNames = array();
		foreach ($this->fields as $fieldModel) {
			$fieldType = $fieldModel->getFieldDataType();
			$fieldName = $fieldModel->get('name');
            if($moduleName == 'Documents' && $fieldName == 'filename') {
                continue;
            }
			
			$fieldNames[] = $fieldName;
			if ($fieldType == 'reference' || $fieldType == 'owner') {
				$referenceFields[] = $fieldName;
			}
		}
		$wsid = vtws_getWebserviceEntityId($moduleName, $recordId);
		$q = sprintf("SELECT %s FROM %s WHERE id='%s' LIMIT 1;", implode(',', $fieldNames), $moduleName, $wsid);
		
		// Retrieves only required fields of the record with permission check.
		try {
			$data = array_shift(vtws_query($q, Users_Record_Model::getCurrentUserModel()));

			if ($data) {
				// De-transform the webservice ID to CRM ID.
				foreach ($data as $key => $value) {
					if (in_array($key, $referenceFields)) {
						$value = array_pop(explode('x', $value));
					}
					$data[$key] = $value;
				}
			}
			
			$this->record = nectarcrm_Record_Model::getCleanInstance($moduleName);
			$this->record->setData($data);
			
		} catch(WebServiceException $wex) {
			// Error retrieving information !
		}
		return $this;
	}
	
	/**
	 * Function to get the values in stuctured format
	 * @return <array> - values in structure array('block'=>array(fieldinfo));
	 */
	public function getStructure() {
		if (!$this->structuredValues) {
			$tooltipFieldsList = $this->fields;
			$recordModel = $this->getRecord();
			$this->structuredValues = array('TOOLTIP_FIELDS' => array());
            $moduleName = $this->module->getName();
			if ($tooltipFieldsList) {
				foreach ($tooltipFieldsList as $fieldModel) {
					$fieldName = $fieldModel->get('name');
                    if($moduleName == 'Documents' && $fieldName == 'filename') {
                        continue;
                    }
					if($fieldModel->isViewableInDetailView()) {
                        // tosafeHTML is to avoid XSS Vulnerability
						$fieldModel->set('fieldvalue', nectarcrm_Util_Helper::toSafeHTML($recordModel->get($fieldName)));
						$this->structuredValues['TOOLTIP_FIELDS'][$fieldName] = $fieldModel;
					}
				}
			}
		}
		
		return $this->structuredValues;
	}
	
	/**
	 * Function to get the instance
	 * @param <String> $moduleName - module name
	 * @param <String> $recordId - record id
	 * @return <nectarcrm_DetailView_Model>
	 */
	public static function getInstance($moduleName,$recordId) {
       if($moduleName=="Calendar"){
            $recordModel = nectarcrm_Record_Model::getInstanceById($recordId);
			$activityType = $recordModel->getType();
            if($activityType=="Events"){
                $moduleName="Events";
            }
        }
		$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'TooltipView', $moduleName);
		$instance = new $modelClassName();

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		
		return $instance->setModule($moduleModel)->loadRecord($recordId);
	}
}
