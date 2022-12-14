<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'libraries/HTTP_Session/Session.php';

class Mobile_API_Session {

	function __construct() {
	}

	static function destroy($sessionid = false) {
		HTTP_Session::destroy($sessionid);
	}

	static function init($sessionid = false) {
		if(empty($sessionid)) {
			HTTP_Session::start(null, null);
			$sessionid = HTTP_Session::id();
		} else {
			HTTP_Session::start(null, $sessionid);
		}

		if(HTTP_Session::isIdle() || HTTP_Session::isExpired()) {
			return false;
		}
		return $sessionid;
	}

	static function get($key, $defvalue = '') {
		return HTTP_Session::get($key, $defvalue);
	}

	static function set($key, $value) {
		HTTP_Session::set($key, $value);
	}

}