<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Folder_Action extends nectarcrm_Action_Controller {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showMailContent');
	}

	function checkPermission(nectarcrm_Request $request) {
		return true;
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function to show body of all the mails in a folder
	 * @param nectarcrm_Request $request
	 */
	public function showMailContent(nectarcrm_Request $request) {
		$mailIds = $request->get("mailids");
		$folderName = $request->get("folderName");

		$model = MailManager_Mailbox_Model::activeInstance();
		$connector = MailManager_Connector_Connector::connectorWithModel($model, $folderName);

		$mailContents = array();
		foreach ($mailIds as $msgNo) {
			$message = $connector->openMail($msgNo, $folderName);
			$mailContents[$msgNo] = $message->getInlineBody();
		}
		$response = new nectarcrm_Response();
		$response->setResult($mailContents);
		$response->emit();
	}

}
