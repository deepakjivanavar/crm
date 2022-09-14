<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Workflows_CreateEntity_View extends Settings_nectarcrm_Index_View {

	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$workflowId = $request->get('for_workflow');
        if ($workflowId) {
			$workflowModel = Settings_Workflows_Record_Model::getInstance($workflowId);
			$selectedModule = $workflowModel->getModule();
			$selectedModuleName = $selectedModule->getName();
		} else {
            $selectedModuleName = $request->get('module_name');
			$selectedModule = nectarcrm_Module_Model::getInstance($selectedModuleName);
			$workflowModel = Settings_Workflows_Record_Model::getCleanInstance($selectedModuleName);
		}
        
		$taskType = 'VTCreateEntityTask';
        $taskModel = Settings_Workflows_TaskRecord_Model::getCleanInstance($workflowModel, $taskType);

		$taskTypeModel = $taskModel->getTaskType();
		$viewer->assign('TASK_TYPE_MODEL', $taskTypeModel);

		$viewer->assign('TASK_TEMPLATE_PATH', $taskTypeModel->getTemplatePath());
		$recordStructureInstance = Settings_Workflows_RecordStructure_Model::getInstanceForWorkFlowModule($workflowModel,
																			Settings_Workflows_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDITTASK);
        $recordStructureInstance->setTaskRecordModel($taskModel);

		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$relatedModule = $request->get('relatedModule');
		$relatedModuleModel = nectarcrm_Module_Model::getInstance($relatedModule);

		$workflowModuleModel = $workflowModel->getModule();

		$viewer->assign('WORKFLOW_MODEL', $workflowModel);
		$viewer->assign('REFERENCE_FIELD_NAME', $workflowModel->getReferenceFieldName($relatedModule));
		$viewer->assign('RELATED_MODULE_MODEL', $relatedModuleModel);
		$viewer->assign('FIELD_EXPRESSIONS', Settings_Workflows_Module_Model::getExpressions());
		$viewer->assign('MODULE_MODEL', $workflowModuleModel);
		$viewer->assign('SOURCE_MODULE',$workflowModuleModel->getName());
		$viewer->assign('RELATED_MODULE_MODEL_NAME','');
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('CreateEntity.tpl', $qualifiedModuleName);
	}
}