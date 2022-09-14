<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_TopPotentials_Dashboard extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		$page = $request->get('page');
		if(empty($page)) {
			$page = 1;
		}
		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $page);

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$models = $moduleModel->getTopPotentials($pagingModel);

		$widget = nectarcrm_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('MODELS', $models);
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userCurrencyInfo = getCurrencySymbolandCRate($currentUser->get('currency_id'));
		$viewer->assign('USER_CURRENCY_SYMBOL', $userCurrencyInfo['symbol']);

		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/TopPotentialsContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/TopPotentials.tpl', $moduleName);
		}
	}
}
