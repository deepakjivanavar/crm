<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_Settings_Utils {

	static function getDefaultMode($module) {
		global $adb;
		$sql = "SELECT nectarcrm_customerportal_fields.records_visible FROM nectarcrm_customerportal_fields
				INNER JOIN nectarcrm_tab ON nectarcrm_customerportal_fields.tabid= nectarcrm_tab.tabid WHERE nectarcrm_tab.name= ?";
		$result = $adb->pquery($sql, array($module));
		$IsVisible = $adb->query_result($result, 0, 'records_visible');

		if ($IsVisible) {
			return 'all';
		} else {
			return 'mine';
		}
	}

	static function getDefaultAssignee() {
		global $adb;
		$sql = "SELECT default_assignee FROM nectarcrm_customerportal_settings LIMIT 1";
		$result = $adb->pquery($sql);
		$default_assignee = $adb->query_result($result, 0, 'default_assignee');

		if (!empty($default_assignee)) {
			$userId = vtws_getWebserviceEntityId('Users', $default_assignee);
			return $userId;
		}
	}

}
