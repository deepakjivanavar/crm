<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_RemoveWidget_Action extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$linkId = $request->get('linkid');
		$response = new nectarcrm_Response();
		
		if ($request->has('reportid')) {
			$widget = nectarcrm_Widget_Model::getInstanceWithReportId($request->get('reportid'), $currentUser->getId());
		} else if ($request->has('widgetid')) {
			$widget = nectarcrm_Widget_Model::getInstanceWithWidgetId($request->get('widgetid'), $currentUser->getId());
        } else {
			$widget = nectarcrm_Widget_Model::getInstance($linkId, $currentUser->getId());
		}

		if (!$widget->isDefault()) {
			$widget->remove();
			$response->setResult(array('linkid' => $linkId, 'name' => $widget->getName(), 'url' => $widget->getUrl(), 'title' => vtranslate($widget->getTitle(), $request->getModule())));
		} else {
			$response->setError(vtranslate('LBL_CAN_NOT_REMOVE_DEFAULT_WIDGET', $moduleName));
		}
		$response->emit();
	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}
}
