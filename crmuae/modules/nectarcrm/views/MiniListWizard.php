<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class nectarcrm_MiniListWizard_View extends nectarcrm_Index_View {

	function process (nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('WIZARD_STEP', $request->get('step'));

		switch ($request->get('step')) {
			case 'step1':
				$modules = nectarcrm_Module_Model::getSearchableModules();
				//Since comments is not treated as seperate module 
				unset($modules['ModComments']);
				$viewer->assign('MODULES', $modules);
				break;
			case 'step2':
				$selectedModule = $request->get('selectedModule');
				$filters = CustomView_Record_Model::getAllByGroup($selectedModule, false);
				$viewer->assign('ALLFILTERS', $filters);
				break;
			case 'step3':
				$selectedModule = $request->get('selectedModule');
				$filterid = $request->get('filterid');

				$db = PearDatabase::getInstance();
				$generator = new EnhancedQueryGenerator($selectedModule, $currentUser);
				$generator->initForCustomViewById($filterid);

				$listviewController = new ListViewController($db, $currentUser, $generator);
				$moduleFields = $generator->getModuleFields();
				$fields = $generator->getFields(); 
				$headerFields = array();
				foreach($fields as $fieldName) {
					if(array_key_exists($fieldName, $moduleFields)) {
						$fieldModel = $moduleFields[$fieldName];
						if($fieldModel->getPresence() == 1) {
							continue;
						}
						$headerFields[$fieldName] = $fieldModel;
					}
				}
				$viewer->assign('HEADER_FIELDS', $headerFields);
				$viewer->assign('LIST_VIEW_CONTROLLER', $listviewController);
				$viewer->assign('SELECTED_MODULE', $selectedModule);
				break;
		}

		$viewer->view('dashboards/MiniListWizard.tpl', $moduleName);
	}
}