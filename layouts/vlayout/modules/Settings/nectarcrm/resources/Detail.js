/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Settings_nectarcrm_Detail_Js",{},{
	detailViewForm : false,

	/**
	 * Function which will give the detail view form
	 * @return : jQuery element
	 */
	getForm : function() {
		if(this.detailViewForm == false) {
			this.detailViewForm = jQuery('#detailView');
		}
		return this.detailViewForm;
	},

	/**
	 * Function to register form for validation
	 */
	registerFormForValidation : function(){
		var detailViewForm = this.getForm();
		detailViewForm.validationEngine(app.validationEngineOptions);
	},

	/**
	 * Function which will handle the registrations for the elements
	 */
	registerEvents : function() {
		this.registerFormForValidation();
	}
});