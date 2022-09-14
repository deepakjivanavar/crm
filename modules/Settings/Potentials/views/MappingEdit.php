<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Potentials_MappingEdit_View extends Settings_nectarcrm_Index_View {

	public function process(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$viewer = $this->getViewer($request);
		
		$viewer->assign('MODULE_MODEL', Settings_Potentials_Mapping_Model::getInstance());
		$viewer->assign('POTENTIALS_MODULE_MODEL', Settings_Potentials_Module_Model::getInstance('Potentials'));
		$viewer->assign('PROJECT_MODULE_MODEL', Settings_Potentials_Module_Model::getInstance('Project'));

		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('RESTRICTED_FIELD_IDS_LIST', Settings_Potentials_Mapping_Model::getRestrictedFieldIdsList());
		$viewer->view('PotentialMappingEdit.tpl', $qualifiedModuleName);
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
			"modules.Settings.$moduleName.resources.PotentialMapping"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}