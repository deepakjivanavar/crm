<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

/** Classes to avoid logging */
class LoggerPropertyConfigurator {
	
	static $singleton = false;
	
	function __construct() {
		LoggerPropertyConfigurator::$singleton = $this;
	}
	
	function configure($configfile) {
		$configinfo = parse_ini_file($configfile);
		
		$types = array();
		$appenders = array();
		
		foreach($configinfo as $k=>$v) {
			if(preg_match("/log4php.rootLogger/i", $k, $m)) {
				$name = 'ROOT';
				list($level, $appender) = explode(',', $v);
				$types[$name]['level'] = $level;
				$types[$name]['appender'] = $appender;
			}
			if(preg_match("/log4php.logger.(.*)/i", $k, $m)) {
				$name = $m[1];
				list($level, $appender) = explode(',', $v);
				$types[$name]['level'] = $level;
				$types[$name]['appender'] = $appender;
			}
			if(preg_match("/log4php.appender.([^.]+).?(.*)/i", $k, $m)) {
				$appenders[$m[1]][$m[2]] = $v;
			}
			
		}
		
		$this->types = $types;
		$this->appenders = $appenders;		
	}

	function getConfigInfo($type) {
		if(isset($this->types[$type])) {
			$typeinfo = $this->types[$type];
			return array (
				'level'   => $typeinfo['level'],
				'appender'=> $this->appenders[$typeinfo['appender']]
		
			);
		}
		return false;
	}
	
	static function getInstance() {
		return self::$singleton;
	}
}
?>
