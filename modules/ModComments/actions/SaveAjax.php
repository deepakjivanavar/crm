<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class ModComments_SaveAjax_Action extends nectarcrm_SaveAjax_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');
		//Do not allow ajax edit of existing comments
		if ($record) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUserModel->getId();
		$request->set('assigned_user_id', $userId);
		$request->set('userid', $userId);
		
		$recordModel = $this->saveRecord($request);
        
		$fieldModelList = $recordModel->getModule()->getFields();
		$result = array();
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			$fieldValue = $recordModel->get($fieldName);
			$result[$fieldName] = array('value' => $fieldValue, 'display_value' => $fieldModel->getDisplayValue($fieldValue));
		}
		$result['id'] = $result['_recordId'] = $recordModel->getId();
		$result['_recordLabel'] = $recordModel->getName();
        
		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}
	
	/**
	 * Function to save record
	 * @param <nectarcrm_Request> $request - values of the record
	 * @return <RecordModel> - record Model of saved record
	 */
	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);
		
		$recordModel->save();
		if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentModuleModel = nectarcrm_Module_Model::getInstance($parentModuleName);
			$parentRecordId = $request->get('sourceRecord');
			$relatedModule = $recordModel->getModule();
			$relatedRecordId = $recordModel->getId();

			$relationModel = nectarcrm_Relation_Model::getInstance($parentModuleModel, $relatedModule);
			$relationModel->addRelation($parentRecordId, $relatedRecordId);
		}
		return $recordModel;
	}
	
	/**
	 * Function to get the record model based on the request parameters
	 * @param nectarcrm_Request $request
	 * @return nectarcrm_Record_Model or Module specific Record Model instance
	 */
	public function getRecordModelFromRequest(nectarcrm_Request $request) {
		$recordModel = parent::getRecordModelFromRequest($request);
		
		$recordModel->set('commentcontent', $request->getRaw('commentcontent'));
        $recordModel->set('is_private', $request->get('is_private'));

		return $recordModel;
	}
}