<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_nectarcrm_TermsAndConditionsAjax_Action extends Settings_nectarcrm_Basic_Action {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('save');
		$this->exposeMethod('getModuleTermsAndConditions');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function save(nectarcrm_Request $request) {
		$moduleName = $request->get('type');
		$model = Settings_nectarcrm_TermsAndConditions_Model::getInstance($moduleName);
		$model->setText($request->get('tandc'));
		$model->save();

		$response = new nectarcrm_Response();
		$response->emit();
	}

	public function getModuleTermsAndConditions(nectarcrm_Request $request) {
		$moduleName = $request->get('type');
		$model = Settings_nectarcrm_TermsAndConditions_Model::getInstance($moduleName);
		$conditionText = $model->getText();

		$response = new nectarcrm_Response();
		$response->setResult(decode_html($conditionText));
		$response->emit();
	}

}
