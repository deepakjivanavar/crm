<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Roles Record Model Class
 */
abstract class Settings_nectarcrm_Record_Model extends nectarcrm_Base_Model {

	abstract function getId();
	abstract function getName();

    /**
	 * Function to get the instance of Settings module model
	 * @return Settings_nectarcrm_Module_Model instance
	 */
	 public static function getInstance($name='Settings:nectarcrm') {
		$modelClassName  = nectarcrm_Loader::getComponentClassName('Model', 'Record', $name);
		 return new $modelClassName();
	 }
    
    
	public function getRecordLinks() {

		$links = array();
		$recordLinks = array();
		foreach ($recordLinks as $recordLink) {
			$links[] = nectarcrm_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}
	
	public function getDisplayValue($key) {
		return $this->get($key);
	}
}
