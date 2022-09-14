<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Inventory_MassSave_Action extends nectarcrm_MassSave_Action {

	public function process(nectarcrm_Request $request) {
		$response = new nectarcrm_Response();
		try {
			vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', $request->get('_timeStampNoChangeMode',false));
			$moduleName = $request->getModule();
			$recordModels = $this->getRecordModelsFromRequest($request);
			foreach($recordModels as $recordId => $recordModel) {
				if(Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId)) {
					//Inventory line items getting wiped out
					$_REQUEST['ajxaction'] = 'DETAILVIEW';
					$recordModel->save();
				}
			}
			vglobal('NECTARCRM_TIMESTAMP_NO_CHANGE_MODE', false);
			$response->setResult(true);
		} catch (DuplicateException $e) {
			$response->setError($e->getMessage(), $e->getDuplicationMessage(), $e->getMessage());
		} catch (Exception $e) {
			$response->setError($e->getMessage());
		}
		$response->emit();
	}
}
