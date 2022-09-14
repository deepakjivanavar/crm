<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Tags_List_View extends Settings_nectarcrm_List_View {

	function checkPermission(nectarcrm_Request $request) {
		$layout = nectarcrm_Viewer::getDefaultLayoutName();
		if ($layout == 'vlayout') {
			throw new AppException(vtranslate('LBL_NOT_ACCESSIBLE'));
		}
		return true;
	}

	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		parent::initializeListViewContents($request, $viewer);
		$viewer->assign('SHOW_LISTVIEW_CHECKBOX', false);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$jsFileNames = array(
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
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}
}