<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class PriceBooks_SaveAjax_Action extends nectarcrm_SaveAjax_Action {

	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);
        vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', $request->get('_timeStampNoChangeMode',false));
		$recordModel->save();
        vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', false);
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
				$sellingPricesList = $parentModuleModel->getPricesForProducts($recordModel->get('currency_id'), array($parentRecordId));
				$recordModel->updateListPrice($parentRecordId, $sellingPricesList[$parentRecordId]);
			}
		}
		return $recordModel;
	}
}
