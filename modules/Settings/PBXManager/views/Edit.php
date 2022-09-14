<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */
Class Settings_PBXManager_Edit_View extends nectarcrm_Edit_View {

     function __construct() {
        $this->exposeMethod('showPopup');
    }

    public function process(nectarcrm_Request $request) {
            $this->showPopup($request);
    }
    
    public function showPopup(nectarcrm_Request $request) {
        $id = $request->get('id');
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        if($id){
            $recordModel = Settings_PBXManager_Record_Model::getInstanceById($id, $qualifiedModuleName);
            $gateway = $recordModel->get('gateway');
        }else{
            $recordModel = Settings_PBXManager_Record_Model::getCleanInstance();
        }
        $viewer->assign('RECORD_ID', $id);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('MODULE', $request->getModule(false));
        $viewer->view('Edit.tpl', $request->getModule(false));
    }
    
}
