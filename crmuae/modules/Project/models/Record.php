<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

 Class Project_Record_Model extends nectarcrm_Record_Model {

	/**
	 * Function to get the summary information for module
	 * @return <array> - values which need to be shown as summary
	 */
	public function getSummaryInfo() {
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$projectTaskInstance = nectarcrm_Module_Model::getInstance('ProjectTask');
		if($userPrivilegesModel->hasModulePermission($projectTaskInstance->getId())) {
			$adb = PearDatabase::getInstance();

			$query ='SELECT smownerid,enddate,projecttaskstatus,projecttaskpriority
					FROM nectarcrm_projecttask
							INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid=nectarcrm_projecttask.projecttaskid
								AND nectarcrm_crmentity.deleted=0
							WHERE nectarcrm_projecttask.projectid = ? ';

			$result = $adb->pquery($query, array($this->getId()));

			$tasksOpen = $taskCompleted = $taskDue = $taskDeferred = $numOfPeople = 0;
			$highTasks = $lowTasks = $normalTasks = $otherTasks = 0;
			$currentDate = date('Y-m-d');
			$inProgressStatus = array('Open', 'In Progress');
			$usersList = array();

			while($row = $adb->fetchByAssoc($result)) {
				$projectTaskStatus = $row['projecttaskstatus'];
				switch($projectTaskStatus){
					case 'Open'		: $tasksOpen++;		break;
					case 'Deferred'	: $taskDeferred++;	break;
					case 'Completed': $taskCompleted++;	break;
				}
				$projectTaskPriority = $row['projecttaskpriority'];
				switch($projectTaskPriority){
					case 'high' : $highTasks++;break;
					case 'low' : $lowTasks++;break;
					case 'normal' : $normalTasks++;break;
					default : $otherTasks++;break;
				}

				if(!empty($row['enddate']) && (strtotime($row['enddate']) < strtotime($currentDate)) &&
						(in_array($row['projecttaskstatus'], $inProgressStatus))) {
					$taskDue++;
				}
				$usersList[] = $row['smownerid'];
			}

			$usersList = array_unique($usersList);
			$numOfPeople = count($usersList);

			$summaryInfo['projecttaskstatus'] =  array(
													'LBL_TASKS_OPEN'	=> $tasksOpen,
													'Progress'			=> $this->get('progress'),
													'LBL_TASKS_DUE'		=> $taskDue,
													'LBL_TASKS_COMPLETED'=> $taskCompleted,
			);

			$summaryInfo['projecttaskpriority'] =  array(
													'LBL_TASKS_HIGH'	=> $highTasks,
													'LBL_TASKS_NORMAL'	=> $normalTasks,
													'LBL_TASKS_LOW'		=> $lowTasks,
													'LBL_TASKS_OTHER'	=> $otherTasks,
			);
		}

		return $summaryInfo;
	}

	/** 
	 * Function to get the project task for a project
	 * @return <Array> - $projectTasks
	 */
	public function getProjectTasks() {
		$recordId  = $this->getId();
		$db = PearDatabase::getInstance();

		$sql = "SELECT projecttaskid as recordid,projecttaskname as name,startdate,enddate,projecttaskstatus FROM nectarcrm_projecttask 
				INNER JOIN nectarcrm_crmentity  ON nectarcrm_projecttask.projecttaskid = nectarcrm_crmentity.crmid
				WHERE projectid=? AND nectarcrm_crmentity.deleted=0 AND nectarcrm_projecttask.startdate IS NOT NULL AND nectarcrm_projecttask.enddate IS NOT NULL";

		$result = $db->pquery($sql, array($recordId));
		$i = -1;
		while($record = $db->fetchByAssoc($result)){
			$record['id'] = $i;
			$record['name'] = decode_html(textlength_check($record['name']));
			$record['status'] = self::getGanttStatus($record['projecttaskstatus']);
			$record['start'] = strtotime($record['startdate']) * 1000;
			$record['duration'] = $this->getDuration($record['startdate'], $record['enddate']);
			$record['end'] = strtotime($record['enddate']) * 1000;
			$projectTasks[] = $record;
			$i--;
		}

		return $projectTasks;
	}

	/**
	 * Function to get the duration
	 * @param <string> $startDate,$endDate
	 * @return $duration
	 */
	public function getDuration($startDate,$endDate) {
		$difference = strtotime($endDate) - strtotime($startDate);
		$duration = floor($difference/(3600*24)+1);

		// if the start date and end date are same
		if($duration == 0) {
			return $duration+0.1;
		} else if($duration < 0) { // if end date is null or less than start date
			return 0; 
		}

		return $duration;
	}

	static public function getGanttStatus($status) {
		switch($status) {
			case 'Open'			: return 'STATUS_UNDEFINED';
			case 'In Progress'  : return 'STATUS_ACTIVE';
			case 'Completed'	: return 'STATUS_DONE';
			case 'Deferred'		: return 'STATUS_SUSPENDED';
			case 'Canceled'		: return 'STATUS_FAILED';
			default				: return $status;
		}
	}

 function getStatusColors() {
		$statusColorMap = array();
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT *FROM nectarcrm_projecttask_status_color');
		if ($db->num_rows($result) > 0) {
			for ($i = 0; $i < $db->num_rows($result); $i++) {
				$status = decode_html($db->query_result($result, $i, 'status'));
				$color = $db->query_result($result, $i, 'color');
				if (empty($color)) {
					$color = $db->query_result($result, $i, 'defaultcolor');
				}
				$statusColorMap[$status] = $color;
			}
		}

		return $statusColorMap;
	}

	static function getGanttStatusCss($status, $color) {
		return '.taskStatus[status="'.self::getGanttStatus($status).'"]{
					background-color: '.$color.';
				}';
	}

	static function getGanttSvgStatusCss($status, $color) {
		return '.taskStatusSVG[status="'.self::getGanttStatus($status).'"]{
					fill: '.$color.';
				}';
	}
}

?>
