<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Project_DetailView_Model extends nectarcrm_DetailView_Model {

	/**
	 * Function to get the detail view widgets
	 * @return <Array> - List of widgets , where each widget is an nectarcrm_Link_Model
	 */
	public function getWidgets() {
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$widgetLinks = parent::getWidgets();
		$widgets = array();

		$helpDeskInstance = nectarcrm_Module_Model::getInstance('HelpDesk');
		if($userPrivilegesModel->hasModuleActionPermission($helpDeskInstance->getId(), 'DetailView')) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($helpDeskInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'HelpDesk',
					'linkName'	=> $helpDeskInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=HelpDesk&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$helpDeskInstance->getQuickCreateUrl()
				);
		}

		$projectMileStoneInstance = nectarcrm_Module_Model::getInstance('ProjectMilestone');
		if($userPrivilegesModel->hasModuleActionPermission($projectMileStoneInstance->getId(), 'DetailView') && $userPrivilegesModel->hasModulePermission($projectMileStoneInstance->getId())) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($projectMileStoneInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'LBL_MILESTONES',
					'linkName'	=> $projectMileStoneInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=ProjectMilestone&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$projectMileStoneInstance->getQuickCreateUrl()
			);
		}

		$projectTaskInstance = nectarcrm_Module_Model::getInstance('ProjectTask');
		if($userPrivilegesModel->hasModuleActionPermission($projectTaskInstance->getId(), 'DetailView') && $userPrivilegesModel->hasModulePermission($projectTaskInstance->getId())) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($projectTaskInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'LBL_TASKS',
					'linkName'	=> $projectTaskInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=ProjectTask&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$projectTaskInstance->getQuickCreateUrl()
			);
		}


		$documentsInstance = nectarcrm_Module_Model::getInstance('Documents');
		if($userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'DetailView')) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'Documents',
					'linkName'	=> $documentsInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=Documents&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$documentsInstance->getQuickCreateUrl()
			);
		}

		foreach ($widgets as $widgetDetails) {
			$widgetLinks[] = nectarcrm_Link_Model::getInstanceFromValues($widgetDetails);
		}

		return $widgetLinks;
	}

	/**
	 * Function to get the detail view related links
	 * @return <array> - list of links parameters
	 */
	public function getDetailViewRelatedLinks() {
		$relatedLinks = parent::getDetailViewRelatedLinks();
		$recordModel = $this->getRecord();
		$moduleName = $recordModel->getModuleName();
		$relatedLinks[] = array(
			'linktype' => 'DETAILVIEWTAB',
			'linklabel' => vtranslate('LBL_CHART', $moduleName),
			'linkurl' => $recordModel->getDetailViewUrl().'&mode=showChart',
			'linkicon' => ''
			);

		return $relatedLinks;
	}
}