<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Settings_nectarcrm_Extension_View extends Settings_nectarcrm_Index_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->get('extensionModule');

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if (empty($moduleModel)) {
			throw new AppException(vtranslate('LBL_HANDLER_NOT_FOUND'));
		}

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if (!$permission) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}

		return true;
	}

	/**
	 * Function to get extension view instance from view name and module
	 * @param <nectarcrm_Request> $request
	 * @return $extensionViewClass
	 */
	function getExtensionViewInstance(nectarcrm_Request $request) {
		$extensionModule = $request->get('extensionModule');
		$extensionView = $request->get('extensionView');
		$extensionViewClass = nectarcrm_Loader::getComponentClassName('view', $extensionView, $extensionModule);
		$extensionViewInstance = new $extensionViewClass();

		return $extensionViewInstance;
	}

	/**
	 * Function to get the extension links for a module
	 * @param <string> $module
	 * @return <array> $links
	 */
	static function getExtensionLinks($module) {
		$linkParams = array('MODULE' => $module, 'ACTION' => 'LIST');
		$links = nectarcrm_Link_Model::getAllByType(getTabid($module), array('EXTENSIONLINK'), $linkParams);

		return $links['EXTENSIONLINK'];
	}

	function preProcess(nectarcrm_Request $request, $display = true) {
		parent::preProcess($request, false);
		$viewer = $this->getViewer($request);
		$viewer->assign('EXTENSION_MODULE', $request->get('extensionModule'));
		$viewer->assign('EXTENSION_VIEW', $request->get('extensionView'));
		$viewer->assign('VIEWID', '');

		if ($display) {
			$this->preProcessDisplay($request);
		}
	}

	function process(nectarcrm_Request $request) {
		$extensionViewInstance = $this->getExtensionViewInstance($request);
		$extensionViewInstance->process($request);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$extensionViewInstance = $this->getExtensionViewInstance($request);

		$jsFileNames = array(
			'modules.nectarcrm.resources.Extension',
			'modules.nectarcrm.resources.ExtensionCommon',
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

		$jsScriptInstances = $extensionViewInstance->getHeaderScripts($request);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

}
