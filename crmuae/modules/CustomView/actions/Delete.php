<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class CustomView_Delete_Action extends nectarcrm_Action_Controller {

	public function process(nectarcrm_Request $request) {
		$customViewModel = CustomView_Record_Model::getInstanceById($request->get('record'));
		$moduleModel = $customViewModel->getModule();

		$customViewModel->delete();

		$listViewUrl = $moduleModel->getListViewUrl();
		if ($request->isAjax()) {
			$response = new nectarcrm_Response();
			$response->setResult(array('success' => true));
			$response->emit();
		} else {
			header("Location: $listViewUrl");
		}
	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}
}
