<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Settings_Tags_EditAjax_View extends Settings_nectarcrm_IndexAjax_View {

	public function checkPermission(nectarcrm_Request $request) {
		return true;
	}

	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$qualifiedName = $request->getModule(false);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedName);
		$viewer->view('EditAjax.tpl', $qualifiedName);
	}
}
