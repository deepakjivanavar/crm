<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_Profiles_EditAjax_Action extends Settings_nectarcrm_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('checkDuplicate');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function checkDuplicate(nectarcrm_Request $request) {
		$profileName = $request->get('profilename');
		$recordId = $request->get('record');

		$recordModel = Settings_Profiles_Record_Model::getInstanceByName($profileName, false, array($recordId));

		$response = new nectarcrm_Response();
		if(!empty($recordModel)) {
			$response->setResult(array('success' => true,'message'=>  vtranslate('LBL_DUPLICATES_EXIST',$request->getModule(false))));
			
		}else{
			$response->setResult(array('success' => false));
		}
		$response->emit();
	}

}