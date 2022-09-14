/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

nectarcrm_Edit_Js("Settings_nectarcrm_Edit_Js",{},{
    
    registerEvents : function() {
        this._super();
        //Register events for settings side menu (Search and collapse open icon )
        var instance = new Settings_nectarcrm_Index_Js(); 
        instance.registerBasicSettingsEvents();
    }
})