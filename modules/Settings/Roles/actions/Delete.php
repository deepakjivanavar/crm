<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Roles_Delete_Action extends Settings_nectarcrm_Basic_Action {

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$recordId = $request->get('record');
		$transferRecordId = $request->get('transfer_record');

		$moduleModel = Settings_nectarcrm_Module_Model::getInstance($qualifiedModuleName);
		$recordModel = Settings_Roles_Record_Model::getInstanceById($recordId);
		$transferToRole = Settings_Roles_Record_Model::getInstanceById($transferRecordId);
		if($recordModel && $transferToRole) {
			$recordModel->delete($transferToRole);
		}

		$redirectUrl = $moduleModel->getDefaultUrl();
		header("Location: $redirectUrl");
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}
