<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class EmailTemplates_DeleteAjax_Action extends nectarcrm_Delete_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$recordModel = EmailTemplates_Record_Model::getInstanceById($recordId);
		$recordModel->setModule($moduleName);
		$recordModel->delete();

		$cvId = $request->get('viewname');
		$response = new nectarcrm_Response();
		$response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
		if($recordModel->isSystemTemplate()) {
			 $response->setError('502', vtranslate('LBL_NO_PERMISSIONS_TO_DELETE_SYSTEM_TEMPLATE', $moduleName));
		}
		$response->emit();
	}
}
