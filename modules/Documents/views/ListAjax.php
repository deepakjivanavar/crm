<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Documents_ListAjax_View extends Documents_List_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getRecordsCount');
		$this->exposeMethod('getPageCount');
		$this->exposeMethod('showSearchResults');
		$this->exposeMethod('ShowListColumnsEdit');
	}

	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	function postProcess(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Extending nectarcrm List Ajax API to show Advance Search results
	 * @param nectarcrm_Request $request
	 */
	public function showSearchResults(nectarcrm_Request $request) {
		$nectarcrmListAjaxInstance = new nectarcrm_ListAjax_View();
		$nectarcrmListAjaxInstance->showSearchResults($request);
	}

	/**
	 * Extending nectarcrm List Ajax API to show List Columns Edit view
	 * @param nectarcrm_Request $request
	 */
	public function ShowListColumnsEdit(nectarcrm_Request $request){
		$nectarcrmListAjaxInstance = new nectarcrm_ListAjax_View();
		$nectarcrmListAjaxInstance->ShowListColumnsEdit($request);
	}
}