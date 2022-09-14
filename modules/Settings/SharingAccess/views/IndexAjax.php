<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_SharingAccess_IndexAjax_View extends Settings_nectarcrm_IndexAjax_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showRules');
		$this->exposeMethod('editRule');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function showRules(nectarcrm_Request $request) {

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$forModule = $request->get('for_module');

		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		$ruleModelList = Settings_SharingAccess_Rule_Model::getAllByModule($moduleModel);

		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('FOR_MODULE', $forModule);
		$viewer->assign('RULE_MODEL_LIST', $ruleModelList);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		echo $viewer->view('ListRules.tpl', $qualifiedModuleName, true);
	}

	public function editRule(nectarcrm_Request $request) {

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$forModule = $request->get('for_module');
		$ruleId = $request->get('record');

		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		if($ruleId) {
			$ruleModel = Settings_SharingAccess_Rule_Model::getInstance($moduleModel, $ruleId);
		} else {
			$ruleModel = new Settings_SharingAccess_Rule_Model();
			$ruleModel->setModuleFromInstance($moduleModel);
		}

		$viewer->assign('ALL_RULE_MEMBERS', Settings_SharingAccess_RuleMember_Model::getAll());
		$viewer->assign('ALL_PERMISSIONS', Settings_SharingAccess_Rule_Model::$allPermissions);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('RULE_MODEL', $ruleModel);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		echo $viewer->view('EditRule.tpl', $qualifiedModuleName, true);
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
			'modules.Settings.nectarcrm.resources.Index',
			"modules.Settings.$moduleName.resources.Index"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}