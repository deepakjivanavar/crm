<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Webforms_ListView_Model extends Settings_nectarcrm_ListView_Model {
    
     /**
	 * Function which returns Basic List Query for webform. 
	 */
    public function getBasicListQuery() {
        $module = $this->getModule();
        $listFields = $module->listFields;
        
		$listQuery = "SELECT ";
		foreach ($listFields as $fieldName => $fieldLabel) {
			$listQuery .= $module->baseTable.".$fieldName, ";
		}
        $listQuery.= $module->baseTable.'.'.$module->baseIndex .' FROM '. $module->baseTable.
                     ' INNER JOIN nectarcrm_tab ON nectarcrm_tab.name='. $module->baseTable.'.targetmodule WHERE nectarcrm_tab.presence IN (0,2)';    
        return $listQuery;
    }
}