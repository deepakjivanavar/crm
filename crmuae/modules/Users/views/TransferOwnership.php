<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Users_TransferOwnership_View extends nectarcrm_Index_View {
    
    public function checkPermission(nectarcrm_Request $request){
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if(!$currentUserModel->isAdminUser()) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
		}
	}
    
    public function process(nectarcrm_Request $request) {
        $moduleName = $request->getModule();
        $usersList = Users_Record_Model::getActiveAdminUsers();
        $activeAdminId = Users::getActiveAdminId();
        unset($usersList[$activeAdminId]);
        $viewer = $this->getViewer($request);
        $viewer->assign('USERS_MODEL', $usersList);
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('TransferOwnership.tpl', $moduleName);
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}