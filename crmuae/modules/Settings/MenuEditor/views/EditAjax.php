<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Settings_MenuEditor_EditAjax_View extends Settings_nectarcrm_Index_View {

	public function __construct() {
		parent::__construct();
		$this->exposeMethod('showAddModule');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	function showAddModule(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$appName = $request->get('appname');

		$viewer->assign('SELECTED_APP_NAME', $appName);
		$viewer->assign('MODULE', $request->getModule());
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('AddModule.tpl', $qualifiedModuleName);
	}

}
