<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../Alert.php';

/** Pending Ticket Alert */
class Mobile_WS_AlertModel_PendingTicketsOfMine extends Mobile_WS_AlertModel {
	function __construct() {
		parent::__construct();
		$this->name = 'Pending Ticket Alert';
		$this->moduleName = 'HelpDesk';
		$this->refreshRate= 1 * (24* 60 * 60); // 1 day
		$this->description='Alert sent when ticket assigned is not yet closed';
	}
	
	function query() {
		$sql = "SELECT crmid FROM nectarcrm_troubletickets INNER JOIN 
				nectarcrm_crmentity ON nectarcrm_crmentity.crmid=nectarcrm_troubletickets.ticketid 
				WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_crmentity.smownerid=? AND 
				nectarcrm_troubletickets.status <> 'Closed'";
		return $sql;
	}
	
	function queryParameters() {
		return array($this->getUser()->id);
	}
}