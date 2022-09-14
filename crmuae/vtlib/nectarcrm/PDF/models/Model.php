<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
class nectarcrm_PDF_Model {
	protected $values = array();
	
	function set($key, $value) {
		$this->values[$key] = $value;
	}

	function get($key, $defvalue='') {
		return (isset($this->values[$key]))? $this->values[$key] : $defvalue;
	}
	
	function count() {
		return count($this->values);
	}
	
	function keys() {
		return array_keys($this->values);
	}
}