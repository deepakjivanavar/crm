<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

// TODO This is a stop-gap measure to have the
// user continue working with Calendar when dropping from Event View.
class Events_Calendar_View extends nectarcrm_Index_View {
	
	public function preProcess(nectarcrm_Request $request, $display = true) {}
	public function postProcess(nectarcrm_Request $request) {}
	
	public function process(nectarcrm_Request $request) {
		header("Location: index.php?module=Calendar&view=Calendar");
	}
}
