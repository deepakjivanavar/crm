<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_FetchRelatedModules extends CustomerPortal_API_Abstract {

	function process(CustomerPortal_API_Request $request) {
		$current_user = $this->getActiveUser();
		$response = new CustomerPortal_API_Response();

		if ($current_user) {
			$module = $request->get("module");

			if (!CustomerPortal_Utils::isModuleActive($module)) {
				throw new Exception("Module not accessible", 1412);
				exit;
			}
			global $adb;
			$sql = "SELECT  nectarcrm_customerportal_relatedmoduleinfo.relatedmodules FROM  nectarcrm_customerportal_relatedmoduleinfo 
                    INNER JOIN nectarcrm_tab ON  nectarcrm_customerportal_relatedmoduleinfo.tabid = nectarcrm_tab.tabid 
                    WHERE nectarcrm_tab.name= ?";
			$result = $adb->pquery($sql, array($module));
			if ($adb->num_rows($result) > 0) {
				$relatedModulesJSON = $adb->query_result($result, 0, 'relatedmodules');
				$data = Zend_Json::decode(decode_html($relatedModulesJSON));
				$relatedModules = array();
				foreach ($data as $module) {
					if ($module["value"] == 1 && CustomerPortal_Utils::isModuleActive($module["name"])) {
						$relatedModules[] = $module["name"];
					}
				}
				$response->setResult($relatedModules);
			}
		}
		return $response;
	}

}
