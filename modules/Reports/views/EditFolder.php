<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Reports_EditFolder_View extends nectarcrm_IndexAjax_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Reports_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process (nectarcrm_Request $request) {
		
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$folderId = $request->get('folderid');

		if ($folderId) {
			$folderModel = Reports_Folder_Model::getInstanceById($folderId);
		} else {
			$folderModel = Reports_Folder_Model::getInstance();
		}
		
		$viewer->assign('FOLDER_MODEL', $folderModel);
		$viewer->assign('MODULE',$moduleName);
		$viewer->view('EditFolder.tpl', $moduleName);
	}
}