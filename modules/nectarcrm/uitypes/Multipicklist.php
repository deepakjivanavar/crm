<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Multipicklist_UIType extends nectarcrm_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/MultiPicklist.tpl';
	}

	/**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param <Object> $value
	 * @return <Object>
	 */
	public function getDisplayValue($value) {

		$moduleName = $this->get('field')->getModuleName();
		$value = explode(' |##| ', $value);
		foreach ($value as $key => $val) {
			$value[$key] = vtranslate($val, $moduleName);
		}

		if(is_array($value)){
            $value = implode(' |##| ', $value);
        }
		return str_ireplace(' |##| ', ', ', $value);
	}
    
    public function getDBInsertValue($value) {
		if(is_array($value)){
            $value = implode(' |##| ', $value);
        }
        return $value;
	}
    
    
    public function getListSearchTemplateName() {
        return 'uitypes/MultiSelectFieldSearchView.tpl';
    }
}