/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/  

nectarcrm_ExtensionCommon_Js("Google_Index_Js", {}, {

    init : function() {
        this.addComponents();
    },
    
    addComponents : function() {
        this.addComponent('Google_Settings_Js');
    },
    
    registerSyncNowButton : function(container) {
        container.on('click', '.syncNow', function(e) {
            var params = {
                module : 'Google',
                view : 'Sync'
            }
            app.helper.showProgress();
            app.request.post({data: params}).then(function(error, data){
                app.helper.hideProgress();
				
				var hasMorenectarcrmRecords = false;
				var hasMoreGoogleRecords = false;
				
                jQuery.each(data, function(module, syncInfo){
					hasMorenectarcrmRecords = false;
					hasMoreGoogleRecords = false;
					
					if(syncInfo['google'].more === true) {
						hasMoreGoogleRecords = true;
						app.helper.showAlertNotification({message : app.vtranslate('JS_MORE_GOOGLE')});
					}

					if(syncInfo['nectarcrm'].more === true) {
						hasMorenectarcrmRecords = true;
						app.helper.showAlertNotification({message : app.vtranslate('JS_MORE_NECTARCRM')});
					}
					
				});
				
				if(hasMorenectarcrmRecords || hasMoreGoogleRecords) {
					setTimeout(3000);
				}
				
				window.location.reload();
            });
        });
    },
    
    registerSettingsMenuClickEvent : function(container) {
        container.on('click', '.settingsPage', function(e) {
            var element = jQuery(e.currentTarget);
            var url = element.data('url');
            if(!url) {
                return;
            }
            
            var params = {
                url : url
            }
            app.helper.showProgress();
            app.request.pjax(params).then(function(error, data){
                app.helper.hideProgress();
                if(data) {
                    container.html(data);
                    app.event.trigger(Google_Settings_Js.postSettingsLoad, container);
                }
            });
        });
    },
    
    registerEvents : function() {
        this._super();
        var container = this.getListContainer();
        this.registerSyncNowButton(container);
        app.event.trigger(Google_Settings_Js.postSettingsLoad, container);
    }
});
