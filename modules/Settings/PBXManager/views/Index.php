<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_PBXManager_Index_View extends Settings_nectarcrm_Index_View{
    
    function __construct() {
        $this->exposeMethod('gatewayInfo');
    }

    public function process(nectarcrm_Request $request) {
        $this->gatewayInfo($request);
    }
    
    public function gatewayInfo(nectarcrm_Request $request){
        $recordModel = Settings_PBXManager_Record_Model::getInstance();
        $moduleModel = Settings_PBXManager_Module_Model::getCleanInstance();
        $viewer = $this->getViewer($request);  
        
        $viewer->assign('RECORD_ID', $recordModel->get('id'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MODULE', $request->getModule(false));
        $viewer->assign('QUALIFIED_MODULE', $request->getModule(false));
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->view('index.tpl', $request->getModule(false));
    }


}
