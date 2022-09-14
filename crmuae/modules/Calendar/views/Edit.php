<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class Calendar_Edit_View extends nectarcrm_Edit_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('Events');
		$this->exposeMethod('Calendar');
	}

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$actionName = 'CreateView';
		if ($record && !$request->get('isDuplicate')) {
			$actionName = 'EditView';
		}

		if(!Users_Privileges_Model::isPermitted($moduleName, $actionName, $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if ($record) {
			$activityModulesList = array('Calendar', 'Events');
			$recordEntityName = getSalesEntityType($record);

			if (!in_array($recordEntityName, $activityModulesList) || !in_array($moduleName, $activityModulesList)) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->getMode();

		$recordId = $request->get('record');
		if(!empty($recordId)) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId);
			$mode = $recordModel->getType();
		}

		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request, $mode);
			return;
		}
		$this->Calendar($request, 'Calendar');
	}

	function Events($request, $moduleName) {
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$viewer = $this->getViewer ($request);
		$record = $request->get('record');

		 if(!empty($record) && $request->get('isDuplicate') == true) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', '');
		}else if(!empty($record)) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', 'edit');
			$viewer->assign('RECORD_ID', $record);
		} else {
			$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
			$viewer->assign('MODE', '');
		}
		$eventModule = nectarcrm_Module_Model::getInstance($moduleName);
		$recordModel->setModuleFromInstance($eventModule);

		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAllPurified(), $fieldList);

		$relContactId = $request->get('contact_id');
		if ($relContactId) {
			$contactRecordModel = nectarcrm_Record_Model::getInstanceById($relContactId);
			$accountId = $contactRecordModel->get('account_id');
			if ($accountId) {
				$requestFieldList['parent_id'] = $accountId;
			}
		}
		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			$specialField = false;
			// We collate date and time part together in the EditView UI handling 
			// so a bit of special treatment is required if we come from QuickCreate 
			if (empty($record) && ($fieldName == 'time_start' || $fieldName == 'time_end') && !empty($fieldValue)) { 
				$specialField = true; 
				// Convert the incoming user-picked time to GMT time 
				// which will get re-translated based on user-time zone on EditForm 
				$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i"); 
			} 
			if (empty($record) && ($fieldName == 'date_start' || $fieldName == 'due_date') && !empty($fieldValue)) { 
				if($fieldName == 'date_start'){
					$startTime = nectarcrm_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
					$startDateTime = nectarcrm_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
					list($startDate, $startTime) = explode(' ', $startDateTime);
					$fieldValue = nectarcrm_Date_UIType::getDisplayDateValue($startDate);
				}else{
					$endTime = nectarcrm_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_end']);
					$endDateTime = nectarcrm_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$endTime);
					list($endDate, $endTime) = explode(' ', $endDateTime);
					$fieldValue = nectarcrm_Date_UIType::getDisplayDateValue($endDate);
				}
			}

			if($fieldModel->isEditable() || $specialField) { 
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		$recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel,
									nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

		$viewMode = $request->get('view_mode');
		if(!empty($viewMode)) {
			$viewer->assign('VIEW_MODE', $viewMode);
		}

		$userChangedEndDateTime = $request->get('userChangedEndDateTime');
		if(!empty($record) && $request->get('isDuplicate') != true) {
			$userChangedEndDateTime = 1;
		}
		//If followup value is passed from request to process the value and sent to client
		$requestFollowUpDate = $request->get('followup_date_start');
		$requestFollowUpTime = $request->get('followup_time_start');
		$followUpStatus = $request->get('followup');
		$eventStatus = $request->get('eventstatus');

		if(!empty($requestFollowUpDate)){
			$followUpDate = $requestFollowUpDate;
		}
		if(!empty($requestFollowUpTime)){
			$followUpTime = $requestFollowUpTime;
		}
		if($followUpStatus == 'on'){
			$viewer->assign('FOLLOW_UP_STATUS',TRUE);
		}
		if($eventStatus == 'Held'){
			$viewer->assign('SHOW_FOLLOW_UP',TRUE);
		}else{
			$viewer->assign('SHOW_FOLLOW_UP',FALSE);
		}

		$remainder = $request->get('set_reminder');
		if($remainder){
			$remainderValues[0]=$request->get('remdays');
			$remainderValues[1]=$request->get('remhrs');
			$remainderValues[2]=$request->get('remmin');
			$viewer->assign('REMINDER_VALUES',$remainderValues);
		}

		$viewer->assign('USER_CHANGED_END_DATE_TIME',$userChangedEndDateTime);
		$viewer->assign('FOLLOW_UP_DATE',$followUpDate);
		$viewer->assign('FOLLOW_UP_TIME',$followUpTime);
		$viewer->assign('RECURRING_INFORMATION', $recordModel->getRecurrenceInformation($request));
		$viewer->assign('TOMORROWDATE', nectarcrm_Date_UIType::getDisplayDateValue(date('Y-m-d', time()+86400)));

		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$existingRelatedContacts = $recordModel->getRelatedContactInfo();

		//To add contact ids that is there in the request . Happens in gotoFull form mode of quick create
		$requestContactIdValue = $request->get('contact_id');
		if(!empty($requestContactIdValue)) {
			$existingRelatedContacts[] = array('name' => decode_html(nectarcrm_Util_Helper::getRecordName($requestContactIdValue)) ,'id' => $requestContactIdValue);
		}
		//If already selected contact ids, then in gotoFull form should show those selected contact ids
		$idsList = $request->get('contactidlist');
		if(!empty($idsList)) {
			$contactIdsList = explode (';', $idsList);
			foreach($contactIdsList as $contactId) {
				$existingRelatedContacts[] = array('name' => decode_html(nectarcrm_Util_Helper::getRecordName($contactId)) ,'id' => $contactId);
			}
		}

		$viewer->assign('RELATED_CONTACTS', $existingRelatedContacts);

		$isRelationOperation = $request->get('relationOperation');

		//if it is relation edit
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}
		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$accessibleUsers = $currentUser->getAccessibleUsers();

		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',nectarcrm_Functions::jsonEncode($picklistDependencyDatasource));
		$viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
		$viewer->assign('INVITIES_SELECTED', $recordModel->getInvities());
		$viewer->assign('CURRENT_USER', $currentUser);

		// added to set the return values
		if($request->get('returnview')) {
			$request->setViewerReturnValues($viewer);
		}   

		if($request->get('displayMode')=='overlay'){
			$viewer->assign('SCRIPTS',$this->getOverlayHeaderScripts($request)); 
			$viewer->view('OverlayEditView.tpl', $moduleName);
		} else {
			$viewer->view('EditView.tpl', $moduleName);
		}
	}
	public function getOverlayHeaderScripts(nectarcrm_Request $request) {
		parent::getOverlayHeaderScripts($request);
	}

	function Calendar($request, $moduleName) {
		parent::process($request);
	}
}