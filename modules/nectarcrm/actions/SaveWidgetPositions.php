<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_SaveWidgetPositions_Action extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		$positionsMap = $request->get('positionsmap');
		
		if ($positionsMap) {
			foreach ($positionsMap as $id => $position) {
				list ($linkid, $widgetid) = explode('-', $id);
				if ($widgetid) {
					nectarcrm_Widget_Model::updateWidgetPosition($position, NULL, $widgetid, $currentUser->getId());
				} else {
					nectarcrm_Widget_Model::updateWidgetPosition($position, $linkid, NULL, $currentUser->getId());
				}
			}
		}
		
		$response = new nectarcrm_Response();
		$response->setResult(array('Save' => 'OK'));
		$response->emit();
	}
}
