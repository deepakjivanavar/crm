<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_Save_Action extends nectarcrm_Save_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Reports_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if (!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		$record = $request->get('record');
		if ($record) {
			$reportModel = Reports_Record_Model::getCleanInstance($record);
			if (!$reportModel->isEditable()) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		$record = $request->get('record');
		$reportModel = new Reports_Record_Model();
		$reportModel->setModule('Reports');
		if(!empty($record) && !$request->get('isDuplicate')) {
			$reportModel->setId($record);
		}

		$reporttype = $request->get('reporttype');
		if(empty($reporttype)) $reporttype='tabular';
		$reportModel->set('reportname', $request->get('reportname'));
		$reportModel->set('folderid', $request->get('folderid'));
		$reportModel->set('description', $request->get('reports_description'));
		$reportModel->set('reporttype', $reporttype);

		$reportModel->setPrimaryModule($request->get('primary_module'));

		$secondaryModules = $request->get('secondary_modules');
		$secondaryModules = implode(':', $secondaryModules);
		$reportModel->setSecondaryModule($secondaryModules);

		$reportModel->set('selectedFields', $request->get('selected_fields'));
		$reportModel->set('sortFields', $request->get('selected_sort_fields'));
		$reportModel->set('calculationFields', $request->get('selected_calculation_fields'));

		$reportModel->set('standardFilter', $request->get('standard_fiter'));
		$reportModel->set('advancedFilter', $request->get('advanced_filter'));
		$reportModel->set('advancedGroupFilterConditions', $request->get('advanced_group_condition'));
		$reportModel->set('members', $request->get('members'));

		$reportModel->save();

		//Scheduled Reports
		$scheduleReportModel = new Reports_ScheduleReports_Model();
		$scheduleReportModel->set('scheduleid', $request->get('schtypeid'));
		$scheduleReportModel->set('schtime', date('H:i', strtotime($request->get('schtime'))));
		$scheduleReportModel->set('schdate', $request->get('schdate'));
		$scheduleReportModel->set('schdayoftheweek', $request->get('schdayoftheweek'));
		$scheduleReportModel->set('schdayofthemonth', $request->get('schdayofthemonth'));
		$scheduleReportModel->set('schannualdates', $request->get('schannualdates'));
		$scheduleReportModel->set('reportid', $reportModel->getId());
		$scheduleReportModel->set('recipients', $request->get('recipients'));
		$scheduleReportModel->set('isReportScheduled', $request->get('enable_schedule'));
		$scheduleReportModel->set('specificemails', $request->get('specificemails'));
		$scheduleReportModel->set('fileformat', $request->get('fileformat'));
		$scheduleReportModel->saveScheduleReport();
		//END

		$loadUrl = $reportModel->getDetailViewUrl();
		header("Location: $loadUrl");
	}
}
