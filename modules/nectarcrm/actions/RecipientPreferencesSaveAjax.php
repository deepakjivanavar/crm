<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class nectarcrm_RecipientPreferencesSaveAjax_Action extends nectarcrm_SaveAjax_Action {

	public function process(nectarcrm_Request $request) {
		$sourceModule = $request->get('source_module');
		$selecltedFields = $request->get('selectedFields');
		$response = new nectarcrm_Response();
		$prefs = array();
		if ($selecltedFields) {
			foreach ($selecltedFields as $fieldInfoJson) {
				if (is_array($fieldInfoJson)) {
					$fieldInfo = $fieldInfoJson;
				} else {
					$fieldInfo = Zend_Json::decode($fieldInfoJson);
				}
				$prefs[$fieldInfo['module_id']][] = $fieldInfo['field_id'];
			}
		}


		/* For eliminating duplicate field entires. This will happen
		 * when user selects same field for more than 1 record, and enable
		 * Remember my preference option in Select Email Preference pop-up
		 */
		foreach ($prefs as $moduleId => $fieldInfo) {
			if (is_array($fieldInfo)) {
				$prefs[$moduleId] = array_unique($fieldInfo);
			}
		}


		$recipientPrefModel = nectarcrm_RecipientPreference_Model::getInstance($sourceModule);
		if (!$recipientPrefModel) {
			$recipientPrefModel = new nectarcrm_RecipientPreference_Model();
			$recipientPrefModel->setSourceModule($sourceModule);
		}

		if (empty($prefs)) {
			$recipientPrefModel->delete();
			$response->setResult(vtranslate('LBL_PREF_RESET_MESSAGE', $request->getModule()));
		} else {
			$recipientPrefModel->set('prefs', $prefs);
			if ($recipientPrefModel->save()) {
				$response->setResult(vtranslate('LBL_RECIPIENT_SAVE_MESSAGE', $request->getModule()));
			} else {
				$response->setError(vtranslate('LBL_ERROR_SAVING_PREF', $request->getModule()));
			}
		}
		$response->emit();
	}

}
