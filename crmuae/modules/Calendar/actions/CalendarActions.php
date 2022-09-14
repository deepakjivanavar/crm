<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_CalendarActions_Action extends nectarcrm_BasicAjax_Action {

	function __construct() {
		$this->exposeMethod('fetchAgendaViewEventDetails');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function fetchAgendaViewEventDetails(nectarcrm_Request $request) {
		$result = array();
		$eventId = $request->get('id');
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance('Events');
		$recordModel = Events_Record_Model::getInstanceById($eventId);

		$result[vtranslate('Assigned To')] = getUserFullName($recordModel->get('assigned_user_id'));
		if ($recordModel->get('priority')) {
			$result[vtranslate('Priority', $moduleName)] = $recordModel->get('priority');
		}
		if ($recordModel->get('location')) {
			$result[vtranslate('Location', $moduleName)] = $recordModel->get('location');
		}
		if ($recordModel->get('contact_id')) {
			$contact_id = nectarcrm_Field_Model::getInstance('contact_id', $moduleModel);
			$result[vtranslate($contact_id->get('label'), $moduleName)] = $contact_id->getDisplayValue($recordModel->get('contact_id'));
		}
		if ($recordModel->get('parent_id')) {
			$parent_id = nectarcrm_Field_Model::getInstance('parent_id', $moduleModel);
			$result[vtranslate($parent_id->get('label'), $moduleName)] = $parent_id->getDisplayValue($recordModel->get('parent_id'));
		}
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

}
