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
 * Settings List View Model Class
 */

class Settings_nectarcrm_ListView_Model extends nectarcrm_Base_Model {

	/**
	 * Function to get the Module Model
	 * @return nectarcrm_Module_Model instance
	 */
	public function getModule() {
		return $this->module;
	}

	public function setModule($name) {
		$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'Module', $name);
		$this->module = new $modelClassName();
		return $this;
	}

	public function setModuleFromInstance($module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * Function to get the list view header
	 * @return <Array> - List of nectarcrm_Field_Model instances
	 */
	public function getListViewHeaders() {
		$module = $this->getModule();
		return $module->getListFields();
	}
    
    public function getBasicListQuery() {
        $module = $this->getModule();
        return 'SELECT * FROM '. $module->getBaseTable();
    }

	/**
	 * Function to get the list view entries
	 * @param nectarcrm_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to nectarcrm_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

		$module = $this->getModule();
		$moduleName = $module->getName();
		$parentModuleName = $module->getParentName();
		$qualifiedModuleName = $moduleName;
		if (!empty($parentModuleName)) {
			$qualifiedModuleName = $parentModuleName . ':' . $qualifiedModuleName;
		}
		$recordModelClass = nectarcrm_Loader::getComponentClassName('Model', 'Record', $qualifiedModuleName);
		$listQuery = $this->getBasicListQuery();
        
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$orderBy = $this->getForSql('orderby');
		if (!empty($orderBy) && $orderBy === 'smownerid') { 
			$fieldModel = nectarcrm_Field_Model::getInstance('assigned_user_id', $moduleModel); 
			if ($fieldModel->getFieldDataType() == 'owner') { 
				$orderBy = 'COALESCE(CONCAT(nectarcrm_users.first_name,nectarcrm_users.last_name),nectarcrm_groups.groupname)'; 
			} 
		}
		if (!empty($orderBy)) {
			$listQuery .= ' ORDER BY ' . $orderBy . ' ' . $this->getForSql('sortorder');
		}
		if($module->isPagingSupported()) {
			$listQuery .= " LIMIT $startIndex, ".($pageLimit+1);
		}

		$listResult = $db->pquery($listQuery, array());
		$noOfRecords = $db->num_rows($listResult);

		$listViewRecordModels = array();
		for ($i = 0; $i < $noOfRecords; ++$i) {
			$row = $db->query_result_rowdata($listResult, $i);
			$record = new $recordModelClass();
			$record->setData($row);

			if (method_exists($record, 'getModule') && method_exists($record, 'setModule')) {
				$moduleModel = Settings_nectarcrm_Module_Model::getInstance($qualifiedModuleName);
				$record->setModule($moduleModel);
			}

			$listViewRecordModels[$record->getId()] = $record;
		}
		if($module->isPagingSupported()) {
			$pagingModel->calculatePageRange($listViewRecordModels);
			if(count($listViewRecordModels) > $pageLimit) {
				array_pop($listViewRecordModels);
				$pagingModel->set('nextPageExists', true);
			} else {
				$pagingModel->set('nextPageExists', false);
			}
		}
		return $listViewRecordModels;
	}
	
	public function getListViewLinks() {
		$links = array();
		$basicLinks = $this->getBasicLinks();
		
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = nectarcrm_Link_Model::getInstanceFromValues($basicLink);
		}
		return $links;
	}
	
	/*
	 * Function to get Basic links
	 * @return array of Basic links
	 */
	public function getBasicLinks(){
		$basicLinks = array();
		$moduleModel = $this->getModule();
		if($moduleModel->hasCreatePermissions())
			$basicLinks[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => 'LBL_ADD_RECORD',
					'linkurl' => $moduleModel->getCreateRecordUrl(),
					'linkicon' => 'fa fa-plus'
			);
		
		return $basicLinks;
	}

	/*	 * * 
	 * Function which will get the list view count  
	 * @return - number of records 
	 */

	public function getListViewCount() {
		$db = PearDatabase::getInstance();

		$listQuery = $this->getBasicListQuery();

        $position = stripos($listQuery, ' from ');
		if ($position) {
			$split = preg_split('/ from /i', $listQuery);
			$splitCount = count($split);
			$listQuery = 'SELECT count(*) AS count ';
			for ($i=1; $i<$splitCount; $i++) {
				$listQuery = $listQuery. ' FROM ' .$split[$i];
			}
		}

		$listResult = $db->pquery($listQuery, array());
		return $db->query_result($listResult, 0, 'count');
	}

	/**
	 * Function to get the instance of Settings module model
	 * @return Settings_nectarcrm_Module_Model instance
	 */
	public static function getInstance($name = 'Settings:nectarcrm') {
		$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'ListView', $name);
		$instance = new $modelClassName();
		return $instance->setModule($name);
	}
}