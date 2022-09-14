<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Roles_MoveAjax_Action extends Settings_nectarcrm_Basic_Action {

	public function preProcess(nectarcrm_Request $request) {
		return;
	}

	public function postProcess(nectarcrm_Request $request) {
		return;
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$parentRoleId = $request->get('parent_roleid');

		$parentRole = Settings_Roles_Record_Model::getInstanceById($parentRoleId);
		$recordModel = Settings_Roles_Record_Model::getInstanceById($recordId);

		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		try {
			$recordModel->moveTo($parentRole);
            //on moving a role sharing privilages should be recalculated for all the users
            $allUsers = Users_Record_Model::getAll();
            foreach ($allUsers as $userId=>$userModel) {
                require_once('modules/Users/CreateUserPrivilegeFile.php');
                createUserSharingPrivilegesfile($userId);
            }
		} catch (AppException $e) {
			$response->setError('Move Role Failed');
		}
		$response->emit();
	}
}
