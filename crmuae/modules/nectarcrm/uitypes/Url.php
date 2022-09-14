<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Url_UIType extends nectarcrm_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/Url.tpl';
	}

	public function getDisplayValue($value) {
		$matchPattern = "^[\w]+:\/\/^";
		preg_match($matchPattern, $value, $matches);
		if(!empty ($matches[0])) {
			$value = '<a class="urlField cursorPointer" href="'.$value.'" target="_blank">'.textlength_check($value).'</a>';
		} else {
			$value = '<a class="urlField cursorPointer" href="http://'.$value.'" target="_blank">'.textlength_check($value).'</a>';
		}
		return $value;
	}
}