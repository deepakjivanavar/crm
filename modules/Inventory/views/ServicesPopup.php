<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Inventory_ServicesPopup_View extends Inventory_ProductsPopup_View {

	/**
	 * Function returns module name for which Popup will be initialized
	 * @param type $request
	 */
	function getModule($request) {
		return 'Services';
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		//src_module value is added just to stop showing inactive services
		$request->set('src_module', $request->getModule());

		parent::initializeListViewContents($request, $viewer);
        $moduleModel = nectarcrm_Module_Model::getInstance('Services');
        
        if (!$moduleModel->isActive()) {
			$viewer->assign('LISTVIEW_ENTRIES_COUNT', 0);
            $viewer->assign('LISTVIEW_ENTRIES', array());
            $viewer->assign('IS_MODULE_DISABLED', true);
        }

		$viewer->assign('GETURL', 'getTaxesURL');
		$viewer->assign('VIEW', 'ServicesPopup');
	}

}