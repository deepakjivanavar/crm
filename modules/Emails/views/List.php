<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Emails_List_View extends nectarcrm_List_View {

	public function preProcess(nectarcrm_Request $request) {
	}

	public function process(nectarcrm_Request $request) {
		header('Location: index.php?module=MailManager&view=List');
	}
}