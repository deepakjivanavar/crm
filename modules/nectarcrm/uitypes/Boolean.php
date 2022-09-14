<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Boolean_UIType extends nectarcrm_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/Boolean.tpl';
	}

	/**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param <Object> $value
	 * @return <Object>
	 */
	public function getDisplayValue($value) {
		if($value == 1 || $value == '1' || strtolower($value) == 'on') {
			return nectarcrm_Language_Handler::getTranslatedString('LBL_YES', $this->get('field')->getModuleName());
		}
		return nectarcrm_Language_Handler::getTranslatedString('LBL_NO', $this->get('field')->getModuleName());
	}
    
     public function getListSearchTemplateName() {
        return 'uitypes/BooleanFieldSearchView.tpl';
    }

}