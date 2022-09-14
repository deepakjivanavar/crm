<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~modules/PickList/DependentPickListUtils.php');

class Settings_PickListDependency_Module_Model extends Settings_nectarcrm_Module_Model {

	var $baseTable = 'nectarcrm_picklist_dependency';
	var $baseIndex = 'id';
	var $name = 'PickListDependency';

	/**
	 * Function to get the url for default view of the module
	 * @return <string> - url
	 */
	public function getDefaultUrl() {
		return 'index.php?module=PickListDependency&parent=Settings&view=List';
	}

	/**
	 * Function to get the url for Adding Dependency
	 * @return <string> - url
	 */
	public function getCreateRecordUrl() {
		return "javascript:Settings_PickListDependency_Js.triggerAdd(event)";
	}

	public function isPagingSupported() {
		return false;
	}

	public static function getAvailablePicklists($module) {
		return nectarcrm_DependencyPicklist::getAvailablePicklists($module);
	}

	public static function getPicklistSupportedModules() {
		$adb = PearDatabase::getInstance();

		$query = "SELECT distinct nectarcrm_field.tabid, nectarcrm_tab.tablabel, nectarcrm_tab.name as tabname FROM nectarcrm_field
						INNER JOIN nectarcrm_tab ON nectarcrm_tab.tabid = nectarcrm_field.tabid
						WHERE uitype IN ('15','16')
						AND nectarcrm_field.tabid != 29
						AND nectarcrm_field.displaytype = 1
						AND nectarcrm_field.presence in ('0','2')
						AND nectarcrm_field.block != 'NULL'
						AND nectarcrm_tab.presence != 1
					GROUP BY nectarcrm_field.tabid HAVING count(*) > 1";
		// END
		$result = $adb->pquery($query, array());
		while($row = $adb->fetch_array($result)) {
			$modules[$row['tablabel']] = $row['tabname'];
		}
		ksort($modules);

		$modulesModelsList = array();
		foreach($modules as $moduleLabel => $moduleName) {
			$instance = new nectarcrm_Module_Model();
			$instance->name = $moduleName;
			$instance->label = $moduleLabel;
			$modulesModelsList[] = $instance;
		}
		return $modulesModelsList;
	}
}
