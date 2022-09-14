<?php
/* ***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class Project_SaveAjax_Action extends nectarcrm_SaveAjax_Action {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('saveColor');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		} else {
			parent::process($request);
		}
	}

	function saveColor(nectarcrm_Request $request) {
		$db = PearDatabase::getInstance();
		$color = $request->get('color');
		$status = $request->get('status');

		$db->pquery('INSERT INTO nectarcrm_projecttask_status_color(status,color) VALUES(?,?) ON DUPLICATE KEY UPDATE color = ?', array($status, $color, $color));
		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->setResult(true);
		$response->emit();
	}

}
