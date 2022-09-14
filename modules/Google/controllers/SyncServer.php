<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
vimport('~~/modules/WSAPP/SyncServer.php');

class Google_SyncServer_Controller extends SyncServer{
	function getDestinationHandleDetails(){
		return wsapp_getHandler('Google_nectarcrmHandler');
	}
}
