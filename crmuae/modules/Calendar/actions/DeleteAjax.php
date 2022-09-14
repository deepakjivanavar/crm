<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_DeleteAjax_Action extends nectarcrm_DeleteAjax_Action {
	
	function checkPermission(nectarcrm_Request $request) {
		$sourceModule = $request->get('sourceModule');
		if (!$sourceModule) {
			$sourceModule = $request->getModule();
		}
		$record = $request->get('record');

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if (!$currentUserPrivilegesModel->isPermitted($sourceModule, 'Delete', $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if ($record) {
			$activityModulesList = array('Calendar', 'Events');
			$recordEntityName = getSalesEntityType($record);

			if (!in_array($recordEntityName, $activityModulesList) || !in_array($sourceModule, $activityModulesList)) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
	}
	
	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$recurringEditMode = $request->get('recurringEditMode');
		
		$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);
		$recordModel->set('recurringEditMode', $recurringEditMode);
		$deletedRecords = $recordModel->delete();

		$cvId = $request->get('viewname');
		deleteRecordFromDetailViewNavigationRecords($recordId, $cvId, $moduleName);
		$response = new nectarcrm_Response();
		$response->setResult(array('viewname' => $cvId, 'module' => $moduleName, 'deletedRecords' => $deletedRecords));
		$response->emit();
	}

}
