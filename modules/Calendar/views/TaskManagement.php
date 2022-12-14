<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_TaskManagement_View extends nectarcrm_Index_View {

	function __construct() {
		$this->exposeMethod('showManagementView');
		$this->exposeMethod('getAllContents');
		$this->exposeMethod('getContentsOfPriority');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function showManagementView(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$statusField = nectarcrm_Field_Model::getInstance('taskstatus', $moduleModel);
		$ownerField = nectarcrm_Field_Model::getInstance('assigned_user_id', $moduleModel);

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('STATUS_FIELD', $statusField);
		$viewer->assign('OWNER_FIELD', $ownerField);

		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('TASK_FILTERS', $this->getFiltersFromSession());
		$module = nectarcrm_Module_Model::getInstance($moduleName);
		$field = nectarcrm_Field_Model::getInstance('taskpriority', $module);
		$priorities = $field->getPicklistValues();

		$page = 1;
		if ($request->get('page')) {
			$page = $request->get('page');
		}
		$viewer->assign('PAGE', $page);
		$viewer->assign('PRIORITIES', array_keys($priorities));
		$viewer->assign('COLORS', $this->generateColors($request));
		$viewer->view('TaskManagement.tpl', $moduleName);
	}

	public function getAllContents(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$module = nectarcrm_Module_Model::getInstance($moduleName);
		$field = nectarcrm_Field_Model::getInstance('taskpriority', $module);
		$priorities = $field->getPicklistValues();

		$data = array();
		foreach ($priorities as $key => $value) {
			$request->set('priority', $key);
			$data[$key] = $this->getContentsOfPriority($request, true);
		}

		echo json_encode($data);
	}

	public function getContentsOfPriority(nectarcrm_Request $request, $fetch = false) {
		$moduleName = $request->getModule();

		$page = 1;
		if ($request->get('page')) {
			$page = $request->get('page');
		}

		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', 10);

		$tasks = $this->filterRecords($request, $pagingModel);

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('TASKS', $tasks[$request->get('priority')]);
		$viewer->assign('PRIORITY', $request->get('priority'));
		$viewer->assign('TASK_FILTERS', $this->getFiltersFromSession());
		$viewer->assign('COLORS', $this->generateColors($request));
		$viewer->assign('PAGING_MODEL', $pagingModel);
		return $viewer->view('TaskManagementContents.tpl', $moduleName, $fetch);
	}

	public function generateColors(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$module = nectarcrm_Module_Model::getInstance($moduleName);
		$field = nectarcrm_Field_Model::getInstance('taskpriority', $module);
		$priorities = $field->getPicklistValues();

		if (!$request->get('colors')) {
			$colors = array();
			foreach ($priorities as $key => $value) {
				$colors[$key] = $this->getColor($key);
			}
		} else {
			$colors = $request->get('colors');
		}
		return $colors;
	}

	public function getColor($priority) {
		$color = '';
		switch ($priority) {
			case 'High'		:	$color = '#FF5555';	break;
			case 'Medium'	:	$color = '#03C04A';	break;
			case 'Low'		:	$color = '#54A7F5';	break;
			default			:	$color = '#'.dechex(rand(0x000000, 0xFFFFFF));
								break;
		}
		return $color;
	}

	protected function setFiltersInSession($filters) {
		if (!isset($filters['status'])) {
			$filters['status'] = array();
		}
		if (!isset($filters['assigned_user_id'])) {
			$filters['assigned_user_id'] = array();
		}
		$_SESSION['task_filters'] = $filters;
	}

	protected function getFiltersFromSession() {
		$filters = $_SESSION['task_filters'];
		if (!isset($filters)) {
			$filters = array('status' => array(), 'date' => 'all', 'assigned_user_id' => array());
		}
		return $filters;
	}

	public function filterRecords(nectarcrm_Request $request, $pagingModel) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$filters = $request->get('filters');
		$this->setFiltersInSession($filters);
		$conditions = array();
		foreach ($filters as $name => $value) {
			if ($name == 'date') {
				switch ($value) {
					case 'today':	$conditions[$name] = array();
									$startDate = new DateTimeField(date('Y-m-d').' 00:00:00');
									$endDate = new DateTimeField(date('Y-m-d').' 23:59:59');

									$conditions[$name]['comparator'] = 'bw';
									$conditions[$name]['fieldName'] = 'due_date';
									$conditions[$name]['fieldValue'] = array('start' => $startDate->getDBInsertDateTimeValue(),	'end' => $endDate->getDBInsertDateTimeValue());
									break;

					case 'thisweek':$conditions[$name] = array();
									$thisweek0 = date('Y-m-d', strtotime('-1 week Sunday'));
									$thisWeekStartDateTime = new DateTimeField($thisweek0.' 00:00:00');
									$thisweek1 = date('Y-m-d', strtotime('this Saturday'));
									$thisWeekEndDateTime = new DateTimeField($thisweek1.' 23:59:59');

									$conditions[$name]['comparator'] = 'bw';
									$conditions[$name]['fieldName'] = 'due_date';
									$conditions[$name]['fieldValue'] = array('start' => $thisWeekStartDateTime->getDBInsertDateTimeValue(), 'end' => $thisWeekEndDateTime->getDBInsertDateTimeValue());
									break;

					case 'range' :	$conditions[$name] = array();
									$startDate = new DateTimeField($filters['startRange'].' 00:00:00');
									$endDate = new DateTimeField($filters['endRange'].' 23:59:59');

									$conditions[$name]['comparator'] = 'bw';
									$conditions[$name]['fieldName'] = 'due_date';
									$conditions[$name]['fieldValue'] = array('start' => $startDate->getDBInsertDateTimeValue(), 'end' => $endDate->getDBInsertDateTimeValue());
									break;

					case 'all' :	$name = 'status';
									$conditions[$name] = array();
									$conditions[$name]['comparator'] = 'n';
									$conditions[$name]['fieldValue'] = 'Completed';
									$conditions[$name]['fieldName'] = 'taskstatus';	
									break;
				}
			} else if ($name == 'status') {
				$conditions[$name] = array();
				$conditions[$name]['comparator'] = 'e';
				$conditions[$name]['fieldValue'] = $value;
				$conditions[$name]['fieldName'] = 'taskstatus';
			} else if ($name == 'assigned_user_id') {
				$conditions[$name] = array();
				$conditions[$name]['comparator'] = 'e';
				$conditions[$name]['fieldValue'] = $value;
				$conditions[$name]['fieldName'] = 'assigned_user_id';
			}
		}

		if ($request->get('priority') != null) {
			$conditions['priority'] = array();
			$conditions['priority']['comparator'] = 'e';
			$conditions['priority']['fieldValue'] = $request->get('priority');
			$conditions['priority']['fieldName'] = 'taskpriority';
		}

		$tasks = $moduleModel->getAllTasksbyPriority($conditions, $pagingModel);
		return $tasks;
	}

}
