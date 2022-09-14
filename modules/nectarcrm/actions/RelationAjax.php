<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_RelationAjax_Action extends nectarcrm_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addRelation');
		$this->exposeMethod('deleteRelation');
		$this->exposeMethod('getRelatedListPageCount');
		$this->exposeMethod('getRelatedRecordInfo');
	}

	function checkPermission(nectarcrm_Request $request) { }

	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	function postProcess(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/*
	 * Function to add relation for specified source record id and related record id list
	 * @param <array> $request
	 *		keys					Content
	 *		src_module				source module name
	 *		src_record				source record id
	 *		related_module			related module name
	 *		related_record_list		json encoded of list of related record ids
	 */
	function addRelation($request) {
		$sourceModule = $request->getModule();
		$sourceRecordId = $request->get('src_record');

		$relatedModule = $request->get('related_module');
		$relatedRecordIdList = $request->get('related_record_list');

		$sourceModuleModel = nectarcrm_Module_Model::getInstance($sourceModule);
		$relatedModuleModel = nectarcrm_Module_Model::getInstance($relatedModule);
		$relationModel = nectarcrm_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
		foreach($relatedRecordIdList as $relatedRecordId) {
			$relationModel->addRelation($sourceRecordId,$relatedRecordId);
		}

		$response = new nectarcrm_Response();
		$response->setResult(true);
		$response->emit();
	}

	/**
	 * Function to delete the relation for specified source record id and related record id list
	 * @param <array> $request
	 *		keys					Content
	 *		src_module				source module name
	 *		src_record				source record id
	 *		related_module			related module name
	 *		related_record_list		json encoded of list of related record ids
	 */
	function deleteRelation($request) {
		$sourceModule = $request->getModule();
		$sourceRecordId = $request->get('src_record');

		$relatedModule = $request->get('related_module');
		$relatedRecordIdList = $request->get('related_record_list');
		$recurringEditMode = $request->get('recurringEditMode');
		$relatedRecordList = array();
		if($relatedModule == 'Calendar' && !empty($recurringEditMode) && $recurringEditMode != 'current') {
			foreach($relatedRecordIdList as $relatedRecordId) {
				$recordModel = nectarcrm_Record_Model::getCleanInstance($relatedModule);
				$recordModel->set('id', $relatedRecordId);
				$recurringRecordsList = $recordModel->getRecurringRecordsList();
				foreach($recurringRecordsList as $parent => $childs) {
					$parentRecurringId = $parent;
					$childRecords = $childs;
				}
				if($recurringEditMode == 'future') {
					$parentKey = array_keys($childRecords, $relatedRecordId);
					$childRecords = array_slice($childRecords, $parentKey[0]);
				}
				foreach($childRecords as $recordId) {
					$relatedRecordList[] = $recordId;
				}
				$relatedRecordIdList = array_slice($relatedRecordIdList, $relatedRecordId);
			}
		}

		foreach($relatedRecordList as $record) {
			$relatedRecordIdList[] = $record;
		}

		//Setting related module as current module to delete the relation
		vglobal('currentModule', $relatedModule);

		$sourceModuleModel = nectarcrm_Module_Model::getInstance($sourceModule);
		$relatedModuleModel = nectarcrm_Module_Model::getInstance($relatedModule);
		$relationModel = nectarcrm_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
		foreach($relatedRecordIdList as $relatedRecordId) {
			$response = $relationModel->deleteRelation($sourceRecordId,$relatedRecordId);
		}

		$response = new nectarcrm_Response();
		$response->setResult(true);
		$response->emit();
	}

	/**
	 * Function to get the page count for reltedlist
	 * @return total number of pages
	 */
	function getRelatedListPageCount(nectarcrm_Request $request){
		$moduleName = $request->getModule();
		$relatedModuleName = $request->get('relatedModule');
		$parentId = $request->get('record');
		$label = $request->get('tab_label');
		$pagingModel = new nectarcrm_Paging_Model();
		$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentId, $moduleName);
		$relationListView = nectarcrm_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
		$totalCount = $relationListView->getRelatedEntriesCount();
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $totalCount / (int) $pageLimit);

		if($pageCount == 0){
			$pageCount = 1;
		}
		$result = array();
		$result['numberOfRecords'] = $totalCount;
		$result['page'] = $pageCount;
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}

	function getRelatedRecordInfo($request) {
		try {
			return $this->getParentRecordInfo($request);
		} catch (Exception $e) {
			$response = new nectarcrm_Response();
			$response->setError($e->getCode(), $e->getMessage());
			$response->emit();
		}
	}

	function getParentRecordInfo($request) {
		$moduleName = $request->get('module');
		$recordModel = nectarcrm_Record_Model::getInstanceById($request->get('id'), $moduleName);
		$moduleModel = $recordModel->getModule();
		$autoFillData = $moduleModel->getAutoFillModuleAndField($moduleName);

		if($autoFillData) {
			foreach($autoFillData as $data) {
				$autoFillModule = $data['module'];
				$autoFillFieldName = $data['fieldname'];
				$autofillRecordId = $recordModel->get($autoFillFieldName);

				$autoFillNameArray = getEntityName($autoFillModule, $autofillRecordId);
				$autoFillName = $autoFillNameArray[$autofillRecordId];

				$resultData[] = array(	'id'		=> $request->get('id'), 
										'name'		=> decode_html($recordModel->getName()),
										'parent_id'	=> array(	'id' => $autofillRecordId,
																'name' => decode_html($autoFillName),
																'module' => $autoFillModule));
			}

			$result[$request->get('id')] = $resultData;

		} else {
			$resultData = array('id'	=> $request->get('id'), 
								'name'	=> decode_html($recordModel->getName()),
								'info'	=> $recordModel->getRawData());
			$result[$request->get('id')] = $resultData;
		}

		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

}
