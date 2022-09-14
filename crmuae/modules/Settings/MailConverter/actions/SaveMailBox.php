<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_MailConverter_SaveMailBox_Action extends Settings_nectarcrm_Index_Action {

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);

		if ($recordId) {
			$recordModel = Settings_MailConverter_Record_Model::getInstanceById($recordId);
		} else {
			$recordModel = Settings_MailConverter_Record_Model::getCleanInstance();
		}

		$recordModel->set('scannerOldName', $request->get('scannerOldName'));
		$fieldsList = $recordModel->getModule()->getFields();
		foreach ($fieldsList as $fieldName=>$fieldModel) {
			$recordModel->set($fieldName, $request->get($fieldName));
		}

		$status = $recordModel->save();

		$response = new nectarcrm_Response();
		if ($status) {
			$result = array('message' => vtranslate('LBL_SAVED_SUCCESSFULLY', $qualifiedModuleName));
			$result['id'] = $recordModel->getId();
			$result['listViewUrl'] = $recordModel->getListUrl();
			$response->setResult($result);
		} else {
			$response->setError(vtranslate('LBL_CONNECTION_TO_MAILBOX_FAILED', $qualifiedModuleName));
		}
		$response->emit();
	}
        
        public function validateRequest(nectarcrm_Request $request) { 
            $request->validateWriteAccess(); 
        }
}