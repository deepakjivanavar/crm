/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Migration_Index_Js",{
	
	startMigrationEvent : function(){
		
		var migrateUrl = 'index.php?module=Migration&view=Index&mode=applyDBChanges';
			AppConnector.request(migrateUrl).then(
			function(data) {
				jQuery("#running").hide();
				jQuery("#success").show();
				jQuery("#nextButton").show();
				jQuery("#showDetails").show().html(data);
			})
	},
	
	registerEvents : function(){
		this.startMigrationEvent();
	}
	
});
