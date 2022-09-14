<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Reports_ExportReport_View extends nectarcrm_View_Controller {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('GetPrintReport');
		$this->exposeMethod('GetXLS');
		$this->exposeMethod('GetCSV');
	}

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Reports_Module_Model::getInstance($moduleName);

		$record = $request->get('record');
		$reportModel = Reports_Record_Model::getCleanInstance($record);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	function preProcess(nectarcrm_Request $request) {
		return false;
	}

	function postProcess(nectarcrm_Request $request) {
		return false;
	}

	function process(nectarcrm_request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
		}
	}

	/**
	 * Function exports the report in a Excel sheet
	 * @param nectarcrm_Request $request
	 */
	function GetXLS(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$reportModel = Reports_Record_Model::getInstanceById($recordId);
        $reportModel->set('advancedFilter', $request->get('advanced_filter'));
		$reportModel->getReportXLS($request->get('source'));
	}

	/**
	 * Function exports report in a CSV file
	 * @param nectarcrm_Request $request
	 */
	function GetCSV(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$reportModel = Reports_Record_Model::getInstanceById($recordId);
        $reportModel->set('advancedFilter', $request->get('advanced_filter'));
		$reportModel->getReportCSV($request->get('source'));
	}

	/**
	 * Function displays the report in printable format
	 * @param nectarcrm_Request $request
	 */
	function GetPrintReport(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$recordId = $request->get('record');
		$reportModel = Reports_Record_Model::getInstanceById($recordId);
        $reportModel->set('advancedFilter', $request->get('advanced_filter'));
		$printData = $reportModel->getReportPrint();

		$viewer->assign('REPORT_NAME', $reportModel->getName());
		$viewer->assign('PRINT_DATA', $printData['data'][0]);
		$viewer->assign('TOTAL', $printData['total']);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('ROW', $printData['data'][1]);

		$viewer->view('PrintReport.tpl', $moduleName);
	}
}