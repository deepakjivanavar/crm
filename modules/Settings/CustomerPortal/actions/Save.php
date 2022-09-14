<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_CustomerPortal_Save_Action extends Settings_nectarcrm_Index_Action {

	public function process(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$enableModules = $request->get('enableModules');
		$defaultAssignee = $request->get('defaultAssignee');
		$response = new nectarcrm_Response();
		if ($defaultAssignee && $enableModules) {
			$moduleModel = Settings_CustomerPortal_Module_Model::getInstance($qualifiedModuleName);
			$moduleModel->set('enableModules', $enableModules);
			$moduleModel->set('defaultAssignee', $defaultAssignee);
			$moduleModel->set('moduleSequence', $request->get('portalModulesInfo'));
			$moduleModel->set('support_notification', $request->get('renewalPeriod'));
			$moduleModel->set('announcement', $request->get('announcement'));
			$moduleModel->set('shortcuts', $request->get('defaultShortcuts'));
			$moduleModel->set('moduleFieldsInfo', $request->get('moduleFieldsInfo'));
			$moduleModel->set('relatedModuleList', $request->get('relatedModuleList'));
			$moduleModel->set('widgets', $request->get('activeWidgets'));
			$moduleModel->set('recordsVisible', $request->get('recordsVisible'));
			$moduleModel->set('recordPermissions', $request->get('recordPermissions'));
			$moduleModel->save();
			$response->setResult(array('success' => true));
		} else {
			$response->setResult(array('success' => false));
		}
		$response->emit();
	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}
}
