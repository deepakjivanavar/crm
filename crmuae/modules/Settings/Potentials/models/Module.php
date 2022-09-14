<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Potentials_Module_Model extends Settings_Leads_Module_Model {
    
    /**
     * Function to get the Restricted ui Types
     * @return <array> $restrictedUitypes
     */
    public function getRestrictedUitypes() {
        $restrictedUitypes = parent::getRestrictedUitypes();
        $pos = array_search(10, $restrictedUitypes);
        unset($restrictedUitypes[$pos]);
        
        return $restrictedUitypes;
    }
    
    /**
	 * Function to get instance of module
	 * @param <String> $moduleName
	 * @return <Settings_Potentials_Module_Model>
	 */
	public static function getInstance($moduleName) {
		$moduleModel = parent::getInstance($moduleName);
		$objectProperties = get_object_vars($moduleModel);

		$moduleModel = new self();
		foreach	($objectProperties as $properName => $propertyValue) {
                $moduleModel->$properName = $propertyValue;
		}
        
		return $moduleModel;
	}
}
