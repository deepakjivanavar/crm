<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_MenuEditor_Index_View extends Settings_nectarcrm_Index_View {

	public function process(nectarcrm_Request $request) {
		$allModelsList = nectarcrm_Menu_Model::getAll(true);
		$menuModelStructure = nectarcrm_MenuStructure_Model::getInstanceFromMenuList($allModelsList);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$viewer = $this->getViewer($request);
		$viewer->assign('ALL_MODULES', $menuModelStructure->getMore());
		$viewer->assign('SELECTED_MODULES', $menuModelStructure->getTop());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);

		$mappedModuleList = Settings_MenuEditor_Module_Model::getAllVisibleModules();
		$viewer->assign('APP_MAPPED_MODULES', $mappedModuleList);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

		$viewer->view('Index.tpl', $qualifiedModuleName);
	}
}
