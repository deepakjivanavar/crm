<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Campaigns_Module_Model extends nectarcrm_Module_Model {

	/**
	 * Function to get Specific Relation Query for this Module
	 * @param <type> $relatedModule
	 * @return <type>
	 */
	public function getSpecificRelationQuery($relatedModule) {
		if ($relatedModule === 'Leads') {
			$specificQuery = 'AND nectarcrm_leaddetails.converted = 0';
			return $specificQuery;
		}
		return parent::getSpecificRelationQuery($relatedModule);
 	}
	
	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return false;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if (in_array($sourceModule, array('Leads', 'Accounts', 'Contacts'))) {
			switch($sourceModule) {
				case 'Leads'		: $tableName = 'nectarcrm_campaignleadrel';		$relatedFieldName = 'leadid';		break;
				case 'Accounts'		: $tableName = 'nectarcrm_campaignaccountrel';		$relatedFieldName = 'accountid';	break;
				case 'Contacts'		: $tableName = 'nectarcrm_campaigncontrel';		$relatedFieldName = 'contactid';	break;
			}

			$condition = " nectarcrm_campaign.campaignid NOT IN (SELECT campaignid FROM $tableName WHERE $relatedFieldName = '$record')";
			$pos = stripos($listQuery, 'where');

			if ($pos) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery. ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}

	/**
	 * Function is used to give links in the All menu bar
	 */
	public function getQuickMenuModels() {
		if ($this->isEntityModule()) {
			$moduleName = $this->getName();
			$listViewModel = nectarcrm_ListView_Model::getCleanInstance($moduleName);
			$basicListViewLinks = $listViewModel->getBasicLinks();
		}

		if ($basicListViewLinks) {
			foreach ($basicListViewLinks as $basicListViewLink) {
				if (is_array($basicListViewLink)) {
					$links[] = nectarcrm_Link_Model::getInstanceFromValues($basicListViewLink);
				} else if (is_a($basicListViewLink, 'nectarcrm_Link_Model')) {
					$links[] = $basicListViewLink;
				}
			}
		}
		return $links;
	}

	/*
	 * Function to get supported utility actions for a module
	 */
	function getUtilityActionsNames() {
		return array();
	}

}