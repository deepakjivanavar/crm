<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ************************************************************************************/

class ProjectTask_Module_Model extends nectarcrm_Module_Model {

	public function getSideBarLinks($linkParams) {
		$linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
		$links = parent::getSideBarLinks($linkParams);
		unset($links['SIDEBARLINK']);

		$quickLinks = array(
			array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_PROJECTS_LIST',
				'linkurl' => $this->getProjectsListUrl(),
				'linkicon' => '',
			),
			array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_TASKS_LIST',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
			),
            array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_MILESTONES_LIST',
				'linkurl' => $this->getMilestonesListUrl(),
				'linkicon' => '',
			),
		);
		foreach($quickLinks as $quickLink) {
			$links['SIDEBARLINK'][] = nectarcrm_Link_Model::getInstanceFromValues($quickLink);
		}

		return $links;
	}

	public function getProjectsListUrl() {
		$taskModel = nectarcrm_Module_Model::getInstance('Project');
		return $taskModel->getListViewUrl();
	}
	
    public function getMilestonesListUrl() {
    $milestoneModel = nectarcrm_Module_Model::getInstance('ProjectMilestone');
    return $milestoneModel->getListViewUrl();
}
	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return false;
	}
	
	/**
	 * Function to get list of field for related list
	 * @return <Array> empty array
	 */
	public function getConfigureRelatedListFields(){
		$relatedListFields = parent::getConfigureRelatedListFields();
		if (!$relatedListFields) {
			//If there is no summary view fieldsenabled, 
			//default related list field values should show in related list
			$relatedListDefaultFields = $this->getRelatedListFields();
			foreach($relatedListDefaultFields as $fieldName) {
				$fieldModel = nectarcrm_Field_Model::getInstance($fieldName, $this);
				if($fieldModel && $fieldModel->isViewableInDetailView()) {
					$relatedListFields[$fieldName] = $fieldName;
				}
			}
		}
		//ProjectTask Progress and Status should show in Projects summary view
		if(!$relatedListFields['projecttaskstatus']) {
			$fieldModel = nectarcrm_Field_Model::getInstance('projecttaskstatus', $this);
			if($fieldModel && $fieldModel->isViewableInDetailView()) {
				$relatedListFields['projecttaskstatus'] = 'projecttaskstatus';
			}
		}
		if(!$relatedListFields['projecttaskprogress']) {
			$fieldModel = nectarcrm_Field_Model::getInstance('projecttaskprogress', $this);
			if($fieldModel && $fieldModel->isViewableInDetailView()) {
				$relatedListFields['projecttaskprogress'] = 'projecttaskprogress';
			}
		}

		return $relatedListFields;
	}

	/*
	 * Function to get supported utility actions for a module
	 */
	function getUtilityActionsNames() {
		return array('Import', 'Export', 'DuplicatesHandling');
	}
}