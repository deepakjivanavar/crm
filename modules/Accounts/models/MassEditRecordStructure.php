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
 * Mass Edit Record Structure Model
 */
class Accounts_MassEditRecordStructure_Model extends nectarcrm_MassEditRecordStructure_Model {
	
	/*
	 * Function that return Field Restricted are not
	 *	@params Field Model
	 *  @returns boolean true or false
	 */
	public function isFieldRestricted($fieldModel){
		$restricted = parent::isFieldRestricted($fieldModel);
		if($restricted && $fieldModel->getName() == 'accountname'){
			return false;
		} else {
			return $restricted;
		}
	}
}
?>
