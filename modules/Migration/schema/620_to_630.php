<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *********************************************************************************/

if(defined('NECTARCRM_UPGRADE')) {
    
	global $adb;

	$query = 'SELECT DISTINCT profileid FROM nectarcrm_profile2utility';
	$result = $adb->pquery($query, array());

	$tabIdsList = array(getTabid('ProjectMilestone'), getTabid('PriceBooks'));
	$actionIdPerms = array(5 => 1, 6 => 1, 10 => 1);

	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$profileId = $adb->query_result($result, $i, 'profileid');

		foreach ($tabIdsList as $tabId) {
			foreach ($actionIdPerms as $actionId => $permission) {
				$isExist = $adb->pquery('SELECT 1 FROM nectarcrm_profile2utility WHERE profileid=? AND tabid=? AND activityid=?', array($profileId, $tabId, $actionId));
				if ($adb->num_rows($isExist)) {
					$query = 'UPDATE nectarcrm_profile2utility SET permission=? WHERE profileid=? AND tabid=? AND activityid=?';
				} else {
					$query = 'INSERT INTO nectarcrm_profile2utility(permission, profileid, tabid, activityid) VALUES (?, ?, ?, ?)';
				}
				Migration_Index_View::ExecuteQuery($query, array($permission, $profileId, $tabId, $actionId));
			}
		}
	}
}

chdir(dirname(__FILE__) . '/../../../');
require_once 'includes/main/WebUI.php';

$pickListFieldName = 'no_of_currency_decimals'; 
$moduleModel = Settings_Picklist_Module_Model::getInstance('Users'); 
$fieldModel = nectarcrm_Field_Model::getInstance($pickListFieldName, $moduleModel); 

if ($fieldModel) { 
    $moduleModel->addPickListValues($fieldModel, 0); 
    $moduleModel->addPickListValues($fieldModel, 1); 
    
    $pickListValues = nectarcrm_Util_Helper::getPickListValues($pickListFieldName); 
    $moduleModel->updateSequence($pickListFieldName, $pickListValues); 
} 
?>
