<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Portal_MassDelete_Action extends nectarcrm_MassDelete_Action {

    function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

    public function process(nectarcrm_Request $request) {
        $module = $request->getModule();
        
        Portal_Module_Model::deleteRecords($request);
        
        $response = new nectarcrm_Response();
        $result = array('message' => vtranslate('LBL_BOOKMARKS_DELETED_SUCCESSFULLY', $module));
        $response->setResult($result);
        $response->emit();
    }
}