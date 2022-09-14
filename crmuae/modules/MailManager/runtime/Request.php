<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Request extends nectarcrm_Request {

	public function get($key, $defvalue = '') {
		$value = parent::get($key, $defvalue);
		if (is_array($value)) {
			//For Review: http://stackoverflow.com/questions/8734626/how-to-urlencode-a-multidimensional-array#answer-8734910
			$str = urlencode(serialize($value));
			return unserialize(urldecode($str));
		}
       	return urldecode($value);
	}

	public static function getInstance($request) {
		return new MailManager_Request($request->getAll(), $request->getAll());
	}
}