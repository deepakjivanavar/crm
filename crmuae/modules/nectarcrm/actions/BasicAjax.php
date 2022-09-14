<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_BasicAjax_Action extends nectarcrm_Action_Controller {

	function checkPermission(nectarcrm_Request $request) {
		return;
	}

	public function process(nectarcrm_Request $request) {
		$searchValue = $request->get('search_value');
		$searchModule = $request->get('search_module');

		$parentRecordId = $request->get('parent_id');
		$parentModuleName = $request->get('parent_module');
		$relatedModule = $request->get('module');

		$searchModuleModel = nectarcrm_Module_Model::getInstance($searchModule);
		$records = $searchModuleModel->searchRecord($searchValue, $parentRecordId, $parentModuleName, $relatedModule);

		$baseRecordId = $request->get('base_record');
		$result = array();
		foreach($records as $moduleName=>$recordModels) {
			foreach($recordModels as $recordModel) {
				if ($recordModel->getId() != $baseRecordId) {
					$result[] = array('label'=>decode_html($recordModel->getName()), 'value'=>decode_html($recordModel->getName()), 'id'=>$recordModel->getId());
				}
			}
		}

		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}
}
