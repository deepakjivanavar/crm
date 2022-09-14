<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

vimport('~~/include/Webservices/Custom/DeleteUser.php');

class Users_Import_View extends nectarcrm_Import_View {
    
    function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Import')) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
        if($request->getMode() == 'import') {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            if(!$currentUserModel->isAdminUser()) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
        }
	}

	public function initializeMappingParameters(nectarcrm_Request $request) {
		parent::initializeMappingParameters($request);
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$moduleFields = $moduleModel->getFields();

		$viewer = $this->getViewer($request);
		$viewer->assign('IMPORTABLE_FIELDS', $moduleModel->getImportableFieldModels($moduleName));
	}

    public function process(nectarcrm_Request $request) {
        if($request->getMode() != 'undoImport') {
            parent::process($request);
        } else {
            $viewer = new nectarcrm_Viewer();
            $db = PearDatabase::getInstance();

            $moduleName = $request->getModule();

            $user = Users_Record_Model::getCurrentUserModel();
            $dbTableName = Import_Utils_Helper::getDbTableName($user);

            $query = "SELECT recordid FROM $dbTableName WHERE status = ? AND recordid IS NOT NULL";
            $result = $db->pquery($query, array(Import_Data_Action::$IMPORT_RECORD_CREATED));
            $noOfRecords = $db->num_rows($result);
            $noOfRecordsDeleted = 0;
            $activeAdminId = Users::getActiveAdminId();
            $userModel = Users_Record_Model::getCurrentUserModel();
            for($i=0; $i<$noOfRecords; $i++) {
                $recordId = $db->query_result($result, $i, 'recordid');
                $userId = vtws_getWebserviceEntityId($moduleName, $recordId);
                $transformUserId = vtws_getWebserviceEntityId($moduleName, $activeAdminId);
                vtws_deleteUser($userId, $transformUserId, $userModel);
                Users_Record_Model::deleteUserPermanently($recordId, $activeAdminId);
                $noOfRecordsDeleted++;
            }
            $viewer->assign('FOR_MODULE', $moduleName);
            $viewer->assign('MODULE', 'Import');
            $viewer->assign('TOTAL_RECORDS', $noOfRecords);
            $viewer->assign('DELETED_RECORDS_COUNT', $noOfRecordsDeleted);
            $viewer->view('ImportUndoResult.tpl', 'Import');
        }
	}
}