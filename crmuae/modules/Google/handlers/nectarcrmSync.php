<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/modules/WSAPP/synclib/handlers/nectarcrmSyncEventHandler.php');
class Google_nectarcrmSync_Handler extends WSAPP_nectarcrmSyncEventHandler {
	public function getSyncServerInstance(){
		return new Google_SyncServer_Controller();
	}
}
