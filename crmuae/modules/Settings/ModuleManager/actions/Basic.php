<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
require_once('vtlib/nectarcrm/Layout.php'); 

class Settings_ModuleManager_Basic_Action extends Settings_nectarcrm_IndexAjax_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('updateModuleStatus');
		$this->exposeMethod('importUserModuleStep3');
		$this->exposeMethod('updateUserModuleStep3');
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function updateModuleStatus(nectarcrm_Request $request) {
		$moduleName = $request->get('forModule');
		$updateStatus = $request->get('updateStatus');

		$moduleManagerModel = new Settings_ModuleManager_Module_Model();

		if($updateStatus == 'true') {
			$moduleManagerModel->enableModule($moduleName);
		}else{
			$moduleManagerModel->disableModule($moduleName);
		}

		$response = new nectarcrm_Response();
		$response->emit();
	}

	public function importUserModuleStep3(nectarcrm_Request $request) {
		$importModuleName = $request->get('module_import_name');
		$uploadFile = $request->get('module_import_file');
		$uploadDir = Settings_ModuleManager_Extension_Model::getUploadDirectory();
		$uploadFileName = "$uploadDir/$uploadFile";
		checkFileAccess($uploadFileName);

		$importType = $request->get('module_import_type');
		if(strtolower($importType) == 'language') {
			$package = new nectarcrm_Language();
		} else if(strtolower($importType) == 'layout') {
			$package = new nectarcrm_Layout();
		} else {
			$package = new nectarcrm_Package();
		}

		$package->import($uploadFileName);
		checkFileAccessForDeletion($uploadFileName);
		unlink($uploadFileName);

		$result = array('success'=>true, 'importModuleName'=> $importModuleName);
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

	public function updateUserModuleStep3(nectarcrm_Request $request){
		$importModuleName = $request->get('module_import_name');
		$uploadFile = $request->get('module_import_file');
		$uploadDir = Settings_ModuleManager_Extension_Model::getUploadDirectory();
		$uploadFileName = "$uploadDir/$uploadFile";
		checkFileAccess($uploadFileName);

		$importType = $request->get('module_import_type');
		if(strtolower($importType) == 'language') {
			$package = new nectarcrm_Language();
		} else if(strtolower($importType) == 'layout') { 
			$package = new nectarcrm_Layout(); 
		} else { 
			$package = new nectarcrm_Package();
		}

		if (strtolower($importType) == 'language' || strtolower($importType) == 'layout' ) {
			$package->import($uploadFileName);
		} else {
			$package->update(nectarcrm_Module::getInstance($importModuleName), $uploadFileName);
		}

		checkFileAccessForDeletion($uploadFileName);
		unlink($uploadFileName);

		$result = array('success'=>true, 'importModuleName'=> $importModuleName);
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

	 public function validateRequest(nectarcrm_Request $request) { 
		$request->validateWriteAccess(); 
	} 
}
