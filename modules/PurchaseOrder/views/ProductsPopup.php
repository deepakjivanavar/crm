<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class PurchaseOrder_ProductsPopup_View extends Inventory_ProductsPopup_View {

	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		parent::initializeListViewContents($request, $viewer);
		$viewer->assign('GETURL', 'getPurchaseOrderTaxesURL');
	}
}