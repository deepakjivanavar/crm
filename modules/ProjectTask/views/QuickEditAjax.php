<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class ProjectTask_QuickEditAjax_View extends nectarcrm_IndexAjax_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		if (!(Users_Privileges_Model::isPermitted($moduleName, 'EditView'))) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$projectId = $request->get('parentid');
		$recordId = $request->get('record');

		if ($recordId) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);
		} else {
			$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
		}

		$moduleModel = $recordModel->getModule();

		$fieldList = $moduleModel->getFields();
		$fieldsInfo = array();
		foreach($fieldList as $name => $model){
			$fieldsInfo[$name] = $model->getFieldInfo();
		}
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		if ($projectId) {
			$recordModel->set('projectid', $projectId);
		}

		$recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, 'GanttQuickEdit');
		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);

		$viewer = $this->getViewer($request);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',nectarcrm_Functions::jsonEncode($picklistDependencyDatasource));
		$viewer->assign('RECORD', $recordId);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('SINGLE_MODULE', 'SINGLE_'.$moduleName);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));

		$viewer->assign('MAX_UPLOAD_LIMIT_MB', nectarcrm_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
		$viewer->assign('RETURN_VIEW', $request->get('returnview'));
		$viewer->assign('RETURN_MODE', $request->get('returnmode'));
		$viewer->assign('RETURN_MODULE', $request->get('returnmodule'));
		$viewer->assign('RETURN_RECORD', $request->get('returnrecord'));
		$viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));

		$viewer->view('QuickEdit.tpl', $moduleName);
	}

	public function getHeaderScripts(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.$moduleName.resources.Edit"
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

}