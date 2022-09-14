<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_MailConverter_ScanNow_Action extends Settings_nectarcrm_Index_Action {

	public function checkPermission(nectarcrm_Request $request) {
		parent::checkPermission($request);
		$recordId = $request->get('record');

		if (!$recordId) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $request->getModule(false)));
		}
	}

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);

		$recordModel = Settings_MailConverter_Record_Model::getInstanceById($recordId);
		$status = $recordModel->scanNow();

		$response = new nectarcrm_Response();
		if (is_bool($status) && $status) {
			$result = array('message'=> vtranslate('LBL_SCANNED_SUCCESSFULLY', $qualifiedModuleName));
			$result['id'] = $recordModel->getId();
			$response->setResult($result);
		} else if ($status) {
			$response->setError($status);
		} else {
			$response->setError(vtranslate($request->getModule(), $qualifiedModuleName).' '.vtranslate('LBL_IS_IN_RUNNING_STATE', $qualifiedModuleName));
		}
		$response->emit();
	}
}