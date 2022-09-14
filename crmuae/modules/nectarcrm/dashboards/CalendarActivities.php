<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_CalendarActivities_Dashboard extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$page = $request->get('page');
		$linkId = $request->get('linkid');

		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', 10);

		$user = $request->get('type');
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$calendarActivities = $moduleModel->getCalendarActivities('upcoming', $pagingModel, $user);

		$widget = nectarcrm_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('ACTIVITIES', $calendarActivities);
		$viewer->assign('PAGING', $pagingModel);
		$viewer->assign('CURRENTUSER', $currentUser);

		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/CalendarActivitiesContents.tpl', $moduleName);
		} else {
			$sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
			$sharedGroups = Calendar_Module_Model::getSharedCalendarGroupsList($currentUser->id);
			$viewer->assign('SHARED_USERS', $sharedUsers);
			$viewer->assign('SHARED_GROUPS', $sharedGroups);
			
			$viewer->view('dashboards/CalendarActivities.tpl', $moduleName);
		}
	}
}
