<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

Class Users_EditAjax_View extends nectarcrm_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('changePassword');
		$this->exposeMethod('changeUsername');
	}

	public function checkPermission(nectarcrm_Request $request){
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$userId = $request->get('recordId');
		if($currentUserModel->getId() != $userId && !$currentUserModel->isAdminUser()) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
		}
	}
	
	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function changePassword(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->get('module');
		$userId = $request->get('recordId');

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USERID', $userId);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('ChangePassword.tpl', $moduleName);
	}

	public function changeUsername(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$userId = $request->get('record');
		$userModel = Users_Record_Model::getInstanceFromPreferenceFile($userId);
		
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('USER_MODEL',$userModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('ChangeUsername.tpl', $moduleName);
	}

}