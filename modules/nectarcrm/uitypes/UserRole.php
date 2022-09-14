<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_UserRole_UIType extends nectarcrm_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/UserRole.tpl';
	}

	/**
	 * Function to get the display value in detail view
	 * @param <Integer> crmid of record
	 * @return <String>
	 */
	public function getEditViewDisplayValue($value) {
		if($value) {
			$userName = getRoleName($value);
			return $userName;
		}
	}

	/**
	 * Function to get display value
	 * @param <String> $value
	 * @param <Number> $recordId
	 * @return <String> display value
	 */
	public function getDisplayValue($value, $recordId) {
		$displayValue = $this->getEditViewDisplayValue($value);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel->isAdminUser()) {
			$roleRecordModel = new Settings_Roles_Record_Model();
			$roleRecordModel->set('roleid', $value);
			return '<a href="'. $roleRecordModel->getEditViewUrl() .'">'. textlength_check($displayValue) .'</a>';
		}
		return $displayValue;
	}

	public function getListSearchTemplateName() {
		return 'uitypes/UserRoleFieldSearchView.tpl';
	}
}