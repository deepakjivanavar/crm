<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class SMSProvider {

	static function getInstance($providername) {
		if (!empty($providername)) {
			$providername = trim($providername);

			$filepath = dirname(__FILE__) . "/../providers/{$providername}.php";
			checkFileAccessForInclusion($filepath);

			$className = "SMSNotifier_" . $providername . "_Provider";
			if (!class_exists($className)) {
				include_once $filepath;
			}
			return new $className();
		}
		return false;
	}

	static function listAll() {
		$providers = array();
		if ($handle = opendir(dirname(__FILE__) . '/../providers')) {
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, array('.', '..', '.svn', 'CVS'))) {
					if (preg_match("/(.*)\.php$/", $file, $matches)) {
						$providers[] = $matches[1];
					}
				}
			}
		}
		return $providers;
	}

}

?>