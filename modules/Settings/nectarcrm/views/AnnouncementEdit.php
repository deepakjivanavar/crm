<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_AnnouncementEdit_View extends Settings_nectarcrm_Index_View {
    
    public function process(nectarcrm_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $annoucementModel = Settings_nectarcrm_Announcement_Model::getInstanceByCreator($currentUser);
        
        $qualifiedModuleName = $request->getModule(false);
        
        $viewer = $this->getViewer($request);
		
        $viewer->assign('ANNOUNCEMENT',$annoucementModel);
        $viewer->view('Announcement.tpl',$qualifiedModuleName);
    }
	
	function getPageTitle(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		return vtranslate('LBL_ANNOUNCEMENT',$qualifiedModuleName);
	}
	
	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.Settings.$moduleName.resources.Announcement",
            "modules.Settings.$moduleName.resources.AnnouncementEdit"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}