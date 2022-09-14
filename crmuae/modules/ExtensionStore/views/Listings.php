<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class ExtensionStore_Listings_View extends nectarcrm_Index_View {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('getPromotions');
	}

	public function getHeaderScripts(nectarcrm_Request $request) {
		$jsFileNames = array(
			"libraries.jquery.boxslider.jqueryBxslider",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function to get news listings by passing type as News
	 */
	protected function getPromotions(nectarcrm_Request $request) {
		$modelInstance = Settings_ExtensionStore_Extension_Model::getInstance();
		$promotions = $modelInstance->getListings(null, 'Promotion');
		$qualifiedModuleName = $request->getModule(false);

		$viewer = $this->getViewer($request);
		$viewer->assign('PROMOTIONS', $promotions);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('HEADER_SCRIPTS', $this->getHeaderScripts($request));
		$viewer->view('Promotions.tpl', $qualifiedModuleName);
	}

}
