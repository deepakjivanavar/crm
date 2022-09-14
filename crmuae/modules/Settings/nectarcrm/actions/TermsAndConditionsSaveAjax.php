<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_nectarcrm_TermsAndConditionsSaveAjax_Action extends Settings_nectarcrm_Basic_Action {
    
    public function process(nectarcrm_Request $request) {
        $model = Settings_nectarcrm_TermsAndConditions_Model::getInstance();
        $model->setText($request->get('tandc'));
        $model->save();
        
        $response = new nectarcrm_Response();
        $response->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) { 
        $request->validateWriteAccess(); 
    } 
}