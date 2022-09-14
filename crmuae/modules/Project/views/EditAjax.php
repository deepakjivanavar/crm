<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Project_EditAjax_View extends nectarcrm_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('editColor');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function editColor(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->get('module');
		$parentRecordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('STATUS', $request->get('status'));
		$viewer->assign('TASK_STATUS', nectarcrm_Util_Helper::getPickListValues('projecttaskstatus'));
		$viewer->assign('TASK_STATUS_COLOR', $parentRecordModel->getStatusColors());
		$viewer->view('EditColor.tpl', $moduleName);
	}

}
