<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class SMSNotifier_MassSaveAjax_Action extends nectarcrm_Mass_Action {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	/**
	 * Function that saves SMS records
	 * @param nectarcrm_Request $request
	 */
	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$recordIds = $this->getRecordsListFromRequest($request);
		$phoneFieldList = $request->get('fields');
		$message = $request->get('message');

		foreach($recordIds as $recordId) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId);
			$numberSelected = false;
			foreach($phoneFieldList as $fieldname) {
				$fieldValue = $recordModel->get($fieldname);
				if(!empty($fieldValue)) {
					$toNumbers[] = $fieldValue;
					$numberSelected = true;
				}
			}
			if($numberSelected) {
				$recordIds[] = $recordId;
			}
		}

		$response = new nectarcrm_Response();
        
		if(!empty($toNumbers)) {
			$id = SMSNotifier_Record_Model::SendSMS($message, $toNumbers, $currentUserModel->getId(), $recordIds, $moduleName);
            $statusDetails = SMSNotifier::getSMSStatusInfo($id);
			$response->setResult(array('id' => $id, 'statusdetails' => $statusDetails[0]));
		} else {
			$response->setResult(false);
		}
		return $response;
	}
}
