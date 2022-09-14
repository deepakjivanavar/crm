<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Potentials_MappingSave_Action extends Settings_nectarcrm_Index_Action {

	public function process(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$mapping = $request->get('mapping');
         //removing csrf token from mapping array because it'll cause query failure
        $csrfKey = '__vtrftk';
        if (array_key_exists($csrfKey, $mapping)) {
            unset($mapping[$csrfKey]);
        }
		$mappingModel = Settings_Potentials_Mapping_Model::getCleanInstance();

		$response = new nectarcrm_Response();
		if ($mapping) {
			$mappingModel->save($mapping);
			$response->setResult(array(vtranslate('LBL_SAVED_SUCCESSFULLY', $qualifiedModuleName)));
		} else {
			$response->setError(vtranslate('LBL_INVALID_MAPPING', $qualifiedModuleName));
		}
		$response->emit();
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}