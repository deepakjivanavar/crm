<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/modules/WSAPP/synclib/connectors/nectarcrmConnector.php');
vimport('~~/modules/WSAPP/SyncServer.php');
include_once 'include/Webservices/Query.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';

class Google_nectarcrm_Connector extends WSAPP_nectarcrmConnector {

	/**
	 * function to push data to nectarcrm
	 * @param type $recordList
	 * @param type $syncStateModel
	 * @return type
	 */
	public function push($recordList, $syncStateModel) {
		return parent::push($recordList, $syncStateModel);
	}

	/**
	 * function to get data from nectarcrm
	 * @param type $syncStateModel
	 * @return type
	 */
	public function pull($syncStateModel) {
		$records = parent::pull($syncStateModel);
		return $records;
	}

	/**
	 * function that returns syncTrackerhandler name
	 * @return string
	 */
	public function getSyncTrackerHandlerName() {
		return 'Google_nectarcrmSyncHandler';
	}
	
}
