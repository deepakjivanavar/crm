<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Services_Detail_View extends Products_Detail_View {

	public function getHeaderScripts(nectarcrm_Request $request) {
		parent::getHeaderScripts($request);
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$modulePopUpFile = 'modules.'.$moduleName.'.resources.Edit';
		$moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
		$moduleRelatedListFile = 'modules.'.$moduleName.'.resources.RelatedList';
		unset($headerScriptInstances[$modulePopUpFile]);
		unset($headerScriptInstances[$moduleDetailFile]);
		unset($headerScriptInstances[$moduleRelatedListFile]);

		$jsFileNames = array(
				'modules.Products.resources.Edit',
				'modules.Products.resources.Detail',
				'modules.Products.resources.RelatedList',
		);
		$jsFileNames[] = $modulePopUpFile;
		$jsFileNames[] = $moduleDetailFile;
		$jsFileNames[] = $moduleRelatedListFile;

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getOverlayHeaderScripts(nectarcrm_Request $request){
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.PriceBooks.resources.Detail",
			"modules.Products.resources.Detail",
			"modules.$moduleName.resources.Detail",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;	
	}
}
