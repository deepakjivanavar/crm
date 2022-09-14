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
 * Assets Field Model Class
 */
class Assets_Field_Model extends nectarcrm_Field_Model {

	/**
	 * Function returns special validator for fields
	 * @return <Array>
	 */
	function getValidator() {
		$validator = array();
		$fieldName = $this->getName();

		switch($fieldName) {
            case 'datesold' : $funcName = array('name'=>'lessThanOrEqualToToday'); 
                              array_push($validator, $funcName); 
                              break; 
			default : $validator = parent::getValidator();
						break;
		}
		return $validator;
	}
}
