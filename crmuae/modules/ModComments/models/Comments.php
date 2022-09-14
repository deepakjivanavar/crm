<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
class ModComments_CommentsModel {
	private $data;
	
	static $ownerNamesCache = array();
	
	function __construct($datarow) {
		$this->data = $datarow;
	}
	
	function author() {
		$authorid = $this->data['smcreatorid'];
		if(!isset(self::$ownerNamesCache[$authorid])) {
			self::$ownerNamesCache[$authorid] = getOwnerName($authorid);
		}
		return self::$ownerNamesCache[$authorid];
	}
	
	function timestamp(){
		$date = new DateTimeField($this->data['modifiedtime']);
		return $date->getDisplayDateTimeValue();
	}
	
	function content() {
		return decode_html($this->data['commentcontent']);
	}
}