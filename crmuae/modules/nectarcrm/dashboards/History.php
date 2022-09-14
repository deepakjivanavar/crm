<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_History_Dashboard extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$LIMIT = 10;
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);

		$moduleName = $request->getModule();
		$historyType = $request->get('historyType');
		$userId = $request->get('type');
            
		$page = $request->get('page');
		if(empty($page)) {
			$page = 1;
		}
		$linkId = $request->get('linkid');

		$modifiedTime = $request->get('modifiedtime');
		//Date conversion from user to database format
		if(!empty($modifiedTime)) {
			$startDate = nectarcrm_Date_UIType::getDBInsertedValue($modifiedTime['start']);
			$dates['start'] = getValidDBInsertDateTimeValue($startDate . ' 00:00:00');
			$endDate = nectarcrm_Date_UIType::getDBInsertedValue($modifiedTime['end']);
			$dates['end'] = getValidDBInsertDateTimeValue($endDate . ' 23:59:59');
		}
		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', $LIMIT);

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$history = $moduleModel->getHistory($pagingModel, $historyType,$userId, $dates);
		$widget = nectarcrm_Widget_Model::getInstance($linkId, $currentUser->getId());
		$modCommentsModel = nectarcrm_Module_Model::getInstance('ModComments'); 

		$viewer->assign('CURRENT_USER', $currentUser);
		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('HISTORIES', $history);
		$viewer->assign('PAGE', $page);
		$viewer->assign('HISTORY_TYPE', $historyType); 
		$viewer->assign('NEXTPAGE', ($pagingModel->get('historycount') < $LIMIT)? 0 : $page+1);
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);

		$userCurrencyInfo = getCurrencySymbolandCRate($currentUser->get('currency_id'));
		$viewer->assign('USER_CURRENCY_SYMBOL', $userCurrencyInfo['symbol']);
		
		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/HistoryContents.tpl', $moduleName);
		} else {
			$accessibleUsers = $currentUser->getAccessibleUsers();
			$viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
			$viewer->view('dashboards/History.tpl', $moduleName);
		}
	}
}
