<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Portal_EditAjax_View extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$viewer = $this->getViewer($request);

		if (!empty($recordId)) {
			$data = Portal_Module_Model::getRecord($recordId);

			$viewer->assign('RECORD', $recordId);
			$viewer->assign('BOOKMARK_NAME', $data['bookmarkName']);
			$viewer->assign('BOOKMARK_URL', $data['bookmarkUrl']);
		}
		$viewer->assign('MODULE', $moduleName);
		$viewer->view('EditView.tpl', $moduleName);
	}
}
