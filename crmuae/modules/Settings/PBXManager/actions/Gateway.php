<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Settings_PBXManager_Gateway_Action extends Settings_nectarcrm_IndexAjax_View{
    
    function __construct() {
        $this->exposeMethod('getSecretKey');
    }
    
    public function process(nectarcrm_Request $request) {
        $this->getSecretKey($request);
    }
    
    public function getSecretKey(nectarcrm_Request $request) {
        $serverModel = PBXManager_Server_Model::getInstance();
        $response = new nectarcrm_Response();
        $nectarcrmsecretkey = $serverModel->get('nectarcrmsecretkey');
        if($nectarcrmsecretkey) {
            $connector = $serverModel->getConnector();
            $nectarcrmsecretkey = $connector->getnectarcrmSecretKey();
            $response->setResult($nectarcrmsecretkey);
        }else {
            $nectarcrmsecretkey = PBXManager_Server_Model::generatenectarcrmSecretKey();
            $response->setResult($nectarcrmsecretkey);
        }
        $response->emit();
    }
}
