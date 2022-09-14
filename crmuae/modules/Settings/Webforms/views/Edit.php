<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_Webforms_Edit_View extends Settings_nectarcrm_Index_View {

	public function checkPermission(nectarcrm_Request $request) {
		parent::checkPermission($request);

		$moduleModel = nectarcrm_Module_Model::getInstance($request->getModule());
		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if (!$currentUserPrivilegesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);
		$mode = '';
		$selectedFieldsList = $allFieldsList = $fileFields = array();
		$viewer = $this->getViewer($request);
		$supportedModules = Settings_Webforms_Module_Model::getSupportedModulesList();

		if ($recordId) {
			$recordModel = Settings_Webforms_Record_Model::getInstanceById($recordId, $qualifiedModuleName);
			$selectedFieldsList = $recordModel->getSelectedFieldsList();
			$fileFields = $recordModel->getFileFields();

			$sourceModule = $recordModel->get('targetmodule');
			$mode = 'edit';
		} else {
			$recordModel = Settings_Webforms_Record_Model::getCleanInstance($qualifiedModuleName);
			$sourceModule = $request->get('sourceModule');
			if (!$sourceModule) {
				$sourceModule = reset(array_keys($supportedModules));
			}
			$recordModel->set('targetmodule',$sourceModule);
		}
		if(!$supportedModules[$sourceModule]){
			$message = vtranslate('LBL_ENABLE_TARGET_MODULES_FOR_WEBFORM',$qualifiedModuleName);
			$viewer->assign('MESSAGE', $message);
			$viewer->view('OperationNotPermitted.tpl', 'nectarcrm');
			return false;
		}

		$allFieldsList = $recordModel->getAllFieldsList($sourceModule);
		$recordStructure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

		$viewer->assign('MODE', $mode);
		$viewer->assign('RECORD_ID', $recordId);
		$viewer->assign('RECORD_MODEL', $recordModel);
		$viewer->assign('MODULE', $qualifiedModuleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

		$viewer->assign('SOURCE_MODULE', $sourceModule);
		$viewer->assign('ALL_FIELD_MODELS_LIST', $allFieldsList);
		$viewer->assign('SELECTED_FIELD_MODELS_LIST', $selectedFieldsList);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructure);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure->getStructure());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('DOCUMENT_FILE_FIELDS', $fileFields);

		$viewer->view('EditView.tpl', $qualifiedModuleName);
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
			"modules.Settings.$moduleName.resources.Field",
			"modules.Settings.$moduleName.resources.Edit"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function setModuleInfo($request, $moduleModel){
		$record = $request->get('record');
		if ($record) {
			parent::setModuleInfo($request, $moduleModel);
		}
	}
}