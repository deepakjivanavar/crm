<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MenuEditor_Save_Action extends Settings_nectarcrm_Index_Action {

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule(false);
		$menuEditorModuleModel = Settings_nectarcrm_Module_Model::getInstance($moduleName);
		$selectedModulesList = $request->get('selectedModulesList');

		if ($selectedModulesList) {
			$menuEditorModuleModel->set('selectedModulesList', $selectedModulesList);
			$menuEditorModuleModel->saveMenuStruncture();
		}
		$loadUrl = $menuEditorModuleModel->getIndexViewUrl();
		header("Location: $loadUrl");
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}
