<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_SharingAccess_SaveAjax_Action extends nectarcrm_SaveAjax_Action {

    public function checkPermission(nectarcrm_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        if($currentUser->isAdminUser()) {
            return true;
        } else {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
        }
	}

	public function process(nectarcrm_Request $request) {
		$modulePermissions = $request->get('permissions');

		foreach($modulePermissions as $tabId => $permission) {
			$moduleModel = Settings_SharingAccess_Module_Model::getInstance($tabId);
			$moduleModel->set('permission', $permission);

			try {
				$moduleModel->save();
			} catch (AppException $e) {
				
			}
		}
		Settings_SharingAccess_Module_Model::recalculateSharingRules();

		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->emit();
	}
 }