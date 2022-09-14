<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Authenticate_View extends nectarcrm_Index_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'index');
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		return true;
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$oauth2Connector = new Google_Oauth2_Connector($moduleName);
		$oauth2Connector->authorize();
	}
}
