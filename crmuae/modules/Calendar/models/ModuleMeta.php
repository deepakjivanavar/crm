<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_ModuleMeta_Model extends nectarcrm_ModuleMeta_Model {

	public function getMergableFields() {
		$mergableFields = array();
		$fieldLabels = array();

		$calendarMergableFields = parent::getMergableFields();
		foreach ($calendarMergableFields as $fieldName => $fieldModel) {
			$fieldLabel = $fieldModel->getFieldLabelKey();
			if (!in_array($fieldLabel, $fieldLabels)) {
				$fieldLabels[] = $fieldLabel;
				$mergableFields[$fieldName] = $fieldModel;
			}
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$eventModuleMeta = nectarcrm_ModuleMeta_Model::getInstance('Events', $currentUserModel);
		$eventMergableFields = $eventModuleMeta->getMergableFields();
		foreach ($eventMergableFields as $fieldName => $fieldModel) {
			$fieldLabel = $fieldModel->getFieldLabelKey();
			if (!in_array($fieldLabel, $fieldLabels)) {
				$fieldLabels[] = $fieldLabel;
				$mergableFields[$fieldName] = $fieldModel;
			}
		}

		$mergableFields = array_merge($eventMergableFields, parent::getMergableFields());
		$skippedFields = array('duration_hours', 'duration_minutes', 'recurringtype', 'reminder_time', 'time_start', 'time_end');
		foreach ($mergableFields as $fieldName => $fieldModel) {
			if (in_array($fieldName, $skippedFields)) {
				unset($mergableFields[$fieldName]);
			}
		}
		return $mergableFields;
	}

	public function getImportableFields() {
		$importableFields = array();
		$fieldLabels = array();

		$calendarImportableFields = parent::getImportableFields();
		foreach ($calendarImportableFields as $fieldName => $fieldModel) {
			$fieldLabel = $fieldModel->getFieldLabelKey();
			if (!in_array($fieldLabel, $fieldLabels)) {
				$fieldLabels[] = $fieldLabel;
				$importableFields[$fieldName] = $fieldModel;
			}
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$eventModuleMeta = nectarcrm_ModuleMeta_Model::getInstance('Events', $currentUserModel);
		$eventImportableFields = $eventModuleMeta->getImportableFields();
		foreach ($eventImportableFields as $fieldName => $fieldModel) {
			$fieldLabel = $fieldModel->getFieldLabelKey();
			if (!in_array($fieldLabel, $fieldLabels)) {
				$fieldLabels[] = $fieldLabel;
				$importableFields[$fieldName] = $fieldModel;
			}
		}

		$importableFields = array_merge($eventImportableFields, parent::getImportableFields());
		$skippedFields = array('duration_hours', 'duration_minutes', 'recurringtype', 'reminder_time');
		foreach ($importableFields as $fieldName => $fieldModel) {
			if (in_array($fieldName, $skippedFields)) {
				unset($importableFields[$fieldName]);
			}
		}

		return $importableFields;
	}

}
