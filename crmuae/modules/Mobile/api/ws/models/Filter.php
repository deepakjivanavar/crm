<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/CustomView/CustomView.php';

class Mobile_WS_FilterModel {
	
	var $filterid, $moduleName;
	var $user;
	protected $customView;
	
	function __construct($moduleName) {
		$this->moduleName = $moduleName;
		$this->customView = new CustomView($moduleName);
	}
	
	function setUser($userInstance) {
		$this->user = $userInstance;
	}
	
	function getUser() {
		return $this->user;
	}
	
	function query() {
		$listquery = getListQuery($this->moduleName);
		$query = $this->customView->getModifiedCvListQuery($this->filterid,$listquery,$this->moduleName);
		return $query;
	}
	
	function queryParameters() {
		return false;
	}
	
	static function modelWithId($moduleName, $filterid) {
		$model = new Mobile_WS_FilterModel($moduleName);
		$model->filterid = $filterid;
		return $model;
	}
	
}