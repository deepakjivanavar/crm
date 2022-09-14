<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Workflows_TasksList_View extends Settings_nectarcrm_Index_View {

	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$recordId = $request->get('record');
		$workflowModel = Settings_Workflows_Record_Model::getInstance($recordId);

		$viewer->assign('WORKFLOW_MODEL', $workflowModel);

		$viewer->assign('TASK_LIST', $workflowModel->getTasks());
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('RECORD',$recordId);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('TasksList.tpl', $qualifiedModuleName);
	}
}