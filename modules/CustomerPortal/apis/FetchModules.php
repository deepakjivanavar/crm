<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_FetchModules extends CustomerPortal_API_Abstract {

	function process(CustomerPortal_API_Request $request) {
		$current_user = $this->getActiveUser();
		$response = new CustomerPortal_API_Response();
		global $adb;

		if ($current_user) {
			$result = array();
			$customerId = vtws_getWebserviceEntityId('Contacts', $this->getActiveCustomer()->id);
			$accountId = $this->getParent($customerId);
			$user_id = CustomerPortal_Settings_Utils::getDefaultAssignee();
			$result['contact_id'] = array('value' => $customerId, 'label' => nectarcrm_Util_Helper::fetchRecordLabelForId($customerId));

			if (!empty($accountId)) {
				$result['account_id'] = array('value' => $accountId, 'label' => nectarcrm_Util_Helper::fetchRecordLabelForId($accountId));
			}

			$result['user_id'] = array('value' => $user_id, 'label' => decode_html(trim(vtws_getName($user_id, $current_user))));
			$sql = "SELECT nectarcrm_relatedlists.label, nectarcrm_customerportal_tabs.tabid, nectarcrm_customerportal_tabs.sequence,
                    nectarcrm_customerportal_tabs.createrecord,nectarcrm_customerportal_tabs.editrecord,nectarcrm_customerportal_fields.records_visible 
                    FROM nectarcrm_customerportal_tabs INNER JOIN nectarcrm_tab on nectarcrm_tab.tabid = nectarcrm_customerportal_tabs.tabid 
                    and nectarcrm_tab.presence=? INNER JOIN nectarcrm_relatedlists ON nectarcrm_customerportal_tabs.tabid =nectarcrm_relatedlists.related_tabid 
                    INNER JOIN nectarcrm_customerportal_fields ON nectarcrm_customerportal_fields.tabid = nectarcrm_customerportal_tabs.tabid WHERE   
                    nectarcrm_customerportal_tabs.visible =? GROUP BY nectarcrm_customerportal_tabs.tabid ORDER BY nectarcrm_customerportal_tabs.sequence ASC;";
			$sqlResult = $adb->pquery($sql, array(0, 1));
			$num_rows = $adb->num_rows($sqlResult);

			$modules = array('types' => array(), 'information' => array());
			for ($i = 0; $i < $num_rows; $i++) {
				$moduleId = $adb->query_result($sqlResult, $i, 'tabid');
				$moduleName = nectarcrm_Functions::getModuleName($moduleId);
				if (!nectarcrm_Runtime::isRestricted('modules', $moduleName)) {
					$modules['types'][] = $moduleName;
					$modules['information'][$moduleName] = array(
						'name' => $moduleName,
						'label' => $adb->query_result($sqlResult, $i, 'label'),
						'uiLabel' => decode_html(vtranslate($moduleName, $moduleName)),
						'order' => $adb->query_result($sqlResult, $i, 'sequence'),
						'create' => $adb->query_result($sqlResult, $i, 'createrecord'),
						'edit' => $adb->query_result($sqlResult, $i, 'editrecord'),
						'recordvisibility' => $adb->query_result($sqlResult, $i, 'records_visible')
					);
				}
			}

			$result['modules'] = $modules;
			$response->setResult($result);
			return $response;
		}
	}

}
