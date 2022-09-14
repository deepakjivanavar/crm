<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_UpdateLoginDetails extends CustomerPortal_API_Abstract {

	function process(CustomerPortal_API_Request $request) {
		$response = new CustomerPortal_API_Response();
		$customer = $this->getActiveCustomer();
		$status = $request->get('status');
		global $adb;
		$currentTime = $adb->formatDate(date('YmdHis'), true);

		if ($status == 'Login') {
			$sql = 'UPDATE nectarcrm_portalinfo SET login_time=? WHERE id=?';
			$adb->pquery($sql, array($currentTime, $customer->id));
		} elseif ($status == 'Logout') {
			$sql = 'UPDATE nectarcrm_portalinfo SET logout_time=?, last_login_time=login_time WHERE id=?';
			$adb->pquery($sql, array($currentTime, $customer->id));
		}

		$response->setResult(true);
		return $response;
	}

}
