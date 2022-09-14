<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'includes/http/Response.php';

class MailManager_Response extends nectarcrm_Response {

	/**
	 * Emit response wrapper as JSONString
	 */
	protected function emitJSON() {
		require_once 'include/Zend/Json/Encoder.php';
		echo Zend_Json_Encoder::encode($this->prepareResponse(), false);
	}

}