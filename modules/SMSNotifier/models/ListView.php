<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class SMSNotifier_ListView_Model extends nectarcrm_ListView_Model {

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of nectarcrm_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = $this->getModule();
		$moduleName = $moduleModel->getName();

		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = nectarcrm_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		if($currentUserModel->isAdminUser()) {
			$settingsLinks = $this->getSettingLinks();
			foreach($settingsLinks as $settingsLink) {
				$links['LISTVIEWSETTING'][] = nectarcrm_Link_Model::getInstanceFromValues($settingsLink);
			}
		}
		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  nectarcrm_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = $this->getModule();
		$moduleName = $moduleModel->getName();

		$linkTypes = array('LISTVIEWMASSACTION');
		$links = nectarcrm_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$massActionLink = array();
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => vtranslate('LBL_DELETE', $moduleName),
					'linkurl' => 'javascript:nectarcrm_List_Js.massDeleteRecords("index.php?module='.$moduleName.'&action=MassDelete");',
					'linkicon' => ''
			);
		}
		$links['LISTVIEWMASSACTION'][] = nectarcrm_Link_Model::getInstanceFromValues($massActionLink);

		return $links;
	}
}
