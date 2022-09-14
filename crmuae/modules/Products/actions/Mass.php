<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Products_Mass_Action extends nectarcrm_Mass_Action {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('isChildProduct');
	}

	public function checkPermission(nectarcrm_Request $request) {
		return true;
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		} else {
			parent::process($request);
		}
	}

	public function isChildProduct(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordIdsList = $this->getRecordsListFromRequest($request);

		$response = new nectarcrm_Response();
		if ($moduleName && $recordIdsList) {
			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			$areChildProducts = $moduleModel->areChildProducts($recordIdsList);

			$response->setResult($areChildProducts);
		}
		$response->emit();
	}
}
