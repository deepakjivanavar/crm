<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class ModComments_Edit_View extends nectarcrm_Edit_View {

	public function checkPermission(nectarcrm_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleName = $request->getModule();
		$record = $request->get('record');
		if (!empty($record) || !Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}
}