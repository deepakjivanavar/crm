<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_CheckDuplicate_Action extends nectarcrm_Action_Controller {

	function checkPermission(nectarcrm_Request $request) {
		return;
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$reportName = $request->get('reportname');
		$record = $request->get('record');
		
		if ($record) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($record, $moduleName);
		} else {
			$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
		}

		$recordModel->set('reportname', $reportName);
		$recordModel->set('reportid', $record);
		$recordModel->set('isDuplicate', $request->get('isDuplicate'));
		
		if (!$recordModel->checkDuplicate()) {
			$result = array('success'=>false);
		} else {
			$result = array('success'=>true, 'message'=>vtranslate('LBL_DUPLICATES_EXIST', $moduleName));
		}
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}
}
