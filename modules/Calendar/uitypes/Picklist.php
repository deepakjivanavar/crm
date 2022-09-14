<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Calendar_Picklist_UIType extends nectarcrm_Picklist_UIType {
    
    
    public function getListSearchTemplateName() {
        
        $fieldName = $this->get('field')->get('name');
        
        if($fieldName == 'taskstatus') {
            return 'uitypes/StatusPickListFieldSearchView.tpl';
        }
        else if ($fieldName == 'activitytype') {
            return 'uitypes/ActivityPicklistFieldSearchView.tpl';
        }
            return parent::getListSearchTemplateName();
    }
}