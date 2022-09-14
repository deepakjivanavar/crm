<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_SearchFaqs extends CustomerPortal_API_Abstract {

	function process(CustomerPortal_API_Request $request) {
		$response = new CustomerPortal_API_Response();
		$current_user = $this->getActiveUser();
		$module = "Faq";
		global $adb;

		if ($current_user) {
			if (!CustomerPortal_Utils::isModuleActive($module)) {
				throw new Exception($module." not accessible", 1412);
				exit;
			}

			$searchKey = $request->get('searchKey');
			$searchKey = decode_html(htmlspecialchars_decode($searchKey));
			$searchKey = addslashes(addslashes($searchKey));
			$searchFields = array('question', 'answer');

			$sql = sprintf('SELECT id,question,answer,status FROM nectarcrm_faq WHERE status=? AND (');
			$sql.= implode(" LIKE '%$searchKey%' OR ", $searchFields);
			$sql.= " LIKE '%".$searchKey."%') ;";
			$sqlResult = $adb->pquery($sql, array("Published"));
			$num_rows = $adb->num_rows($sqlResult);
			$data = array();
			for ($i = 0; $i < $num_rows; $i++) {
				$record = array();
				$record['question'] = decode_html($adb->query_result($sqlResult, $i, 'question'));
				$record['faq_answer'] = decode_html($adb->query_result($sqlResult, $i, 'answer'));
				$record['faqstatus'] = decode_html($adb->query_result($sqlResult, $i, 'status'));
				$record['id'] = vtws_getWebserviceEntityId("Faq", $adb->query_result($sqlResult, $i, 'id'));
				$data[] = $record;
			}

			$response->setResult($data);
			return $response;
		}
	}

}
