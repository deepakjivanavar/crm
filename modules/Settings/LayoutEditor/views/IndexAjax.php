<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_LayoutEditor_IndexAjax_View extends Settings_nectarcrm_IndexAjax_View {
	
	function __construct() {
		$this->exposeMethod('getFieldUI');
	}
    
    public function addBlock(nectarcrm_Request $request) {
        $moduleName = $request->get('sourceModule');
        $moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
        $blockList = $moduleModel->getBlocks();
        $qualifiedModuleName = $request->getModule(false);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('BLOCKS', $blockList);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        echo $viewer->view('AddBlock.tpl', $qualifiedModuleName,true);
    } 
    
    public function getFieldUI (nectarcrm_Request $request) {
        $fieldsList = $request->get('fieldIdList');
        $module = $request->get('sourceModule');
        $fieldModelList = Settings_LayoutEditor_Field_Model::getInstanceFromFieldId($fieldsList, getTabId($module));
        $viewer = $this->getViewer($request);
        $qualifiedModuleName = $request->getModule(false);
		$viewer->assign('SELECTED_MODULE_NAME', $module);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('FIELD_MODELS_LIST', $fieldModelList);
        $viewer->view('FieldUi.tpl',$qualifiedModuleName);
    }
    
}