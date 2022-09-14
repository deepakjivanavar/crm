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

/** Events for today alert */
class Mobile_WS_AlertModel_EventsOfMineToday extends Mobile_WS_AlertModel {
	function __construct() {
		parent::__construct();
		$this->name = 'Your events for the day';
		$this->moduleName = 'Calendar';
		$this->refreshRate= 1 * (24* 60 * 60); // 1 day
		$this->description='Alert sent when events are scheduled for the day';
	}
	
	function query() {
		$today = date('Y-m-d');
		$sql = "SELECT crmid, activitytype FROM nectarcrm_activity INNER JOIN 
				nectarcrm_crmentity ON nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_crmentity.smownerid=? AND 
				nectarcrm_activity.activitytype <> 'Emails' AND 
				(nectarcrm_activity.date_start = '{$today}' OR nectarcrm_activity.due_date = '{$today}')";
		return $sql;
	}
	
	function queryParameters() {
		return array($this->getUser()->id);
	}
}
