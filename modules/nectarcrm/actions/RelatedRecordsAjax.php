<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_RelatedRecordsAjax_Action extends nectarcrm_Action_Controller {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('getRelatedRecordsCount');
	}

	function checkPermission(nectarcrm_Request $request) {
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function to get count of all related module records of a given record
	 * @param type $request
	 */
	function getRelatedRecordsCount($request) {
		$parentRecordId = $request->get("recordId");
		$parentModule = $request->get("module");
		$parentModuleModel = nectarcrm_Module_Model::getInstance($parentModule);
		$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentRecordId, $parentModuleModel);
		$relationModels = $parentModuleModel->getRelations();
		$relatedRecordsCount = array();
		foreach ($relationModels as $relation) {
			$relationId = $relation->getId();
			$relatedModuleName = $relation->get('relatedModuleName');
			$relationListView = nectarcrm_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $relation->get('label'));
			$count = $relationListView->getRelatedEntriesCount();
			$relatedRecordsCount[$relationId] = $count;
		}
		$response = new nectarcrm_Response();
		$response->setResult($relatedRecordsCount);
		$response->emit();
	}

}
