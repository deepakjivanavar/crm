<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Users_Popup_View extends nectarcrm_Popup_View {
    
    function checkPermission(nectarcrm_Request $request) {
        $moduleName = $request->getModule();
        $sourceModuleName = $request->get('src_module');
        $sourceFieldName = $request->get('src_field');
        if( $moduleName == 'Users' && $sourceModuleName == 'Quotes' && $sourceFieldName == 'assigned_user_id1' ) {
            return true;
        }
        return parent::checkPermission($request);
    }
}