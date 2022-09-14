<?php
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
*
 ********************************************************************************/

/*
 * File containing methods to proceed with the ui validation for all the forms
 *
 */
/**
 * Get field validation information
 */
function getDBValidationData($tablearray, $tabid='') {
	return nectarcrm_Deprecated::getModuleFieldTypeOfDataInfos($tablearray, $tabid);
  }

?>
