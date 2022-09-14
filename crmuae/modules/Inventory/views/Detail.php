<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Inventory_Detail_View extends nectarcrm_Detail_View {
	function preProcess(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	}

	/**
	 * Function returns Inventory details
	 * @param nectarcrm_Request $request
	 */
	function showModuleDetailView(nectarcrm_Request $request) {
		$this->showLineItemDetails($request);
		return parent::showModuleDetailView($request);
	}

	/**
	 * Function returns Inventory details
	 * @param nectarcrm_Request $request
	 * @return type
	 */
	function showDetailViewByMode(nectarcrm_Request $request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		echo parent::showDetailViewByMode($request);
	}

	function showModuleBasicView($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		echo parent::showModuleBasicView($request);
	}

	function showModuleSummaryView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if (!$this->record) {
			$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}

	/**
	 * Function returns Inventory Line Items
	 * @param nectarcrm_Request $request
	 */
	function showLineItemDetails(nectarcrm_Request $request) {
		$record = $request->get('record');
		$moduleName = $request->getModule();

		$recordModel = Inventory_Record_Model::getInstanceById($record);
		$relatedProducts = $recordModel->getProducts();

		//##Final details convertion started
		$finalDetails = $relatedProducts[1]['final_details'];

		//Final tax details convertion started
		$taxtype = $finalDetails['taxtype'];
		if ($taxtype == 'group') {
			$taxDetails = $finalDetails['taxes'];
			$taxCount = count($taxDetails);
			foreach ($taxDetails as $key => $taxInfo) {
				$taxDetails[$key]['amount'] = nectarcrm_Currency_UIType::transformDisplayValue($taxInfo['amount'], null, true);
			}
			$finalDetails['taxes'] = $taxDetails;
		}
		//Final tax details convertion ended

		//Deducted tax details convertion started
		$deductTaxes = $finalDetails['deductTaxes'];
		foreach ($deductTaxes as $taxId => $taxInfo) {
			$deductTaxes[$taxId]['taxAmount'] = nectarcrm_Currency_UIType::transformDisplayValue($deductTaxes[$taxId]['taxAmount'], null, true);
		}
		$finalDetails['deductTaxes'] = $deductTaxes;
		//Deducted tax details convertion ended

		$currencyFieldsList = array('adjustment', 'grandTotal', 'hdnSubTotal', 'preTaxTotal', 'tax_totalamount',
									'shtax_totalamount', 'discountTotal_final', 'discount_amount_final', 'shipping_handling_charge', 'totalAfterDiscount', 'deductTaxesTotalAmount');
		foreach ($currencyFieldsList as $fieldName) {
			$finalDetails[$fieldName] = nectarcrm_Currency_UIType::transformDisplayValue($finalDetails[$fieldName], null, true);
		}

		$relatedProducts[1]['final_details'] = $finalDetails;
		//##Final details convertion ended

		//##Product details convertion started
		$productsCount = count($relatedProducts);
		for ($i=1; $i<=$productsCount; $i++) {
			$product = $relatedProducts[$i];

			//Product tax details convertion started
			if ($taxtype == 'individual') {
				$taxDetails = $product['taxes'];
				$taxCount = count($taxDetails);
				for($j=0; $j<$taxCount; $j++) {
					$taxDetails[$j]['amount'] = nectarcrm_Currency_UIType::transformDisplayValue($taxDetails[$j]['amount'], null, true);
				}
				$product['taxes'] = $taxDetails;
			}
			//Product tax details convertion ended

			$currencyFieldsList = array('taxTotal', 'netPrice', 'listPrice', 'unitPrice', 'productTotal','purchaseCost','margin',
										'discountTotal', 'discount_amount', 'totalAfterDiscount');
			foreach ($currencyFieldsList as $fieldName) {
				$product[$fieldName.$i] = nectarcrm_Currency_UIType::transformDisplayValue($product[$fieldName.$i], null, true);
			}

			$relatedProducts[$i] = $product;
		}
		//##Product details convertion ended

		$selectedChargesAndItsTaxes = $relatedProducts[1]['final_details']['chargesAndItsTaxes'];
		if (!$selectedChargesAndItsTaxes) {
			$selectedChargesAndItsTaxes = array();
		}

		$shippingTaxes = array();
		$allShippingTaxes = getAllTaxes('all', 'sh');
		foreach ($allShippingTaxes as $shTaxInfo) {
			$shippingTaxes[$shTaxInfo['taxid']] = $shTaxInfo;
		}

		$selectedTaxesList = array();
		foreach ($selectedChargesAndItsTaxes as $chargeId => $chargeInfo) {
			if ($chargeInfo['taxes']) {
				foreach ($chargeInfo['taxes'] as $taxId => $taxPercent) {
					$taxInfo = array();
					$amount = $calculatedOn = $chargeInfo['value'];

					if ($shippingTaxes[$taxId]['method'] === 'Compound') {
						$compoundTaxes = Zend_Json::decode(html_entity_decode($shippingTaxes[$taxId]['compoundon']));
						if (is_array($compoundTaxes)) {
							foreach ($compoundTaxes as $comTaxId) {
								$calculatedOn += ((float)$amount * (float)$chargeInfo['taxes'][$comTaxId]) / 100;
							}
							$taxInfo['method']		= 'Compound';
							$taxInfo['compoundon']	= $compoundTaxes;
						}
					}
					$calculatedAmount = ((float)$calculatedOn * (float)$taxPercent) / 100;

					$taxInfo['name']	= $shippingTaxes[$taxId]['taxlabel'];
					$taxInfo['percent']	= $taxPercent;
					$taxInfo['amount']	= nectarcrm_Currency_UIType::transformDisplayValue($calculatedAmount, null, true);

					$selectedTaxesList[$chargeId][$taxId] = $taxInfo;
				}
			}
		}

		$selectedChargesList = Inventory_Charges_Model::getChargeModelsList(array_keys($selectedChargesAndItsTaxes));
		foreach ($selectedChargesList as $chargeId => $chargeModel) {
			$chargeInfo['name']		= $chargeModel->getName();
			$chargeInfo['amount']	= nectarcrm_Currency_UIType::transformDisplayValue($selectedChargesAndItsTaxes[$chargeId]['value'], null, true);
			$chargeInfo['percent']	= $selectedChargesAndItsTaxes[$chargeId]['percent'];
			$chargeInfo['taxes']	= $selectedTaxesList[$chargeId];
			$chargeInfo['deleted']	= $chargeModel->get('deleted');

			$selectedChargesList[$chargeId] = $chargeInfo;
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('RELATED_PRODUCTS', $relatedProducts);
		$viewer->assign('SELECTED_CHARGES_AND_ITS_TAXES', $selectedChargesList);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_NAME',$moduleName);

//		$viewer->view('LineItemsDetail.tpl', 'Inventory');
	}

	public function getActivities(nectarcrm_Request $request) {
		$moduleName = 'Calendar';
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if ($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			$moduleName = $request->getModule();
			$recordId = $request->get('record');

			$pageNumber = $request->get('page');
			if (empty($pageNumber)) {
				$pageNumber = 1;
			}
			$pagingModel = new nectarcrm_Paging_Model();
			$pagingModel->set('page', $pageNumber);
			$pagingModel->set('limit', 10);

			if (!$this->record) {
				$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
			}
			$recordModel = $this->record->getRecord();
			$moduleModel = $recordModel->getModule();

			$relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			$viewer->assign('PAGE_NUMBER', $pageNumber);
			$viewer->assign('ACTIVITIES', $relatedActivities);

			return $viewer->view('RelatedActivities.tpl', $moduleName, true);
		}
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getOverlayHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getOverlayHeaderScripts($request);
		$moduleName = $request->getModule();
		$moduleDetailFile = 'modules.' . $moduleName . '.resources.Detail';
		unset($headerScriptInstances[$moduleDetailFile]);
		$jsFileNames = array(
			'modules.Inventory.resources.Detail',
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$moduleName = $request->getModule();

		//Added to remove the module specific js, as they depend on inventory files
		$modulePopUpFile = 'modules.'.$moduleName.'.resources.Popup';
		$moduleEditFile = 'modules.'.$moduleName.'.resources.Edit';
		$moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
		unset($headerScriptInstances[$modulePopUpFile]);
		unset($headerScriptInstances[$moduleEditFile]);
		unset($headerScriptInstances[$moduleDetailFile]);

		$jsFileNames = array(
			'modules.Inventory.resources.Popup',
			'modules.Inventory.resources.Detail',
			'modules.Inventory.resources.Edit',
			"modules.$moduleName.resources.Detail",
		);
		$jsFileNames[] = $moduleEditFile;
		$jsFileNames[] = $modulePopUpFile;
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}
