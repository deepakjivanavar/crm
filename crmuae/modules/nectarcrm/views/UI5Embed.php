<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class nectarcrm_UI5Embed_View extends nectarcrm_Index_View {
	
	protected function preProcessDisplay(nectarcrm_Request $request) {}
	
	protected function getUI5EmbedURL(nectarcrm_Request $request) {
		return '../index.php?action=index&module=' . $request->getModule();
	}
	
	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('UI5_URL', $this->getUI5EmbedURL($request));
		$viewer->view('UI5EmbedView.tpl');
	}
	
}