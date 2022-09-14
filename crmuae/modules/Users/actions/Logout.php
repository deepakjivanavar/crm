<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Users_Logout_Action extends nectarcrm_Action_Controller {
	
	function checkPermission(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		//Redirect into the referer page
		$logoutURL = $this->getLogoutURL();
        session_regenerate_id(true);
		nectarcrm_Session::destroy();
		
		//Track the logout History
		$moduleName = $request->getModule();
		$moduleModel = Users_Module_Model::getInstance($moduleName);
		$moduleModel->saveLogoutHistory();
		//End

		if(!empty($logoutURL)) {
			header('Location: '.$logoutURL);
			exit();
		} else {
			header ('Location: index.php');
		}
	}
	
	protected function getLogoutURL() {
		$logoutUrl = nectarcrm_Session::get('LOGOUT_URL');
		if (isset($logoutUrl) && !empty($logoutUrl)) {
			return $logoutUrl;
		}
		return nectarcrmConfig::getOD('LOGIN_URL');
	}
}