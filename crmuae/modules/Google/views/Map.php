<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Map_View extends nectarcrm_Detail_View {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		return true;
	}

	/**
	 * must be overriden
	 * @param nectarcrm_Request $request
	 * @return boolean 
	 */
	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	/**
	 * must be overriden
	 * @param nectarcrm_Request $request
	 * @return boolean 
	 */
	function postProcess(nectarcrm_Request $request) {
		return true;
	}

	/**
	 * called when the request is recieved.
	 * if viewtype : detail then show location
	 * TODO : if viewtype : list then show the optimal route.
	 * @param nectarcrm_Request $request 
	 */
	function process(nectarcrm_Request $request) {
		switch ($request->get('viewtype')) {
			case 'detail':$this->showLocation($request);
				break;
			default:break;
		}
	}

	/**
	 * display the template.
	 * @param nectarcrm_Request $request 
	 */
	function showLocation(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		// record and source_module values to be passed to populate the values in the template,
		// required to get the respective records address based on the module type.
		$viewer->assign('RECORD', $request->get('record'));
		$viewer->assign('SOURCE_MODULE', $request->get('source_module'));
		$viewer->view('map.tpl', $request->getModule());
	}

}