<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_OutgoingServerSaveAjax_Action extends Settings_nectarcrm_Basic_Action {
    
    public function process(nectarcrm_Request $request) {
        $outgoingServerSettingsModel = Settings_nectarcrm_Systems_Model::getInstanceFromServerType('email', 'OutgoingServer');
        $loadDefaultSettings = $request->get('default');
        if($loadDefaultSettings == "true") {
            $outgoingServerSettingsModel->loadDefaultValues();
        }else{
            $outgoingServerSettingsModel->setData($request->getAll());
        }
        $response = new nectarcrm_Response();
        try{
            if ($loadDefaultSettings == "true") {
                $response->setResult('OK');
            } else {
                $id = $outgoingServerSettingsModel->save($request);
                $data = $outgoingServerSettingsModel->getData();
                $response->setResult($data);
            }
        }catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}
