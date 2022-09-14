<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class EmailTemplates_ListView_Model extends nectarcrm_ListView_Model {

	private $querySelectColumns = array('templatename, foldername, subject', 'systemtemplate', 'module', 'description');
	private $listViewColumns = array('templatename', 'subject', 'description', 'module');

	public function addColumnToSelectClause($columName) {
		if (!is_array($columName))
			$columNameList = array($columName);
		else
			$columNameList = $columName;

		$this->querySelectColumns = array_merge($this->querySelectColumns, $columNameList);
		return $this; 
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  nectarcrm_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$moduleModel = $this->getModule();
		$linkTypes = array('LISTVIEWMASSACTION');
		$links = array();

		$massActionLinks[] = array(
			'linktype' => 'LISTVIEWMASSACTION',
			'linklabel' => 'LBL_DELETE',
			'linkurl' => 'javascript:EmailTemplates_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
			'linkicon' => ''
		);

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = nectarcrm_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}

	/**
	 * Static Function to get the Instance of nectarcrm ListView model for a given module and custom view
	 * @param <String> $moduleName - Module Name
	 * @param <Number> $viewId - Custom View Id
	 * @return nectarcrm_ListView_Model instance
	 */
	public static function getInstance($moduleName, $viewId = 0) {
		$db = PearDatabase::getInstance();
		$modelClassName = nectarcrm_Loader::getComponentClassName('Model', 'ListView', $moduleName);
		$instance = new $modelClassName();

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		return $instance->set('module', $moduleModel);
	}

	/**
	 * Function to get the list view header
	 * @return <Array> - List of nectarcrm_Field_Model instances
	 */
	public function getListViewHeaders() {
		$fieldObjects = array();
		$listViewHeaders = array('Template Name' => 'templatename', 'Subject' => 'subject', 'Description' => 'description', 'Module Name' => 'module');
		foreach ($listViewHeaders as $key => $fieldName) {
			$fieldModel = new EmailTemplates_Field_Model();
			$fieldModel->set('name', $fieldName);
			$fieldModel->set('label', $key);
			$fieldModel->set('column', $fieldName);
			$fieldObjects[] = $fieldModel;
		}
		return $fieldObjects;
	}

	/**
	 * Function to get the list view entries
	 * @param nectarcrm_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to nectarcrm_Record_Model instance.
	 */

	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		$listQuery = $this->getQuery();
		$sourceModule = $this->get('sourceModule');
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');

		$whereQuery .= ' WHERE ';
		if(!empty($searchKey) && !empty($searchValue)) {
			$whereQuery .= "$searchKey LIKE '$searchValue%' AND ";
		}

		//module should be enabled or module should be empty then allow
		$moduleActiveCheck = '(nectarcrm_tab.presence IN (0,2) OR nectarcrm_emailtemplates.module IS null OR nectarcrm_emailtemplates.module = "")';
		$listQuery .= $whereQuery. $moduleActiveCheck;
		//To retrieve only selected module records
		if ($sourceModule) {
			$listQuery .= " AND nectarcrm_emailtemplates.module = '".$sourceModule."'";
		}

		if ($orderBy) {
			$listQuery .= " ORDER BY $orderBy $sortOrder";
		} else {
			$listQuery .= " ORDER BY templateid DESC";
		}
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		$result = $db->pquery($listQuery, array());
		$num_rows = $db->num_rows($result);

		$listViewRecordModels = array();
		for ($i = 0; $i < $num_rows; $i++) {
			$recordModel = new EmailTemplates_Record_Model();
			$recordModel->setModule('EmailTemplates');
			$row = $db->query_result_rowdata($result, $i);
			$recordModel->setRawData($row);
			foreach ($row as $key => $value) {
				if($key=="module"){
					$value = vtranslate($value,$value);
				}
				if(in_array($key,$this->listViewColumns)){
					$value = textlength_check($value);
				}
				$row[$key] = $value;
			}
			$listViewRecordModels[$row['templateid']] = $recordModel->setData($row);
		}

		$pagingModel->calculatePageRange($listViewRecordModels);

		if($num_rows > $pageLimit){
			array_pop($listViewRecordModels);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}

		return $listViewRecordModels;
	}

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of nectarcrm_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = nectarcrm_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$basicLinks = array(
				array(
						'linktype' => 'LISTVIEWBASIC',
						'linklabel' => 'LBL_ADD_RECORD',
						'linkurl' => $moduleModel->getCreateRecordUrl(),
						'linkicon' => ''
				)
		);
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = nectarcrm_Link_Model::getInstanceFromValues($basicLink);
		}

		return $links;
	}

	function getQuery() {
		$listQuery = 'SELECT templateid,'.implode(',',$this->querySelectColumns).' FROM nectarcrm_emailtemplates
						LEFT JOIN nectarcrm_tab ON nectarcrm_tab.name = nectarcrm_emailtemplates.module
						AND (nectarcrm_tab.isentitytype=1 or nectarcrm_tab.name = "Users") ';
		return $listQuery;
	}

	/**
	 * Function to get the list view entries
	 * @param nectarcrm_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to nectarcrm_Record_Model instance.
	 */
	public function getListViewCount() {
		$db = PearDatabase::getInstance();

		$listQuery = $this->getQuery();

		$position = stripos($listQuery, 'from');
		if ($position) {
			$split = preg_split('/from/i', $listQuery);
			$splitCount = count($split);
			$listQuery = 'SELECT count(*) AS count ';
			for ($i=1; $i<$splitCount; $i++) {
				$listQuery = $listQuery. ' FROM ' .$split[$i];
			}
		}
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');

		$whereQuery .= " WHERE ";
		if(!empty($searchKey) && !empty($searchValue)) {
			$whereQuery .= "$searchKey LIKE '$searchValue%' AND ";
		}

		//module should be enabled or module should be empty then allow
		$moduleActiveCheck = '(nectarcrm_tab.presence IN (0,2) OR nectarcrm_emailtemplates.module IS null OR nectarcrm_emailtemplates.module = "")';
		$listQuery .= $whereQuery. $moduleActiveCheck;

		$sourceModule = $this->get('sourceModule');
		if ($sourceModule) {
			$listQuery .= ' AND nectarcrm_emailtemplates.module= "' . $sourceModule . '" ';
		}

		$listResult = $db->pquery($listQuery, array());
		return $db->query_result($listResult, 0, 'count');
	}

} 