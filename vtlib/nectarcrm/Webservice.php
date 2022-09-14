<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/nectarcrm/Utils.php');

/**
 * Provides API to work with nectarcrm CRM Webservice (available from nectarcrm 5.1)
 * @package vtlib
 */
class nectarcrm_Webservice {
	
	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delim=true) {
		nectarcrm_Utils::Log($message, $delim);
	}

	/**
	 * Initialize webservice for the given module
	 * @param nectarcrm_Module Instance of the module.
	 */
	static function initialize($moduleInstance) {
		if($moduleInstance->isentitytype) {
			// TODO: Enable support when webservice API support is added.
			if(function_exists('vtws_addDefaultModuleTypeEntity')) { 
				vtws_addDefaultModuleTypeEntity($moduleInstance->name);
				self::log("Initializing webservices support ...DONE");
			}
		}
	}

	/**
	 * Initialize webservice for the given module
	 * @param nectarcrm_Module Instance of the module.
	 */
	static function uninitialize($moduleInstance) {
		if($moduleInstance->isentitytype) {
			// TODO: Enable support when webservice API support is added.
			if(function_exists('vtws_deleteWebserviceEntity')) { 
				vtws_deleteWebserviceEntity($moduleInstance->name);
				self::log("De-Initializing webservices support ...DONE");
			}
		}
	}
}
?>
