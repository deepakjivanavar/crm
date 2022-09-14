<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Users_PreferenceDetail_View extends nectarcrm_Detail_View {

	public function checkPermission(nectarcrm_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$record = $request->get('record');

		if($currentUserModel->isAdminUser() == true || $currentUserModel->get('id') == $record) {
			return true;
		} else {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * Function to returns the preProcess Template Name
	 * @param <type> $request
	 * @return <String>
	 */
	public function preProcessTplName(nectarcrm_Request $request) {
		return 'PreferenceDetailViewPreProcess.tpl';
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	public function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}

	public function preProcess(nectarcrm_Request $request, $display=true) {
		if($this->checkPermission($request)) {
			$qualifiedModuleName = $request->getModule(false);
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$recordId = $request->get('record');
			$moduleName = $request->getModule();
			$detailViewModel = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
			$recordModel = $detailViewModel->getRecord();
			$selectedModuleMenuCategory = 'MARKETING';

			$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
			$detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);

			$viewer->assign('MODULE_MODEL', $detailViewModel->getModule());
			$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

			$viewer->assign('IS_EDITABLE', $detailViewModel->getRecord()->isEditable($moduleName));
			$viewer->assign('IS_DELETABLE', $detailViewModel->getRecord()->isDeletable($moduleName));

			$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
			$linkModels = $detailViewModel->getSideBarLinks($linkParams);
			$viewer->assign('QUICK_LINKS', $linkModels);
			$viewer->assign('PAGETITLE', $this->getPageTitle($request));
			$viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
			$viewer->assign('STYLES',$this->getHeaderCss($request));
			$viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
			$viewer->assign('SEARCHABLE_MODULES', nectarcrm_Module_Model::getSearchableModules());

			$menuModelsList = nectarcrm_Menu_Model::getAll(true);
			$selectedModule = $request->getModule();
			$menuStructure = nectarcrm_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);

			// Order by pre-defined automation process for QuickCreate.
			uksort($menuModelsList, array('nectarcrm_MenuStructure_Model', 'sortMenuItemsByProcess'));

			$companyDetails = nectarcrm_CompanyDetails_Model::getInstanceById();
			$companyLogo = $companyDetails->getLogo();
			$viewer->assign('SELECTED_MENU_CATEGORY',$selectedModuleMenuCategory);
			$viewer->assign('CURRENTDATE', date('Y-n-j'));
			$viewer->assign('MODULE', $selectedModule);
			$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
			$viewer->assign('PARENT_MODULE', $request->get('parent'));
			$viewer->assign('VIEW', $request->get('view'));
			$viewer->assign('MENUS', $menuModelsList);
			$viewer->assign('QUICK_CREATE_MODULES', nectarcrm_Menu_Model::getAllForQuickCreate());
			$viewer->assign('MENU_STRUCTURE', $menuStructure);
			$viewer->assign('MENU_SELECTED_MODULENAME', $selectedModule);
			$viewer->assign('MENU_TOPITEMS_LIMIT', $menuStructure->getLimit());
			$viewer->assign('COMPANY_LOGO',$companyLogo);
			$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

			$homeModuleModel = nectarcrm_Module_Model::getInstance('Home');
			$viewer->assign('HOME_MODULE_MODEL', $homeModuleModel);
			$viewer->assign('HEADER_LINKS',$this->getHeaderLinks());
			$viewer->assign('ANNOUNCEMENT', $this->getAnnouncement());
			$viewer->assign('CURRENT_VIEW', $request->get('view'));
			$viewer->assign('SKIN_PATH', nectarcrm_Theme::getCurrentUserThemePath());
			$viewer->assign('CURRENT_USER_MODEL', $currentUser);
			$viewer->assign('LANGUAGE', $currentUser->get('language'));
			$viewer->assign('COMPANY_DETAILS_SETTINGS',new Settings_nectarcrm_CompanyDetails_Model());

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

			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

			$moduleFields = $moduleModel->getFields();
			foreach($moduleFields as $fieldName => $fieldModel){
				$fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
			}
			$viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));

			$activeBLock = Settings_nectarcrm_Module_Model::getActiveBlockName($request);
			$viewer->assign('ACTIVE_BLOCK', $activeBLock);

			if($display) {
				$this->preProcessDisplay($request);
			}
		}
	}

	protected function preProcessDisplay(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->view($this->preProcessTplName($request), $request->getModule());
	}

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);

		//This part is fetching picklist values from calendar settings
		$recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$dayStartPicklistValues = Users_Record_Model::getDayStartsPicklistValues($recordStructureInstance->getStructure());

		$viewer = $this->getViewer($request);
		$viewer->assign("DAY_STARTS", Zend_Json::encode($dayStartPicklistValues));
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		return parent::process($request);
	}

	public function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$moduleDetailFile = 'modules.'.$moduleName.'.resources.PreferenceDetail';
		unset($headerScriptInstances[$moduleDetailFile]);

		$jsFileNames = array(
			"modules.Users.resources.Detail",
			"modules.Users.resources.Users",
			'modules.'.$moduleName.'.resources.PreferenceDetail',
			'modules.'.$moduleName.'.resources.PreferenceEdit',
			'modules.Settings.nectarcrm.resources.Index',
			"~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.min.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

}
