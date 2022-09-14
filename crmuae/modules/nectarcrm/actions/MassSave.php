<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_MassSave_Action extends nectarcrm_Mass_Action {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$response = new nectarcrm_Response();
		try {
			vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', $request->get('_timeStampNoChangeMode',false));
			$moduleName = $request->getModule();
			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			$recordModels = $this->getRecordModelsFromRequest($request);
			$allRecordSave= true;
			foreach($recordModels as $recordId => $recordModel) {
				if(Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId)) {
					$recordModel->save();
				} else {
					$allRecordSave= false;
				}
			}
			vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', false);
			if($allRecordSave) {
				$response->setResult(true);
			} else {
			   $response->setResult(false);
			}
		} catch (DuplicateException $e) {
			$response->setError($e->getMessage(), $e->getDuplicationMessage(), $e->getMessage());
		} catch (Exception $e) {
			$response->setError($e->getMessage());
		}
		$response->emit();
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param nectarcrm_Request $request
	 * @return nectarcrm_Record_Model or Module specific Record Model instance
	 */
	function getRecordModelsFromRequest(nectarcrm_Request $request) {

		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$recordIds = $this->getRecordsListFromRequest($request);
		$recordModels = array();

		$fieldModelList = $moduleModel->getFields();
		foreach($recordIds as $recordId) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleModel);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');

			foreach ($fieldModelList as $fieldName => $fieldModel) {
				$fieldValue = $request->get($fieldName, null);
				$fieldDataType = $fieldModel->getFieldDataType();
				if($fieldDataType == 'time'){
					$fieldValue = nectarcrm_Time_UIType::getTimeValueWithSeconds($fieldValue);
				}
				if(isset($fieldValue) && $fieldValue != null) {
					if(!is_array($fieldValue)) {
						$fieldValue = trim($fieldValue);
					}
					$recordModel->set($fieldName, $fieldValue);
				} else {
					$uiType = $fieldModel->get('uitype');
					if($uiType == 70) {
						$recordModel->set($fieldName, $recordModel->get($fieldName));
					}  else {
						$uiTypeModel = $fieldModel->getUITypeModel();
						$recordModel->set($fieldName, $uiTypeModel->getUserRequestValue($recordModel->get($fieldName)));
					}
				}
			}
			$recordModels[$recordId] = $recordModel;
		}
		return $recordModels;
	}
}
