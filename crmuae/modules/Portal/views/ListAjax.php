<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Portal_ListAjax_View extends nectarcrm_List_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('getRecordCount');
		$this->exposeMethod('getPageCount');
		$this->exposeMethod('getListViewCount');
	}

	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	function postProcess(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function getListViewCount(\nectarcrm_Request $request) {
		$listViewModel = new Portal_ListView_Model();
		$countResult = $listViewModel->getRecordCount();
		return $countResult;
	}

	public function getRecordCount(nectarcrm_Request $request) {

		$countResult = $this->getListViewCount($request);
		$result['count'] = $countResult;
		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function to get the page count for list
	 * @return total number of pages
	 */
	function getPageCount(nectarcrm_Request $request) {
		$listViewCount = $this->getListViewCount($request);
		$pagingModel = new nectarcrm_Paging_Model();
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $listViewCount / (int) $pageLimit);

		if ($pageCount == 0) {
			$pageCount = 1;
		}
		$result = array();
		$result['page'] = $pageCount;
		$result['numberOfRecords'] = $listViewCount;
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

}
