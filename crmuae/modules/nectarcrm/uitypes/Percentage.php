<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Percentage_UIType extends nectarcrm_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/Percentage.tpl';
	}

	public function getDisplayValue($value, $record = false, $recordInstance = false) {
		$fldvalue = str_replace(",", ".", $value);
		$value = (is_numeric($fldvalue)) ? $fldvalue : null;
		return CurrencyField::convertToUserFormat($value, null, true);
	}

	public function getEditViewDisplayValue($value) {
		return $this->getDisplayValue($value);
	}
}
