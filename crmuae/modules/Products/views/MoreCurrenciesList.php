<?php

/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ********************************************************************************** */

class Products_MoreCurrenciesList_View extends nectarcrm_IndexAjax_View {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$actionName = ($record) ? 'EditView' : 'CreateView';
		if(!Users_Privileges_Model::isPermitted($moduleName, $actionName, $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$currencyName = $request->get('currency');

		if (!empty($recordId)) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);
			$priceDetails = $recordModel->getPriceDetails();
		} else {
			$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
			$priceDetails = $recordModel->getPriceDetails();

			foreach ($priceDetails as $key => $currencyDetails) {
				if ($currencyDetails['curname'] === $currencyName) {
					$baseCurrencyConversionRate = $currencyDetails['conversionrate'];
					break;
				}
			}

			foreach ($priceDetails as $key => $currencyDetails) {
				if ($currencyDetails['curname'] === $currencyName) {
					$currencyDetails['conversionrate'] = 1;
					$currencyDetails['is_basecurrency'] = 1;
				} else {
					$currencyDetails['conversionrate'] = $currencyDetails['conversionrate'] / $baseCurrencyConversionRate;
					$currencyDetails['is_basecurrency'] = 0;
				}
				$priceDetails[$key] = $currencyDetails;
			}
		}

		$viewer = $this->getViewer($request);

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('PRICE_DETAILS', $priceDetails);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('MoreCurrenciesList.tpl', 'Products');
	}

}