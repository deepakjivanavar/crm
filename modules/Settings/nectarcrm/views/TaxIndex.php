<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_TaxIndex_View extends Settings_nectarcrm_Index_View {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('showChargesAndItsTaxes');
		$this->exposeMethod('showTaxRegions');
	}

    public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

		$taxRecordModel = new Inventory_TaxRecord_Model();
		$productAndServicesTaxList = Inventory_TaxRecord_Model::getProductTaxes();
		$qualifiedModuleName = $request->getModule(false);

        $viewer = $this->getViewer($request);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('TAX_RECORD_MODEL', $taxRecordModel);
		$viewer->assign('PRODUCT_AND_SERVICES_TAXES', $productAndServicesTaxList);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('TaxIndex.tpl', $qualifiedModuleName);
    }

	public function showChargesAndItsTaxes(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$taxRecordModel = new Inventory_TaxRecord_Model();
		$charges = Inventory_Charges_Model::getInventoryCharges();
		$chargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();

		$viewer = $this->getViewer($request);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('TAX_RECORD_MODEL', $taxRecordModel);
		$viewer->assign('CHARGE_MODELS_LIST', $charges);
		$viewer->assign('CHARGE_TAXES', $chargeTaxes);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('ChargesAndItsTaxes.tpl', $qualifiedModuleName);
	}

	public function showTaxRegions(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$taxRegions = Inventory_TaxRegion_Model::getAllTaxRegions();

		$viewer = $this->getViewer($request);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('TAX_REGIONS', $taxRegions);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('TaxRegions.tpl', $qualifiedModuleName);
	}

	function getPageTitle(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		return vtranslate('LBL_TAX_CALCULATIONS',$qualifiedModuleName);
	}
	
	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	public function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.Settings.$moduleName.resources.Tax"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}