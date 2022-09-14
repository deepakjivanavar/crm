<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_ActivityReminder_Action extends nectarcrm_Action_Controller{

	function __construct() {
		$this->exposeMethod('getReminders');
		$this->exposeMethod('postpone');
	}

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}

	function getReminders(nectarcrm_Request $request) {
		$recordModels = Calendar_Module_Model::getCalendarReminder();
		foreach($recordModels as $record) {
			$records[] = $record->getDisplayableValues();
			$record->updateReminderStatus();
		}

		$response = new nectarcrm_Response();
		$response->setResult($records);
		$response->emit();
	}

	function postpone(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$module = $request->getModule();
		$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $module);
		$recordModel->updateReminderStatus(0);
	}
}