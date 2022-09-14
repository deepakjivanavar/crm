<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Users_SystemSetup_View extends nectarcrm_Index_View {
	
	public function preProcess(nectarcrm_Request $request, $display=true) {
		return true;
	}
	
	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$userModel = Users_Record_Model::getCurrentUserModel();
		$isFirstUser = Users_CRMSetup::isFirstUser($userModel);
		
		if($isFirstUser) {
			$viewer->assign('IS_FIRST_USER', $isFirstUser);
			$viewer->assign('PACKAGES_LIST', Users_CRMSetup::getPackagesList());
			$viewer->view('SystemSetup.tpl', $moduleName);
		} else {
			header ('Location: index.php?module=Users&parent=Settings&view=UserSetup');
			exit();
		}
	}
	
	function postProcess(nectarcrm_Request $request) {
		return true;
	}
	
}