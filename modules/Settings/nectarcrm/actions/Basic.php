<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
class Settings_nectarcrm_Basic_Action extends Settings_nectarcrm_IndexAjax_View {
    
    function __construct() {
		parent::__construct();
		$this->exposeMethod('updateFieldPinnedStatus');
	}
    
    function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    
    public function updateFieldPinnedStatus(nectarcrm_Request $request) {
        $fieldId = $request->get('fieldid');
        $menuItemModel = Settings_nectarcrm_MenuItem_Model::getInstanceById($fieldId);
        
        $pin = $request->get('pin');
        if($pin == 'true') {
            $menuItemModel->markPinned();
        }else{
            $menuItemModel->unMarkPinned();
        }
        
	$response = new nectarcrm_Response();
	$response->setResult(array('SUCCESS'=>'OK'));
	$response->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}