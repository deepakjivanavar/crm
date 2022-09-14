<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModTracker_Relation_Model extends nectarcrm_Record_Model {

	function setParent($parent) {
		$this->parent = $parent;
	}

	function getParent() {
		return $this->parent;
	}

	function getLinkedRecord() {
        $db = PearDatabase::getInstance();
        
		$targetId = $this->get('targetid');
		$targetModule = $this->get('targetmodule');
        
        $query = 'SELECT * FROM nectarcrm_crmentity WHERE crmid = ?';
		$params = array($targetId);
		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);
		$moduleModels = array();
		if($noOfRows) {
			if(!array_key_exists($targetModule, $moduleModels)) {
				$moduleModel = nectarcrm_Module_Model::getInstance($targetModule);
			}
			$row = $db->query_result_rowdata($result, 0);
			$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'Record', $targetModule);
			$recordInstance = new $modelClassName();
			$recordInstance->setData($row)->setModuleFromInstance($moduleModel);
			$recordInstance->set('id', $row['crmid']);
			if($targetModule == 'Emails') {
				$recordInstance->set('parent_id', $this->parent->get('crmid'));
			}
			return $recordInstance;
		}
		return false;
	}

	public function getRecordDetailViewUrl() {
		try {
			$recordModel = nectarcrm_Record_Model::getInstanceById($this->get('targetid'), $this->get('targetmodule'));
			if ($this->get('targetmodule') == 'Emails') {
				return $recordModel->getDetailViewUrl($this->parent->get('crmid'));
			}
			return $recordModel->getDetailViewUrl();
		} catch (Exception $e) {
			return false;
		}
	}

}
