<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Potentials_SaveAjax_Action extends nectarcrm_SaveAjax_Action {

	public function process(nectarcrm_Request $request) {
		//Restrict to store indirect relationship from Potentials to Contacts
		$sourceModule = $request->get('sourceModule');
		$relationOperation = $request->get('relationOperation');
		$skip = true;

		if ($relationOperation && $sourceModule === 'Contacts') {
			$request->set('relationOperation', false);
			$skip = false;
		}

		parent::process($request);

		// to link the relation in updates
		if (!$skip) {
			$sourceRecordId = $request->get('sourceRecord');
			$focus = CRMEntity::getInstance($sourceModule);
			$destinationModule = $request->get('module');
			$destinationRecordId = $this->savedRecordId;
			$focus->trackLinkedInfo($sourceModule, $sourceRecordId, $destinationModule, $destinationRecordId);
		}
	}
}