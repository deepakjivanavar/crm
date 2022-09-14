<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Potentials_RecipientPreferences_View extends Project_RecipientPreferences_View {

	public function process(nectarcrm_Request $request) {
		$sourceModule = $request->getModule();
		$emailFieldsInfo = $this->getEmailFieldsInfo($sourceModule);
		$viewer = $this->getViewer($request);
		$viewer->assign('EMAIL_FIELDS_LIST', $emailFieldsInfo);
		$viewer->assign('MODULE', $request->getModule());
		$viewer->assign('SOURCE_MODULE', $sourceModule);
		echo $viewer->view('RecipientPreferences.tpl', 'Project', true);
	}
}
