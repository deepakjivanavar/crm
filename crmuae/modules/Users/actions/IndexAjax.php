<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Users_IndexAjax_Action extends nectarcrm_BasicAjax_Action {
    
    function __construct() {
		parent::__construct();
		$this->exposeMethod('toggleLeftPanel');
	}
    
    function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    
    public function toggleLeftPanel (nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$recordModel = nectarcrm_Record_Model::getInstanceById($currentUser->getId(), 'Users');
        $recordModel->set('leftpanelhide',$request->get('showPanel'));
        $recordModel->leftpanelhide = $request->get('showPanel');
        $recordModel->set('mode','edit');
	
        $response = new nectarcrm_Response();
        try{
            $recordModel->save();
            $response->setResult(array('success'=>true));
        }catch(Exception $e){
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
}