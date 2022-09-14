<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

abstract class nectarcrm_Footer_View extends nectarcrm_Header_View {

	function __construct() {
		parent::__construct();
	}

	//Note: To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	/*function preProcessParentTplName(nectarcrm_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	/*function postProcess(nectarcrm_Request $request) {
		parent::postProcess($request);
	}*/
       public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
            '~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/timepicker/jquery.timepicker.css',
            '~/libraries/jquery/lazyYT/lazyYT.min.css'
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
}
