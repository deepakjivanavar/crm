<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PriceBooks_ListPriceUpdate_View extends nectarcrm_View_Controller {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate($moduleName, $moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	function preProcess(nectarcrm_Request $request, $display = true) {
	}

	function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$priceBookId = $request->get('record');
		$relId = $request->get('relid');
		$currentPrice = $request->get('currentPrice');

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('PRICEBOOK_ID', $priceBookId);
		$viewer->assign('REL_ID', $relId);
		$viewer->assign('CURRENT_PRICE', $currentPrice);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('ListPriceUpdate.tpl', $moduleName);
	}

	function postProcess(nectarcrm_Request $request) {
	}
}

?>
