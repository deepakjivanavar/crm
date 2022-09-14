<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Reports_Module_Model extends nectarcrm_Module_Model {

	/**
	 * Function deletes report
	 * @param Reports_Record_Model $reportModel
	 */
	function deleteRecord(Reports_Record_Model $reportModel) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$subOrdinateUsers = $currentUser->getSubordinateUsers();

		$subOrdinates = array();
		foreach($subOrdinateUsers as $id=>$name) {
			$subOrdinates[] = $id;
		}

		$owner = $reportModel->get('owner');

		if($currentUser->isAdminUser() || in_array($owner, $subOrdinates) || $owner == $currentUser->getId()) {
			$reportId = $reportModel->getId();
			$db = PearDatabase::getInstance();

			$db->pquery('DELETE FROM nectarcrm_selectquery WHERE queryid = ?', array($reportId));

			$db->pquery('DELETE FROM nectarcrm_report WHERE reportid = ?', array($reportId));

			$db->pquery('DELETE FROM nectarcrm_schedulereports WHERE reportid = ?', array($reportId));

                        $db->pquery('DELETE FROM nectarcrm_reporttype WHERE reportid = ?', array($reportId));

			$result = $db->pquery('SELECT * FROM nectarcrm_homereportchart WHERE reportid = ?',array($reportId));
			$numOfRows = $db->num_rows($result);
			for ($i = 0; $i < $numOfRows; $i++) {
				$homePageChartIdsList[] = $adb->query_result($result, $i, 'stuffid');
			}
			if ($homePageChartIdsList) {
				$deleteQuery = 'DELETE FROM nectarcrm_homestuff WHERE stuffid IN (' . implode(",", $homePageChartIdsList) . ')';
				$db->pquery($deleteQuery, array());
			}
                        
                        if($reportModel->get('reporttype') == 'chart'){
                            nectarcrm_Widget_Model::deleteChartReportWidgets($reportId);
                        }
			return true;
		}
		return false;
	}

	/**
	 * Function returns quick links for the module
	 * @return <Array of nectarcrm_Link_Model>
	 */
	function getSideBarLinks() {
		$quickLinks = array(
			array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => 'LBL_REPORTS',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
			),
		);
		foreach($quickLinks as $quickLink) {
			$links['SIDEBARLINK'][] = nectarcrm_Link_Model::getInstanceFromValues($quickLink);
		}

		$quickWidgets = array(
			array(
				'linktype' => 'SIDEBARWIDGET',
				'linklabel' => 'LBL_RECENTLY_MODIFIED',
				'linkurl' => 'module='.$this->get('name').'&view=IndexAjax&mode=showActiveRecords',
				'linkicon' => ''
			),
		);
		foreach($quickWidgets as $quickWidget) {
			$links['SIDEBARWIDGET'][] = nectarcrm_Link_Model::getInstanceFromValues($quickWidget);
		}

		return $links;
	}

	/**
	 * Function returns the recent created reports
	 * @param <Number> $limit
	 * @return <Array of Reports_Record_Model>
	 */
	function getRecentRecords($limit = 10) {
		$db = PearDatabase::getInstance();

		$result = $db->pquery('SELECT * FROM nectarcrm_report 
						INNER JOIN nectarcrm_reportmodules ON nectarcrm_reportmodules.reportmodulesid = nectarcrm_report.reportid
						INNER JOIN nectarcrm_tab ON nectarcrm_tab.name = nectarcrm_reportmodules.primarymodule AND presence = 0
						ORDER BY reportid DESC LIMIT ?', array($limit));
		$rows = $db->num_rows($result);

		$recentRecords = array();
		for($i=0; $i<$rows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$recentRecords[$row['reportid']] = $this->getRecordFromArray($row);
		}
		return $recentRecords;
	}

	/**
	 * Function returns the report folders
	 * @return <Array of Reports_Folder_Model>
	 */
	function getFolders() {
		return Reports_Folder_Model::getAll();
	}

	/**
	 * Function to get the url for add folder from list view of the module
	 * @return <string> - url
	 */
	function getAddFolderUrl() {
		return 'index.php?module='.$this->get('name').'&view=EditFolder';
	}
    
    /**
     * Function to check if the extension module is permitted for utility action
     * @return <boolean> true
     */
    public function isUtilityActionEnabled() {
        return true;
    }

	/**
	 * Function is a callback from nectarcrm_Link model to check permission for the links
	 * @param type $linkData
	 */
	public function checkLinkAccess($linkData) {
		$privileges = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$reportModuleModel = nectarcrm_Module_Model::getInstance('Reports');
		return $privileges->hasModulePermission($reportModuleModel->getId());
	}
    
    /*
     * Function to get supported utility actions for a module
     */
    function getUtilityActionsNames() {
        return array('Export');
    }
}