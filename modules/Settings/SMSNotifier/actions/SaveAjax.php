<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_SMSNotifier_SaveAjax_Action extends Settings_nectarcrm_Index_Action {

	public function process(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$qualifiedModuleName = $request->getModule(false);

		if ($recordId) {
			$recordModel = Settings_SMSNotifier_Record_Model::getInstanceById($recordId, $qualifiedModuleName);
		} else {
			$recordModel = Settings_SMSNotifier_Record_Model::getCleanInstance($qualifiedModuleName);
		}

		$editableFields = $recordModel->getEditableFields();
		foreach ($editableFields as $fieldName => $fieldModel) {
			$recordModel->set($fieldName, $request->get($fieldName));
		}

		$userName = $request->get('username');
		if(isset($userName)) {
			$recordModel->set('username', $request->get('username'));
		}
		$password = $request->get('username');
		if(isset($password)) {
			$recordModel->set('password', $request->get('password'));
		}
		
        $parameters = ''; 
		$selectedProvider = $request->get('providertype');
		$allProviders = $recordModel->getModule()->getAllProviders();
		foreach ($allProviders as $provider) {
			if ($provider->getName() === $selectedProvider) {
				$fieldsInfo = Settings_SMSNotifier_ProviderField_Model::getInstanceByProvider($provider); 
				foreach ($fieldsInfo as $fieldInfo) { 
 		        	$recordModel->set($fieldInfo['name'], $request->get($fieldInfo['name'])); 
 		            $parameters[$fieldInfo['name']] = $request->get($fieldInfo['name']); 
		        } 
 		        $recordModel->set('parameters', Zend_Json::encode($parameters)); 
 		        break;
			}
		}

		$response = new nectarcrm_Response();
		try {
			$recordModel->save();
			$response->setResult(array(vtranslate('LBL_SAVED_SUCCESSFULLY', $qualifiedModuleName)));
		} catch (Exception $e) {
			$response->setError($e->getMessage());
		}
		$response->emit();
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}