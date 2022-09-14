<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class nectarcrm_SaveStar_Action extends nectarcrm_Mass_Action {

	function checkPermission(nectarcrm_Request $request) {
		//Return true as WebUI.php is already checking for module permission
		return true;
	}

	public function process(nectarcrm_Request $request) {
		$module = $request->get('module');
		if ($request->has('selected_ids')) {
			$recordIds = $this->getRecordsListFromRequest($request);
		} else {
			$recordIds = array($request->get('record'));
		}

		$moduleUserSpecificTableName = nectarcrm_Functions::getUserSpecificTableName($module);
		//TODO : Currently we are not doing retrieve_entity_info before doing save since we have only one user specific field(starred)
		// if we add more user specific field then we need to peform retrieve_entity_info 
		foreach ($recordIds as $recordId) {
			$focus = CRMEntity::getInstance($module);
			$focus->mode = "edit";
			$focus->id = $recordId;
			$focus->column_fields->startTracking();
			$focus->column_fields['starred'] = $request->get('value');
			$focus->insertIntoEntityTable($moduleUserSpecificTableName, $module);
		}

		$response = new nectarcrm_Response();
		$response->setResult(true);
		$response->emit();
	}

}
