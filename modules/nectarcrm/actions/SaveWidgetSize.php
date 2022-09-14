<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 2.0
 * ("License.txt"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class nectarcrm_SaveWidgetSize_Action extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$id = $request->get('id');
		$tabId = $request->get('tabid');
		$size = Zend_Json::encode($request->get('size'));
		list ($linkid, $widgetid) = explode('-', $id);

		if ($widgetid) {
			nectarcrm_Widget_Model::updateWidgetSize($size, NULL, $widgetid, $currentUser->getId(), $tabId);
		} else {
			nectarcrm_Widget_Model::updateWidgetSize($size, $linkid, NULL, $currentUser->getId(), $tabId);
		}

		$response = new nectarcrm_Response();
		$response->setResult(array('Save' => 'OK'));
		$response->emit();
	}

}
