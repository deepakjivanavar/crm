<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'modules/Emails/mail.php';
class Inventory_Save_Action extends nectarcrm_Save_Action {
    
    protected function getRecordModelFromRequest(nectarcrm_Request $request) {
		return parent::getRecordModelFromRequest($request);
		
	}
    
}
