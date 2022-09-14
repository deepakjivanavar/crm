<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

vimport('~~/modules/Calendar/iCal/ical-parser-class.php');

class Import_ICSReader_Reader extends iCal {

	/**
	 * Function to get info about imported file contains header or not
	 * @return <boolean>
	 */
	public function hasHeader() {
		return true;
	}

	/**
	 * Function to get info about imported file contains First Row or not
	 * @param <boolean> $hasHeader
	 * @return <boolean>
	 */
	public function getFirstRowData($hasHeader=true) {
		return true;
	}

}
?>
