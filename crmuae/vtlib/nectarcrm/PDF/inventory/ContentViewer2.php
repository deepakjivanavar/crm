<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/ContentViewer.php';

class nectarcrm_PDF_InventoryTaxGroupContentViewer extends nectarcrm_PDF_InventoryContentViewer {

	function __construct() {
		// NOTE: General A4 PDF width ~ 189 (excluding margins on either side)
			
		$this->cells = array( // Name => Width
			'Code'		=> 30,
			'Name'		=> 65,
			'Quantity'	=> 20,
			'Price'		=> 25,
			'Discount'	=> 20,
			'Total'		=> 30
		);
	}
	
}
