<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_DetailView_Model extends nectarcrm_DetailView_Model {
	/**
	 * Function to get the detail view links (links and widgets)
	 * @param <array> $linkParams - parameters which will be used to calicaulate the params
	 * @return <array> - array of link models in the format as below
	 *                   array('linktype'=>list of link models);
	 */
	public function getDetailViewLinks($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		$linkModelList = parent::getDetailViewLinks($linkParams);
		$recordModel = $this->getRecord();
		$invoiceModuleModel = nectarcrm_Module_Model::getInstance('Invoice');
		$quoteModuleModel = nectarcrm_Module_Model::getInstance('Quotes');
		$salesOrderModuleModel = nectarcrm_Module_Model::getInstance('SalesOrder');
		$projectModuleModel = nectarcrm_Module_Model::getInstance('Project');

		$emailModuleModel = nectarcrm_Module_Model::getInstance('Emails');

		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
			$basicActionLink = array(
				'linktype' => 'DETAILVIEWBASIC',
				'linklabel' => 'LBL_SEND_EMAIL',
				'linkurl' => 'javascript:nectarcrm_Detail_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().
								'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
				'linkicon' => ''
			);
			$linkModelList['DETAILVIEWBASIC'][] = nectarcrm_Link_Model::getInstanceFromValues($basicActionLink);
		}

		if($currentUserModel->hasModuleActionPermission($invoiceModuleModel->getId(), 'CreateView')) {
			$basicActionLink = array(
				'linktype' => 'DETAILVIEW',
				'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($invoiceModuleModel->getSingularLabelKey(), 'Invoice'),
				'linkurl' => $recordModel->getCreateInvoiceUrl(),
				'linkicon' => ''
			);
			$linkModelList['DETAILVIEW'][] = nectarcrm_Link_Model::getInstanceFromValues($basicActionLink);
		}

		if($currentUserModel->hasModuleActionPermission($quoteModuleModel->getId(), 'CreateView')) {
			$basicActionLink = array(
				'linktype' => 'DETAILVIEW',
				'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($quoteModuleModel->getSingularLabelKey(), 'Quotes'),
				'linkurl' => $recordModel->getCreateQuoteUrl(),
				'linkicon' => ''
			);
			$linkModelList['DETAILVIEW'][] = nectarcrm_Link_Model::getInstanceFromValues($basicActionLink);
		}

		if($currentUserModel->hasModuleActionPermission($salesOrderModuleModel ->getId(), 'CreateView')) {
			$basicActionLink = array(
				'linktype'	=> 'DETAILVIEW',
				'linklabel' => vtranslate('LBL_CREATE').' '.vtranslate($salesOrderModuleModel ->getSingularLabelKey(), 'SalesOrder'),
				'linkurl'	=> $recordModel->getCreateSalesOrderUrl(),
				'linkicon'	=> ''
			);
			$linkModelList['DETAILVIEW'][] = nectarcrm_Link_Model::getInstanceFromValues($basicActionLink);
		}

		$CalendarActionLinks[] = array();
		$CalendarModuleModel = nectarcrm_Module_Model::getInstance('Calendar');
		if($currentUserModel->hasModuleActionPermission($CalendarModuleModel->getId(), 'CreateView')) {
			$CalendarActionLinks[] = array(
					'linktype' => 'DETAILVIEW',
					'linklabel' => 'LBL_ADD_EVENT',
					'linkurl' => $recordModel->getCreateEventUrl(),
					'linkicon' => ''
			);

			$CalendarActionLinks[] = array(
					'linktype' => 'DETAILVIEW',
					'linklabel' => 'LBL_ADD_TASK',
					'linkurl' => $recordModel->getCreateTaskUrl(),
					'linkicon' => ''
			);
		}

		if($currentUserModel->hasModuleActionPermission($projectModuleModel->getId(), 'CreateView') && !$recordModel->isPotentialConverted()) {
			$basicActionLink = array(
				'linktype' => 'DETAILVIEWBASIC',
				'linklabel' => vtranslate('LBL_CREATE_PROJECT', $recordModel->getModuleName()),
				'linkurl' => 'Javascript:Potentials_Detail_Js.convertPotential("'.$recordModel->getConvertPotentialUrl().'",this);',
				'linkicon' => ''
			);
			$linkModelList['DETAILVIEWBASIC'][] = nectarcrm_Link_Model::getInstanceFromValues($basicActionLink);
		}
		
		foreach($CalendarActionLinks as $basicLink) {
			$linkModelList['DETAILVIEW'][] = nectarcrm_Link_Model::getInstanceFromValues($basicLink);
		}

		return $linkModelList;
	}

	/**
	 * Function to get the detail view widgets
	 * @return <Array> - List of widgets , where each widget is an nectarcrm_Link_Model
	 */
	public function getWidgets() {
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$widgetLinks = parent::getWidgets();
		$widgets = array();

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

		$contactsInstance = nectarcrm_Module_Model::getInstance('Contacts');
		if($userPrivilegesModel->hasModuleActionPermission($contactsInstance->getId(), 'DetailView')) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($contactsInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'LBL_RELATED_CONTACTS',
					'linkName'	=> $contactsInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=Contacts&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$contactsInstance->getQuickCreateUrl()
			);
		}

		$productsInstance = nectarcrm_Module_Model::getInstance('Products');
		if($userPrivilegesModel->hasModuleActionPermission($productsInstance->getId(), 'DetailView')) {
			$createPermission = $userPrivilegesModel->hasModuleActionPermission($productsInstance->getId(), 'CreateView');
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'LBL_RELATED_PRODUCTS',
					'linkName'	=> $productsInstance->getName(),
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&relatedModule=Products&mode=showRelatedRecords&page=1&limit=5',
					'action'	=>	($createPermission == true) ? array('Add') : array(),
					'actionURL' =>	$productsInstance->getQuickCreateUrl()
			);
		}

		foreach ($widgets as $widgetDetails) {
			$widgetLinks[] = nectarcrm_Link_Model::getInstanceFromValues($widgetDetails);
		}

		return $widgetLinks;
	}
}