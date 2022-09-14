<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Workflow Task Type Model Class
 */
require_once 'modules/com_nectarcrm_workflow/VTTaskManager.inc';

class Settings_Workflows_TaskType_Model extends nectarcrm_Base_Model {

	public function getId() {
		return $this->get('id');
	}

	public function getName() {
		return $this->get('tasktypename');
	}
	
	public function getLabel() {
		return $this->get('label');
	}

	public function getTemplatePath() {
		return $this->get('templatepath');
	}

	public function getEditViewUrl() {
		return '?module=Workflows&parent=Settings&view=EditTask&type='.$this->getName();
	}
    
    public function getV7EditViewUrl() {
		return '?module=Workflows&parent=Settings&view=EditV7Task&type='.$this->getName();
	}

	public static function getInstanceFromClassName($taskClass) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT * FROM com_nectarcrm_workflow_tasktypes where classname=?",array($taskClass));
		$row = $db->query_result_rowdata($result, 0);
		$taskTypeObject = VTTaskType::getInstance($row);
		return self::getInstanceFromTaskTypeObject($taskTypeObject);
	}

	public static function getAllForModule($moduleModel) {
		$taskTypes = VTTaskType::getAll($moduleModel->getName());
		$taskTypeModels = array();
		foreach($taskTypes as $taskTypeObject) {
			$taskTypeModels[] = self::getInstanceFromTaskTypeObject($taskTypeObject);
		}
		return $taskTypeModels;
	}

	public static function getInstance($taskType) {
		$taskTypeObject = VTTaskType::getInstanceFromTaskType($taskType);
		return self::getInstanceFromTaskTypeObject($taskTypeObject);
	}

	public static function getInstanceFromTaskTypeObject($taskTypeObject) {
		return new self($taskTypeObject->data);
	}

	public function getTaskBaseModule() {
		$taskTypeName = $this->get('tasktypename');
		switch($taskTypeName) {
			case 'VTCreateTodoTask' : return nectarcrm_Module_Model::getInstance('Calendar');
			case 'VTCreateEventTask' : return nectarcrm_Module_Model::getInstance('Events');
		}
	}

}
