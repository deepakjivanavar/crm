<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Events_AddCalendarEvent_View extends nectarcrm_QuickCreateAjax_View {

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		$recordId = $request->get('record');
		$mode = $request->get('mode');
		if ($mode === 'edit' && $recordId) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);
		} else {
			$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
		}

		$moduleModel = $recordModel->getModule();

		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		foreach ($requestFieldList as $fieldName => $fieldValue) {
			$fieldModel = $fieldList[$fieldName];
			if ($fieldModel->isEditable()) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}

		$existingRelatedContacts = $recordModel->getRelatedContactInfo();
		//If already selected contact ids, then in gotoFull form should show those selected contact ids
		$idsList = $request->get('contactidlist');
		if (!empty($idsList)) {
			$contactIdsList = explode(';', $idsList);
			foreach ($contactIdsList as $contactId) {
				$existingRelatedContacts[] = array('name' => decode_html(nectarcrm_Util_Helper::getRecordName($contactId)), 'id' => $contactId);
			}
		}

		$fieldsInfo = array();
		foreach ($fieldList as $name => $model) {
			$fieldsInfo[$name] = $model->getFieldInfo();
		}

		$recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$recordStructure = $recordStructureInstance->getStructure();
		$fields = array();
		foreach ($recordStructure as $blockLabel => $blockFields) {
			foreach ($blockFields as $fieldName => $field) {
				$fields[$fieldName] = $field;
			}
		}

		$viewer = $this->getViewer($request);
		//Events quick create will fall back to nectarcrm 
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE_EVENT', Zend_Json::encode($picklistDependencyDatasource['Events']));
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('SINGLE_MODULE', 'SINGLE_' . $moduleName);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('FIELDS', $fields);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));

		$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));

		$viewer->assign('MAX_UPLOAD_LIMIT_MB', nectarcrm_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', nectarcrm_Util_Helper::getMaxUploadSizeInBytes());
		$viewer->assign('RECORD_ID', $recordId);
		$viewer->assign('MODE', $mode);
		$viewer->assign('RELATED_CONTACTS', $existingRelatedContacts);
		echo $viewer->view('AddCalendarEvent.tpl', $moduleName, true);
	}

}

?>