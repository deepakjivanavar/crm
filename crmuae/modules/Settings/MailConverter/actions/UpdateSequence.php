<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_MailConverter_UpdateSequence_Action extends Settings_nectarcrm_Index_Action {

	public function checkPermission(nectarcrm_Request $request) {
		parent::checkPermission($request);
		$scannerId = $request->get('scannerId');

		if (!$scannerId) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $request->getModule(false)));
		}
	}

	public function process(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$scannerId = $request->get('scannerId');
		$sequencesList = $request->get('sequencesList');

		$scannerModel = Settings_MailConverter_Record_Model::getInstanceById($scannerId);

		$response = new nectarcrm_Response();
		if ($sequencesList) {
			$scannerModel->updateSequence($sequencesList);
			$response->setResult(vtranslate('LBL_SEQUENCE_UPDATED_SUCCESSFULLY', $qualifiedModuleName));
		} else {
			$response->setError(vtranslate('LBL_RULES_SEQUENCE_INFO_IS_EMPTY', $qualifiedModuleName));
		}

		$response->emit();
	}
        
        public function validateRequest(nectarcrm_Request $request) { 
            $request->validateWriteAccess(); 
        } 
}