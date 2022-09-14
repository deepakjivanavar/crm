<?php

/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PBXManager_DetailView_Model extends nectarcrm_DetailView_Model {
    
     /**
    * Overrided to remove Edit button, Duplicate button
    * To remove related links
    */
    public function getDetailViewLinks($linkParams) {
		$linkTypes = array('DETAILVIEWBASIC','DETAILVIEW');
		$moduleModel = $this->getModule();
		$recordModel = $this->getRecord();

		$moduleName = $moduleModel->getName();
		$recordId = $recordModel->getId();

		$detailViewLink = array();

		$linkModelListDetails = nectarcrm_Link_Model::getAllByType($moduleModel->getId(),$linkTypes,$linkParams);
		//Mark all detail view basic links as detail view links.
		//Since ui will be look ugly if you need many basic links
		$detailViewBasiclinks = $linkModelListDetails['DETAILVIEWBASIC'];
		unset($linkModelListDetails['DETAILVIEWBASIC']);

		if(Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
			$deletelinkModel = array(
					'linktype' => 'DETAILVIEW',
					'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_'. $moduleName, $moduleName)),
					'linkurl' => 'javascript:nectarcrm_Detail_Js.deleteRecord("'.$recordModel->getDeleteUrl().'")',
					'linkicon' => ''
			);
			$linkModelList['DETAILVIEW'][] = nectarcrm_Link_Model::getInstanceFromValues($deletelinkModel);
		}

		if(!empty($detailViewBasiclinks)) {
			foreach($detailViewBasiclinks as $linkModel) {
				// Remove view history, needed in nectarcrm5 to see history but not in nectarcrm6
				if($linkModel->linklabel == 'View History') {
					continue;
				}
				$linkModelList['DETAILVIEW'][] = $linkModel;
			}
		}

		$widgets = $this->getWidgets();
		foreach($widgets as $widgetLinkModel) {
			$linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
		}
		
		return $linkModelList;
	}
}
