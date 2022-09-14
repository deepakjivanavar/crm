<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class ExtensionStore_Promotion_Action extends nectarcrm_Index_View {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('maxCreatedOn');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    protected function maxCreatedOn(nectarcrm_Request $request){
		$modelInstance = Settings_ExtensionStore_Extension_Model::getInstance();
		$promotions = $modelInstance->getMaxCreatedOn('Promotion', 'max', 'createdon');
		$response = new nectarcrm_Response();
		if ($promotions['success'] != 'true') {
			$response->setError('', $promotions['error']);
		} else {
			$response->setResult($promotions['response']);
		}
		$response->emit();
	}
}
