<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

include_once dirname(__FILE__) . '/Connectors.php';

class nectarcrm_Cache_Connector {
	protected $connection;

	protected function __construct() {
		if (!$this->connection) {
			$this->connection = nectarcrm_Cache_Connector_Memory::getInstance();
		}
	}

	protected function cacheKey($ns, $key) {
		if(is_array($key)) $key = implode('-', $key);
		return $ns . '-' . $key;
	}

	public function set($namespace, $key, $value) {
		$this->connection->set($this->cacheKey($namespace, $key), $value);
	}

	public function get($namespace, $key) {
		return $this->connection->get($this->cacheKey($namespace, $key));
	}

	public function delete($namespace, $key) {
		$this->connection->delete($this->cacheKey($namespace, $key));
	}

	public function has($namespace, $key) {
		return $this->get($namespace, $key) !== false;
	}

	public function flush(){
		$this->connection->flush(); 

		$time = time()+1; //one second future 
		while(time() < $time) { 
			//sleep 
		} 
	}

	public static function getInstance() {
		static $singleton = NULL;
		if ($singleton === NULL) {
			$singleton = new self();
		}
		return $singleton;
	}
}