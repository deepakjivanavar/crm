/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

nectarcrm.Class('Migration_Index_Js', {

	startMigrationEvent: function () {
		var migrateUrl = 'index.php?module=Migration&view=Index&mode=applyDBChanges';
		app.request.post({url:migrateUrl}).then(function (err, data) {
			jQuery('#running').addClass('hide').removeClass('show');
			jQuery('#success').addClass('show').removeClass('hide');
			jQuery('#nextButton').addClass('show').removeClass('hide');
			jQuery('#showDetails').addClass('show').removeClass('hide').html(data);
		});
	},

	registerEvents: function () {
		this.startMigrationEvent();
	}

});
