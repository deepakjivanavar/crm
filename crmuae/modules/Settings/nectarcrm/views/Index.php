<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_Index_View extends nectarcrm_Basic_View {

	function __construct() {
		parent::__construct();
	}

	function checkPermission(nectarcrm_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if(!$currentUserModel->isAdminUser()) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'nectarcrm'));
		}
	}

	public function preProcess (nectarcrm_Request $request, $display=true) {
		parent::preProcess($request, false);
		$this->preProcessSettings($request,$display);
	}

	public function preProcessSettings (nectarcrm_Request $request ,$display=true) {

		$viewer = $this->getViewer($request);

		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$selectedMenuId = $request->get('block');
		$fieldId = $request->get('fieldid');
		$settingsModel = Settings_nectarcrm_Module_Model::getInstance();
		$menuModels = $settingsModel->getMenus();

		if(!empty($selectedMenuId)) {
			$selectedMenu = Settings_nectarcrm_Menu_Model::getInstanceById($selectedMenuId);
		} elseif(!empty($moduleName) && $moduleName != 'nectarcrm') {
			$fieldItem = Settings_nectarcrm_Index_View::getSelectedFieldFromModule($menuModels,$moduleName);
			if($fieldItem){
				$selectedMenu = Settings_nectarcrm_Menu_Model::getInstanceById($fieldItem->get('blockid'));
				$fieldId = $fieldItem->get('fieldid');
			} else {
				reset($menuModels);
				$firstKey = key($menuModels);
				$selectedMenu = $menuModels[$firstKey];
			}
		} else {
			reset($menuModels);
			$firstKey = key($menuModels);
			$selectedMenu = $menuModels[$firstKey];
		}

		$settingsMenItems = array();
		foreach($menuModels as $menuModel) {
			$menuItems = $menuModel->getMenuItems();
			foreach($menuItems as $menuItem) {
				$settingsMenItems[$menuItem->get('name')] = $menuItem;
			}
		}
		$viewer->assign('SETTINGS_MENU_ITEMS', $settingsMenItems);

		$activeBLock = Settings_nectarcrm_Module_Model::getActiveBlockName($request);
		$viewer->assign('ACTIVE_BLOCK', $activeBLock);

		$restrictedModules = array('nectarcrm', 'ExtensionStore', 'CustomerPortal', 'Roles', 'ExchangeConnector', 'LoginHistory', 'SharingAccess');

		if(!in_array($moduleName, $restrictedModules)) {
			if($moduleName === 'Users') {
				$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			}else {
				$moduleModel = Settings_nectarcrm_Module_Model::getInstance($qualifiedModuleName);
			}
			$this->setModuleInfo($request, $moduleModel);
		}

		$viewer->assign('SELECTED_FIELDID',$fieldId);
		$viewer->assign('SELECTED_MENU', $selectedMenu);
		$viewer->assign('SETTINGS_MENUS', $menuModels);
		$viewer->assign('MODULE', $moduleName);

		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	protected function preProcessTplName(nectarcrm_Request $request) {
		return 'SettingsMenuStart.tpl';
	}

	public function postProcessSettings (nectarcrm_Request $request) {

		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$viewer->view('SettingsMenuEnd.tpl', $qualifiedModuleName);
	}

	public function postProcess (nectarcrm_Request $request) {
		$this->postProcessSettings($request);
		parent::postProcess($request);
	}

	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$usersCount = Users_Record_Model::getCount(true);
		$activeWorkFlows = Settings_Workflows_Module_Model::getActiveWorkflowCount();
		$activeModules = Settings_ModuleManager_Module_Model::getModulesCount(true);
		$pinnedSettingsShortcuts = Settings_nectarcrm_MenuItem_Model::getPinnedItems();

		$viewer->assign('USERS_COUNT',$usersCount);
		$viewer->assign('ACTIVE_WORKFLOWS',$activeWorkFlows);
		$viewer->assign('ACTIVE_MODULES',$activeModules);
		$viewer->assign('SETTINGS_SHORTCUTS',$pinnedSettingsShortcuts);
		$viewer->assign('MODULE',$qualifiedModuleName);
		$viewer->view('Index.tpl', $qualifiedModuleName);
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
			'modules.nectarcrm.resources.nectarcrm',
			'modules.Settings.nectarcrm.resources.nectarcrm',
			'modules.Settings.nectarcrm.resources.Edit',
			"modules.Settings.$moduleName.resources.$moduleName",
			'modules.Settings.nectarcrm.resources.Index',
			"modules.Settings.$moduleName.resources.Index",
			"~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public static function getSelectedFieldFromModule($menuModels,$moduleName) {
		if($menuModels) {
			foreach($menuModels  as $menuModel) {
				$menuItems = $menuModel->getMenuItems();
				foreach($menuItems as $item) {
					$linkTo = $item->getUrl();
					if(stripos($linkTo, '&module='.$moduleName) !== false || stripos($linkTo, '?module='.$moduleName) !== false) {
						return $item;
					}
				}
			}
		}
		return false;
	}


	/**
	 * Setting module related Information to $viewer (for nectarcrm7)
	 * @param type $request
	 * @param type $moduleModel
	 */
	public function setModuleInfo($request, $moduleModel){
		$fieldsInfo = array();
		$basicLinks = array();
		$viewer = $this->getViewer($request);

		if(method_exists($moduleModel, 'getFields')) {
			$moduleFields = $moduleModel->getFields();
			foreach($moduleFields as $fieldName => $fieldModel){
				$fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
			}
			$viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
		}

		if(method_exists($moduleModel, 'getModuleBasicLinks')) {
			$moduleBasicLinks = $moduleModel->getModuleBasicLinks();
			foreach($moduleBasicLinks as $basicLink){
				$basicLinks[] = nectarcrm_Link_Model::getInstanceFromValues($basicLink);
			}
			$viewer->assign('MODULE_BASIC_ACTIONS', $basicLinks);
		}
	}

	public function getPageTitle(nectarcrm_Request $request) {
		$pageTitle = parent::getPageTitle($request);

		if ($pageTitle == 'nectarcrm') {
			$pageTitle = vtranslate($request->get('parent'), $request->getModule());
		}
		return $pageTitle;
	}
}
