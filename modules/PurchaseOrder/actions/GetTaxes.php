<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PurchaseOrder_GetTaxes_Action extends Inventory_GetTaxes_Action {

	function checkPermission(nectarcrm_Request $request) {
		$record = $request->get('record');

		$moduleName = getSalesEntityType($record);
		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $record);

		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	function process(nectarcrm_Request $request) {
		$decimalPlace = getCurrencyDecimalPlaces();
		$currencyId = $request->get('currency_id');
		$currencies = Inventory_Module_Model::getAllCurrencies();
		$conversionRate = $conversionRateForPurchaseCost = 1;

		$idList = $request->get('idlist');
		if (!$idList) {
			$recordId = $request->get('record');
			$idList = array($recordId);
		}

		$response = new nectarcrm_Response();
		$namesList = $purchaseCostsList = $taxesList = $listPricesList = $listPriceValuesList = array();
		$descriptionsList = $quantitiesList = $imageSourcesList = $productIdsList = $baseCurrencyIdsList = array();

		foreach($idList as $id) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($id);
			$taxes = $recordModel->getTaxes();
			foreach ($taxes as $key => $taxInfo) {
				$taxInfo['compoundOn'] = json_encode($taxInfo['compoundOn']);
				$taxes[$key] = $taxInfo;
			}

			$taxesList[$id]				= $taxes;
			$namesList[$id]				= decode_html($recordModel->getName());
			$quantitiesList[$id]		= $recordModel->get('qtyinstock');
			$descriptionsList[$id]		= decode_html($recordModel->get('description'));

			$priceDetails = $recordModel->getPriceDetails();
			foreach ($priceDetails as $currencyDetails) {
				if ($currencyId == $currencyDetails['curid']) {
					$conversionRate = $currencyDetails['conversionrate'];
				}
			}
			$listPricesList[$id] = (float)$recordModel->get('unit_price') * (float)$conversionRate;
			$purchaseCostsList[$id] = round((float)$recordModel->get('purchase_cost') * (float)$conversionRate, $decimalPlace);
			$baseCurrencyIdsList[$id] = getProductBaseCurrency($id, $recordModel->getModuleName());

			foreach($currencies as $currency) {
				$listPriceValuesList[$id][$currency['currency_id']] = $currency['conversionrate'] * (float)$recordModel->get('purchase_cost');
			}

			if ($recordModel->getModuleName() == 'Products') {
				$productIdsList[] = $id;
			}

		}

		if ($productIdsList) {
			$imageDetailsList = Products_Record_Model::getProductsImageDetails($productIdsList);

			foreach ($imageDetailsList as $productId => $imageDetails) {
				$imageSourcesList[$productId] = $imageDetails[0]['path'].'_'.$imageDetails[0]['orgname'];
			}
		}

		foreach($idList as $id) {
			$resultData = array(
								'id'					=> $id,
								'name'					=> $namesList[$id],
								'taxes'					=> $taxesList[$id],
								'listprice'				=> $listPricesList[$id],
								'listpricevalues'		=> $listPriceValuesList[$id],
								'purchaseCost'			=> $purchaseCostsList[$id],
								'description'			=> $descriptionsList[$id],
								'baseCurrencyId'		=> $baseCurrencyIdsList[$id],
								'quantityInStock'		=> $quantitiesList[$id],
								'imageSource'			=> $imageSourcesList[$id]
					);

			$info[] = array($id => $resultData);
		}
		$response->setResult($info);
		$response->emit();
	}
}
