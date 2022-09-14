<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_PickListDependency_List_View extends Settings_nectarcrm_List_View {

	public function preProcess(nectarcrm_Request $request, $display = true) {
		$moduleModelList = Settings_PickListDependency_Module_Model::getPicklistSupportedModules();
		$forModule = $request->get('formodule');
		$viewer = $this->getViewer($request);
		$viewer->assign('PICKLIST_MODULES_LIST',$moduleModelList);
		$viewer->assign('FOR_MODULE',$forModule);
		parent::preProcess($request, $display);
	}

	public function process(nectarcrm_Request $request) {
	   if($request->isAjax()) {
			$moduleModelList = Settings_PickListDependency_Module_Model::getPicklistSupportedModules();
			$forModule = $request->get('formodule');

			$viewer = $this->getViewer($request);
			$viewer->assign('PICKLIST_MODULES_LIST',$moduleModelList);
			$viewer->assign('FOR_MODULE',$forModule);

			$this->initializeListViewContents($request, $viewer);
			$viewer->view('ListViewHeader.tpl', $request->getModule(false));
	   }
	   parent::process($request);
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
			'~libraries/jquery/malihu-custom-scrollbar/js/jquery.mCustomScrollbar.concat.min.js',
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);

		$cssFileNames = array(
			'~/libraries/jquery/malihu-custom-scrollbar/css/jquery.mCustomScrollbar.css',
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		parent::initializeListViewContents($request, $viewer);
		$viewer->assign('SHOW_LISTVIEW_CHECKBOX', false);
		$viewer->assign('LISTVIEW_ACTIONS_ENABLED', true);
	}
}