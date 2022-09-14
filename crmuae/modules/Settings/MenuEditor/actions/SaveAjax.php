<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

Class Settings_MenuEditor_SaveAjax_Action extends Settings_nectarcrm_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('removeModule');
		$this->exposeMethod('addModule');
		$this->exposeMethod('saveSequence');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	function removeModule(nectarcrm_Request $request) {
		$sourceModule = $request->get('sourceModule');
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		$db->pquery('UPDATE nectarcrm_app2tab SET visible = ? WHERE tabid = ? AND appname = ?', array(0, getTabid($sourceModule), $appName));

		$response = new nectarcrm_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function addModule(nectarcrm_Request $request) {
		$sourceModules = array($request->get('sourceModule'));
		if ($request->has('sourceModules')) {
			$sourceModules = $request->get('sourceModules');
		}
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		foreach ($sourceModules as $sourceModule) {
			$db->pquery('UPDATE nectarcrm_app2tab SET visible = ? WHERE tabid = ? AND appname = ?', array(1, getTabid($sourceModule), $appName));
		}

		$response = new nectarcrm_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function saveSequence(nectarcrm_Request $request) {
		$moduleSequence = $request->get('sequence');
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		foreach ($moduleSequence as $moduleName => $sequence) {
			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			$db->pquery('UPDATE nectarcrm_app2tab SET sequence = ? WHERE tabid = ? AND appname = ?', array($sequence, $moduleModel->getId(), $appName));
		}

		$response = new nectarcrm_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

}

?>
