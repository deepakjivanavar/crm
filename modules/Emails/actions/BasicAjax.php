<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_BasicAjax_Action extends nectarcrm_Action_Controller {

	public function checkPermission(nectarcrm_Request $request) {
		return;
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->get('module');
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$searchValue = $request->get('searchValue');

		$emailsResult = array();
		if ($searchValue) {
			$emailsResult = $moduleModel->searchEmails($request->get('searchValue'));
		}

		$response = new nectarcrm_Response();
		$response->setResult($emailsResult);
		$response->emit();
	}
}

?>
