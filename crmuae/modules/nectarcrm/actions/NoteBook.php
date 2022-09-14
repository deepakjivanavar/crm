<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_NoteBook_Action extends nectarcrm_Action_Controller {

	function __construct() {
		$this->exposeMethod('NoteBookCreate');
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->getMode();

		if($mode){
			$this->invokeExposedMethod($mode,$request);
		}
	}

	function NoteBookCreate(nectarcrm_Request $request){
		$adb = PearDatabase::getInstance();

		$moduleName = $request->getModule();
		$userModel = Users_Record_Model::getCurrentUserModel();
		$linkId = $request->get('linkId');
		$noteBookName = $request->get('notePadName');
		$noteBookContent = $request->get('notePadContent');
		$tabId = $request->get("tab");
		$userid = $userModel->getId();

		// Added for nectarcrm7
		if(empty($tabId)){
			$dasbBoardModel = nectarcrm_DashBoard_Model::getInstance($moduleName);
			$defaultTab = $dasbBoardModel->getUserDefaultTab($userModel->getId());
			$tabId = $defaultTab['id'];
		}

		$date_var = date("Y-m-d H:i:s");
		$date = $adb->formatDate($date_var, true);

		$dataValue = array();
		$dataValue['contents'] = $noteBookContent;
		$dataValue['lastSavedOn'] = $date;

		$data = Zend_Json::encode((object) $dataValue);

		$query="INSERT INTO nectarcrm_module_dashboard_widgets(linkid, userid, filterid, title, data,dashboardtabid) VALUES(?,?,?,?,?,?)";
		$params= array($linkId,$userid,0,$noteBookName,$data,$tabId);
		$adb->pquery($query, $params);
		$id = $adb->getLastInsertID();

		$result = array();
		$result['success'] = TRUE;
		$result['widgetId'] = $id;
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();

	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}
}
