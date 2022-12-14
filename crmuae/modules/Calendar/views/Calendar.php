<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_Calendar_View extends nectarcrm_Index_View {

	public function preProcess(nectarcrm_Request $request, $display = true) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$viewer->assign('MODULE_NAME', $moduleName);
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$viewer->assign('IS_CREATE_PERMITTED', $moduleModel->isPermitted('CreateView'));
		$viewer->assign('IS_MODULE_EDITABLE', $moduleModel->isPermitted('EditView'));
		$viewer->assign('IS_MODULE_DELETABLE', $moduleModel->isPermitted('Delete'));

		parent::preProcess($request, false);
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	protected function preProcessTplName(nectarcrm_Request $request) {
		return 'CalendarViewPreProcess.tpl';
	}

	public function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$jsFileNames = array(
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/fullcalendar/lib/moment.min.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/fullcalendar/fullcalendar.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/webui-popover/dist/jquery.webui-popover.js",
			"modules.Calendar.resources.CalendarView",
			"~/libraries/jquery/colorpicker/js/colorpicker.js"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);

		$cssFileNames = array(
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/fullcalendar/fullcalendar.css',
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/fullcalendar/fullcalendar-bootstrap.css',
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/webui-popover/dist/jquery.webui-popover.css',
			'~/libraries/jquery/colorpicker/css/colorpicker.css'
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if($mode == 'settings'){
			$this->getCalendarSettings($request);
		}
		$viewer = $this->getViewer($request);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if($request->getMode() == 'Settings'){
			return $this->getCalendarSettings($request);
		}
		$viewer->assign('CURRENT_USER', $currentUserModel);
		$viewer->assign('IS_CREATE_PERMITTED', isPermitted('Calendar', 'CreateView'));

		$viewer->view('CalendarView.tpl', $request->getModule());
	}

	/*
	 * Function to get the calendar settings view
	 */
	public function getCalendarSettings(nectarcrm_Request $request){

		$viewer = $this->getViewer($request);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$module = $request->getModule();
		$detailViewModel = nectarcrm_DetailView_Model::getInstance('Users', $currentUserModel->id);
		$userRecordStructure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($detailViewModel->getRecord(), nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$recordStructure = $userRecordStructure->getStructure();
		$allUsers = Users_Record_Model::getAll(true);
		$sharedUsers = Calendar_Module_Model::getCaledarSharedUsers($currentUserModel->id);
		$sharedType = Calendar_Module_Model::getSharedType($currentUserModel->id);
		$dayStartPicklistValues = Users_Record_Model::getDayStartsPicklistValues($recordStructure);

		$hourFormatFeildModel = $recordStructure['LBL_CALENDAR_SETTINGS']['hour_format'];

		$viewer->assign('CURRENTUSER_MODEL',$currentUserModel);
		$viewer->assign('SHAREDUSERS', $sharedUsers);
		$viewer->assign("DAY_STARTS", Zend_Json::encode($dayStartPicklistValues));
		$viewer->assign('ALL_USERS',$allUsers);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('MODULE',$module);
		$viewer->assign('RECORD', $currentUserModel->id);
		$viewer->assign('SHAREDTYPE', $sharedType);
		$viewer->assign('HOUR_FORMAT_VALUE', $hourFormatFeildModel->get('fieldvalue'));

		$viewer->view('CalendarSettings.tpl', $request->getModule());
	}


}