<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PriceBooks_Save_Action extends nectarcrm_Save_Action {

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

			//To store the relationship between Products/Services and PriceBooks
			if ($parentRecordId && ($parentModuleName === 'Products' || $parentModuleName === 'Services')) {
				$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
				$recordModel->updateListPrice($parentRecordId, $parentRecordModel->get('unit_price'));
			}
		}
		return $recordModel;
	}

}