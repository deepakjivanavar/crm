<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_AddFolder_View extends nectarcrm_IndexAjax_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		if(!Users_Privileges_Model::isPermitted($moduleName, 'CreateView')) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}

	public function process (nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		if ($request->has('folderid') && $request->get('mode') == 'edit') {
			$folderId = $request->get('folderid');
			$folderModel = Documents_Folder_Model::getInstanceById($folderId);

			$viewer->assign('FOLDER_ID', $folderId);
			$viewer->assign('SAVE_MODE', $request->get('mode'));
			$viewer->assign('FOLDER_NAME', $folderModel->getName());
			$viewer->assign('FOLDER_DESC', $folderModel->getDescription());
		}
		$viewer->assign('MODULE',$moduleName);
		$viewer->view('AddFolder.tpl', $moduleName);
	}
}