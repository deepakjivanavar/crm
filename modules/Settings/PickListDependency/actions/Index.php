<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_PickListDependency_Index_Action extends Settings_nectarcrm_Basic_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkCyclicDependency');
    }
   
    public function checkCyclicDependency(nectarcrm_Request $request) {
        $module = $request->get('sourceModule');
        $moduleModel = nectarcrm_Module_Model::getInstance($module);
        $sourceField = $request->get('sourcefield');
        $targetField = $request->get('targetfield');
        $result = nectarcrm_DependencyPicklist::checkCyclicDependency($module, $sourceField, $targetField);
        if($result) {
            $currentSourceField = nectarcrm_DependencyPicklist::getPicklistSourceField($module, $sourceField, $targetField);
            $currentSourceFieldModel = nectarcrm_Field_Model::getInstance($currentSourceField, $moduleModel);
            $targetFieldModel = nectarcrm_Field_Model::getInstance($targetField, $moduleModel);
            $errorMessage = vtranslate('LBL_CYCLIC_DEPENDENCY_ERROR', $request->getModule(false));
            $message = sprintf($errorMessage, '"'.vtranslate($currentSourceFieldModel->get('label'), $module).'"', '"'.vtranslate($targetFieldModel->get('label'), $module).'"');
        }
        $response = new nectarcrm_Response();
        $response->setResult(array('result'=>$result, 'message' => $message));
        $response->emit();
    }
}