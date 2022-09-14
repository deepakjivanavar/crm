<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_ViewTypes_View extends nectarcrm_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('getViewTypes');
		$this->exposeMethod('getSharedUsersList');
	}

	function getViewTypes(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$calendarViews = Calendar_Module_Model::getCalendarViewTypes($currentUser->id);
		$allViewTypes = Calendar_Module_Model::getCalendarViewTypesToAdd($currentUser->id);

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('VIEWTYPES', $calendarViews);
		$viewer->assign('ADDVIEWS', $allViewTypes);
		$viewer->view('CalendarViewTypes.tpl', $moduleName);
	}

	/**
	 * Function to get Shared Users
	 * @param nectarcrm_Request $request
	 */
	function getSharedUsersList(nectarcrm_Request $request){
		$viewer = $this->getViewer($request);
		$currentUser = Users_Record_Model::getCurrentUserModel();


		$moduleName = $request->getModule();
		$sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
		$sharedGroups = Calendar_Module_Model::getSharedCalendarGroupsList($currentUser->id);
		$sharedUsersInfo = Calendar_Module_Model::getSharedUsersInfoOfCurrentUser($currentUser->id);

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('SHAREDUSERS', $sharedUsers);
		$viewer->assign('SHAREDGROUPS', $sharedGroups);
		$viewer->assign('SHAREDUSERS_INFO', $sharedUsersInfo);
		$viewer->assign('CURRENTUSER_MODEL',$currentUser);
		$viewer->view('CalendarSharedUsers.tpl', $moduleName);
	}
}
