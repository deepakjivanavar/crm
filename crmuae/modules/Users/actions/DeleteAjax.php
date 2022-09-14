<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/include/Webservices/Custom/DeleteUser.php');

class Users_DeleteAjax_Action extends nectarcrm_Delete_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		 $ownerId = $request->get('userid');
		if(!$currentUser->isAdminUser()) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
		} else if($currentUser->isAdminUser() && ($currentUser->getId() == $ownerId)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
		}
	}
	
	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
        $ownerId = $request->get('userid');
        $newOwnerId = $request->get('transfer_user_id');
        
        $mode = $request->get('mode');
        $response = new nectarcrm_Response();
        $result['message'] = vtranslate('LBL_USER_DELETED_SUCCESSFULLY', $moduleName);

		if($mode == 'permanent'){
            Users_Record_Model::deleteUserPermanently($ownerId, $newOwnerId);
        } else {
            $userId = vtws_getWebserviceEntityId($moduleName, $ownerId);
            $transformUserId = vtws_getWebserviceEntityId($moduleName, $newOwnerId);

            $userModel = Users_Record_Model::getCurrentUserModel();

            vtws_deleteUser($userId, $transformUserId, $userModel);

            if($request->get('permanent') == '1') {
                Users_Record_Model::deleteUserPermanently($ownerId, $newOwnerId);
            }    
        }
        
        if($request->get('mode') == 'deleteUserFromDetailView'){
            $usersModuleModel = Users_Module_Model::getInstance($moduleName);
            $listViewUrl = $usersModuleModel->getListViewUrl();
            $result['listViewUrl'] = $listViewUrl;
        }
		
		$response->setResult($result);
		$response->emit();
	}
}
