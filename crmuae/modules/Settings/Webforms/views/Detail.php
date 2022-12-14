<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Webforms_Detail_View extends Settings_nectarcrm_Index_View {

	public function checkPermission(nectarcrm_Request $request) {
		parent::checkPermission($request);

		$recordId = $request->get('record');
		$moduleModel = nectarcrm_Module_Model::getInstance($request->getModule());

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$recordId || !$currentUserPrivilegesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);

		$recordModel = Settings_Webforms_Record_Model::getInstanceById($recordId, $qualifiedModuleName);
		$siteUrl = $postUrl = vglobal('site_URL');
		if($siteUrl[strlen($siteUrl)-1] != '/') $postUrl .= '/';
		$postUrl .= 'modules/Webforms/capture.php';
		$recordModel->set('posturl', $postUrl);

		$recordStructure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$moduleModel = $recordModel->getModule();

		$navigationInfo = ListViewSession::getListViewNavigation($recordId);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_NAME', $qualifiedModuleName);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure->getStructure());
		$viewer->assign('MODULE_MODEL', $moduleModel);

		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('SOURCE_MODULE', $recordModel->get('targetmodule'));
		$viewer->assign('DETAILVIEW_LINKS', $recordModel->getDetailViewLinks());
		$viewer->assign('SELECTED_FIELD_MODELS_LIST', $recordModel->getSelectedFieldsList());
		$viewer->assign('DOCUMENT_FILE_FIELDS', $recordModel->getFileFields());
		$viewer->assign('NO_PAGINATION',true);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$userCurrencyInfo = getCurrencySymbolandCRate($currentUserModel->get('currency_id'));
		$viewer->assign('USER_CURRENCY_SYMBOL', $userCurrencyInfo['symbol']);

		$viewer->view('DetailView.tpl', $qualifiedModuleName);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.Settings.nectarcrm.resources.Detail",
			"modules.Settings.$moduleName.resources.Detail"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

}