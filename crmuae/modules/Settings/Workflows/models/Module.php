<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/com_nectarcrm_workflow/include.inc';
require_once 'modules/com_nectarcrm_workflow/expression_engine/VTExpressionsManager.inc';

class Settings_Workflows_Module_Model extends Settings_nectarcrm_Module_Model {

	var $baseTable = 'com_nectarcrm_workflows';
	var $baseIndex = 'workflow_id';
//	var $listFields = array('summary' => 'Summary', 'module_name' => 'Module', 'execution_condition' => 'Execution Condition');
	var $listFields = array('module_name' => 'Module', 'workflowname' => 'Workflow Name', 'summary'=>'Description', 'execution_condition' => 'Trigger',  'test' => 'Conditions');
	var $name = 'Workflows';

	static $metaVariables = array(
		'Current Date' => '(general : (__nectarcrmMeta__) date) ($_DATE_FORMAT_)',
		'Current Time' => '(general : (__nectarcrmMeta__) time)',
		'System Timezone' => '(general : (__nectarcrmMeta__) dbtimezone)',
		'User Timezone' => '(general : (__nectarcrmMeta__) usertimezone)',
		'CRM Detail View URL' => '(general : (__nectarcrmMeta__) crmdetailviewurl)',
		'Portal Detail View URL' => '(general : (__nectarcrmMeta__) portaldetailviewurl)',
		'Site Url' => '(general : (__nectarcrmMeta__) siteurl)',
		'Portal Url' => '(general : (__nectarcrmMeta__) portalurl)',
		'Record Id' => '(general : (__nectarcrmMeta__) recordId)',
		'LBL_HELPDESK_SUPPORT_NAME' => '(general : (__nectarcrmMeta__) supportName)',
		'LBL_HELPDESK_SUPPORT_EMAILID' => '(general : (__nectarcrmMeta__) supportEmailid)',
	);

	static $triggerTypes = array(
		1 => 'ON_FIRST_SAVE',
		2 => 'ONCE',
		3 => 'ON_EVERY_SAVE',
		4 => 'ON_MODIFY',
		// Reserving 5 & 6 for ON_DELETE and ON_SCHEDULED types.
		6=>	 'ON_SCHEDULE'
	);

	/**
	 * Function to get the url for default view of the module
	 * @return <string> - url
	 */
	public static function getDefaultUrl() {
		return 'index.php?module=Workflows&parent=Settings&view=List';
	}

	/**
	 * Function to get the url for create view of the module
	 * @return <string> - url
	 */
	public static function getCreateViewUrl() {
		return "javascript:Settings_Workflows_List_Js.triggerCreate('index.php?module=Workflows&parent=Settings&view=Edit')";
	}

	public static function getCreateRecordUrl() {
		return 'index.php?module=Workflows&parent=Settings&view=Edit';
	}

	public static function getSupportedModules() {
		$moduleModels = nectarcrm_Module_Model::getAll(array(0,2));
		$supportedModuleModels = array();
		foreach($moduleModels as $tabId => $moduleModel) {
			if($moduleModel->isWorkflowSupported() && $moduleModel->getName() != 'Webmails') {
				$supportedModuleModels[$tabId] = $moduleModel;
			}
		}
		return $supportedModuleModels;
	}

	public static function getTriggerTypes() {
		return self::$triggerTypes;
	}

	public static function getExpressions() {
		$db = PearDatabase::getInstance();

		$mem = new VTExpressionsManager($db);
		return $mem->expressionFunctions();
	}

	public static function getMetaVariables() {
		return self::$metaVariables;
	}

	public function getListFields() {
		if(!$this->listFieldModels) {
			$fields = $this->listFields;
			$fieldObjects = array();
			foreach($fields as $fieldName => $fieldLabel) {
				if($fieldName == 'module_name' || $fieldName == 'execution_condition') {
					$fieldObjects[$fieldName] = new nectarcrm_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel, 'sort'=>false));
				} else {
					$fieldObjects[$fieldName] = new nectarcrm_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel));
				}
			}
			$this->listFieldModels = $fieldObjects;
		}
		return $this->listFieldModels;
	}

	/**
	 * Function to get the count of active workflows
	 * @return <Integer> count of active workflows
	 */
	public function getActiveWorkflowCount($moduleCount = false){
		$db = PearDatabase::getInstance();

		$query = 'SELECT count(*) AS count, nectarcrm_tab.tabid FROM com_nectarcrm_workflows 
				  INNER JOIN nectarcrm_tab ON nectarcrm_tab.name = com_nectarcrm_workflows.module_name 
				  AND nectarcrm_tab.presence IN (0,2) WHERE com_nectarcrm_workflows.status = ? ';

		if($moduleCount){
		   $query .=' GROUP BY com_nectarcrm_workflows.module_name';
		}

		$result = $db->pquery($query, array(1));
		$count = 0;
		$wfModulesCount = array();
		$noOfRows = $db->num_rows($result);
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$count = $count+$row['count'];
			$wfModulesCount[$row['tabid']] = $row['count'];
		}

		if($moduleCount){
		   $wfModulesCount['All'] = $count;
		   return $wfModulesCount;
		} else {
		   return $count;
		}

	}

	public function getFields() {
	   return array();
	}

	public function getModuleBasicLinks(){
	   return array();
	}
}
