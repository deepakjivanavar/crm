<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ********************************************************************************** */

class Settings_MailConverter_SaveFolders_Action extends Settings_nectarcrm_Index_Action {

    public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);
		$checkedFolders = $request->get('folders');
		$folders = explode(',', $checkedFolders);
		Settings_MailConverter_Module_Model::updateFolders($recordId, $folders);

		$response = new nectarcrm_Response();

		$result = array('message' => vtranslate('LBL_SAVED_SUCCESSFULLY', $qualifiedModuleName));
		$result['id'] = $recordId;
		$response->setResult($result);

		$response->emit();
	}

}

?>