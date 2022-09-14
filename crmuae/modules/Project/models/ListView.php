<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * ListView Model Class for Project module
 */
class Project_ListView_Model extends nectarcrm_ListView_Model {

	/**
	 * Function to get the list of listview links
	 * @param <Array> $linkParams Parameters to be replaced in the link template
	 * @return <Array> - an array of nectarcrm_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$links = parent::getListViewLinks($linkParams);
        $quickLinks = array();
        
        $projectTaskInstance = nectarcrm_Module_Model::getInstance('ProjectTask');
        if($userPrivilegesModel->hasModulePermission($projectTaskInstance->getId())) {
            $quickLinks[] = array(
                                'linktype' => 'LISTVIEWQUICK',
                                'linklabel' => 'Tasks List',
                                'linkurl' => $this->getModule()->getDefaultUrl(),
                                'linkicon' => ''
                            );
        }

        foreach($quickLinks as $quickLink) {
            $links['LISTVIEWQUICK'][] = nectarcrm_Link_Model::getInstanceFromValues($quickLink);
        }
        
		return $links;
	}

}