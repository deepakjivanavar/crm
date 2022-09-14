/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

nectarcrm_List_Js("Inventory_List_Js", {

},
        {

            showQuickPreviewForId: function(recordId, appName, templateId) {
                var self = this;
                var nectarcrmInstance = nectarcrm_Index_Js.getInstance();
                nectarcrmInstance.showQuickPreviewForId(recordId, self.getModuleName(), app.getAppName(), templateId);
            },
            
            registerEvents: function() {
                this._super();
            }

        });
