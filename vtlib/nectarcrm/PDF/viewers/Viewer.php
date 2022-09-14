<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
abstract class nectarcrm_PDF_Viewer {
	
	protected $labelModel;
	
	function setLabelModel($m) {
		$this->labelModel = $m;
	}
	
	abstract function totalHeight($parent);
	abstract function initDisplay($parent);
	abstract function display($parent);
}
