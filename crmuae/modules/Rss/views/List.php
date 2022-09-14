<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Rss_List_View extends nectarcrm_Index_View {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	function preProcess(nectarcrm_Request $request, $display=true) {
		parent::preProcess($request);
	}

	function preProcessTplName(nectarcrm_Request $request) {
		return 'ListViewPreProcess.tpl';
	}

	function process (nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$this->initializeListViewContents($request, $viewer);

		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('ListViewContents.tpl', $moduleName);
	}

	function postProcess(nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$viewer->view('ListViewPostProcess.tpl', $moduleName);
		parent::postProcess($request);
	}
	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		$module = $request->getModule();
		$recordId = $request->get('id');
		$moduleModel = nectarcrm_Module_Model::getInstance($module);
		if($recordId) {
			$recordInstance = Rss_Record_Model::getInstanceById($recordId, $module);
		} else {
			$recordInstance = Rss_Record_Model::getCleanInstance($module);
			$recordInstance->getDefaultRss();
			$recordInstance = Rss_Record_Model::getInstanceById($recordInstance->getId(), $module);
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE',$module);
		$viewer->assign('RECORD',$recordInstance);
		$linkParams = array('MODULE'=>$module, 'ACTION'=>$request->get('view'));
		$viewer->assign('QUICK_LINKS',$moduleModel->getSideBarLinks($linkParams));
		$viewer->assign('LISTVIEW_HEADERS', $this->getListViewRssHeaders($module));
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
			'modules.nectarcrm.resources.List',
			"modules.$moduleName.resources.List",
			'modules.CustomView.resources.CustomView',
			"modules.$moduleName.resources.CustomView",
			"modules.Emails.resources.MassEdit",
			"modules.nectarcrm.resources.CkEditor",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/stickyTableHeaders/throttle.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/stickyTableHeaders/jquery.stickyheader.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

		/**
	 * Function to get the list view header
	 * @return <Array> - List of nectarcrm_Field_Model instances
	 */
	public function getListViewRssHeaders($module) {
		$headerFieldModels = array();
		$headerFields = array(
						'title' => array(
							'uitype' => '1',
							'name' => 'title',
							'label' => 'LBL_SUBJECT',
							'typeofdata' => 'V~O',
							'diplaytype' => '1',
						), 
						'sender' => array(
							'uitype' => '1',
							'name' => 'sender',
							'label' => 'LBL_SENDER',
							'typeofdata' => 'V~O',
							'diplaytype' => '1',
						)
		);
		foreach ($headerFields as $fieldName => $fieldDetails) {
				$fieldModel = Settings_Webforms_Field_Model::getInstanceByRow($fieldDetails);
				$fieldModel->module = $module;
				$fieldModelsList[$fieldName] = $fieldModel;
		}
		return $fieldModelsList;
	}
}