<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Reports_ListViewQuickPreview_View extends nectarcrm_ListViewQuickPreview_View {

	function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$record = $request->get('record');

		$reportModel = Reports_Record_Model::getInstanceById($record);
		$reportChartModel = Reports_Chart_Model::getInstanceById($reportModel);
		$secondaryModules = $reportModel->getSecondaryModules();

		if (!$secondaryModules) {
			$viewer->assign('CLICK_THROUGH', true);
		} else {
			$viewer->assign('CLICK_THROUGH', false);
		}

		$data = $reportChartModel->getData();
		$viewer->assign('CHART_TYPE', $reportChartModel->getChartType());
		$viewer->assign('DATA', $data);
		$viewer->assign('REPORT_MODEL', $reportModel);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $moduleName);

		$viewer->view('ListViewQuickPreview.tpl', $moduleName);
	}

}
