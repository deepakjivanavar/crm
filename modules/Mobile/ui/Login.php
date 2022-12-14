<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

include_once dirname(__FILE__) . '/../api/ws/Login.php';

class Mobile_UI_Login  extends Mobile_WS_Login {
	
	function process(Mobile_API_Request $request) {
		$viewer = new Mobile_UI_Viewer();
		return $viewer->process('generic/Login.tpl');
	}

}