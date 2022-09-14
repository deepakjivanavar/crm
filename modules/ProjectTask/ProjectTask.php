<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
class ProjectTask extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'nectarcrm_projecttask';
	var $table_index= 'projecttaskid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_projecttaskcf', 'projecttaskid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('nectarcrm_crmentity', 'nectarcrm_projecttask', 'nectarcrm_projecttaskcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'nectarcrm_crmentity' => 'crmid',
		'nectarcrm_projecttask'   => 'projecttaskid',
		'nectarcrm_projecttaskcf' => 'projecttaskid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'nectarcrm_'
		'Project Task Name'=> Array('projecttask', 'projecttaskname'),
		'Start Date'=> Array('projecttask', 'startdate'),
		'End Date'=> Array('projecttask', 'enddate'),
		'Type'=>Array('projecttask','projecttasktype'),
		'Progress'=>Array('projecttask','projecttaskprogress'),
		'Assigned To' => Array('crmentity','smownerid')

	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Project Task Name'=> 'projecttaskname',
		'Start Date'=>'startdate',
		'End Date'=> 'enddate',
		'Type'=>'projecttasktype',
		'Progress'=>'projecttaskprogress',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'projecttaskname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'nectarcrm_'
		'Project Task Name'=> Array('projecttask', 'projecttaskname'),
		'Start Date'=> Array('projecttask', 'startdate'),
		'Type'=>Array('projecttask','projecttasktype'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Project Task Name'=> 'projecttaskname',
		'Start Date'=>'startdate',
		'Type'=>'projecttasktype',
		'Assigned To' => 'assigned_user_id'
	);

	// For Popup window record selection
	var $popup_fields = Array('projecttaskname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'projecttaskname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'projecttaskname';

	// Required Information for enabling Import feature
	var $required_fields = Array('projecttaskname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'projecttaskname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'projecttaskname', 'projectid', 'assigned_user_id');

	function __construct() {
		global $log, $currentModule;
		$this->column_fields = getColumnFields(get_class($this));
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

   function save_module($module) {
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $where='') {
		$query = "SELECT nectarcrm_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'nectarcrm_crmentity';

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= " LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
		$query .= " LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";

		$joinedTables[] = 'nectarcrm_users';
		$joinedTables[] = 'nectarcrm_groups';

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM nectarcrm_field" .
				" INNER JOIN nectarcrm_fieldmodulerel ON nectarcrm_fieldmodulerel.fieldid = nectarcrm_field.fieldid" .
				" WHERE uitype='10' AND nectarcrm_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other =  CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE nectarcrm_crmentity.deleted = 0 ".$usewhere;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (nectarcrm_crmentity.smownerid in($current_user->id) OR nectarcrm_crmentity.smownerid IN
					(
						SELECT nectarcrm_user2role.userid FROM nectarcrm_user2role
						INNER JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_user2role.userid
						INNER JOIN nectarcrm_role ON nectarcrm_role.roleid=nectarcrm_user2role.roleid
						WHERE nectarcrm_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR nectarcrm_crmentity.smownerid IN
					(
						SELECT shareduserid FROM nectarcrm_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR
						(";

					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " nectarcrm_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " nectarcrm_groups.groupid IN
						(
							SELECT nectarcrm_tmp_read_group_sharing_per.sharedgroupid
							FROM nectarcrm_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('ProjectTask', "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, nectarcrm_users.user_name AS user_name
					FROM nectarcrm_crmentity INNER JOIN $this->table_name ON nectarcrm_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
		$query .= " LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id and nectarcrm_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM nectarcrm_field" .
				" INNER JOIN nectarcrm_fieldmodulerel ON nectarcrm_fieldmodulerel.fieldid = nectarcrm_field.fieldid" .
				" WHERE uitype='10' AND nectarcrm_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " nectarcrm_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, nectarcrm_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
					  " = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
						LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";

		$where_clause = "	WHERE nectarcrm_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN nectarcrm_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}


		$query = $select_clause . $from_clause .
					" LEFT JOIN nectarcrm_users_last_import ON nectarcrm_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		global $adb;
		if($event_type == 'module.postinstall') {
			$projectTaskResult = $adb->pquery('SELECT tabid FROM nectarcrm_tab WHERE name=?', array('ProjectTask'));
			$projecttaskTabid = $adb->query_result($projectTaskResult, 0, 'tabid');

			// Mark the module as Standard module
			$adb->pquery('UPDATE nectarcrm_tab SET customized=0 WHERE name=?', array($modulename));

			if(getTabid('CustomerPortal')) {
				$checkAlreadyExists = $adb->pquery('SELECT 1 FROM nectarcrm_customerportal_tabs WHERE tabid=?', array($projecttaskTabid));
				if($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
					$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM nectarcrm_customerportal_tabs");
					$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
					$nextSequence = $maxSequence+1;
					$adb->query("INSERT INTO nectarcrm_customerportal_tabs(tabid,visible,sequence) VALUES ($projecttaskTabid,1,$nextSequence)");
					$adb->query("INSERT INTO nectarcrm_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($projecttaskTabid,'showrelatedinfo',1)");
				}
			}

			$modcommentsModuleInstance = nectarcrm_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('ProjectTask'));
			}

			$result = $adb->pquery("SELECT 1 FROM nectarcrm_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO nectarcrm_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("nectarcrm_modentity_num"), $modulename, 'PT', 1, 1, 1));
			}
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {

			$modcommentsModuleInstance = nectarcrm_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('ProjectTask'));
			}

			$result = $adb->pquery("SELECT 1 FROM nectarcrm_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO nectarcrm_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("nectarcrm_modentity_num"), $modulename, 'PT', 1, 1, 1));
			}
		}
	}

	/**
	 * Function to check the module active and user action permissions before showing as link in other modules
	 * like in more actions of detail view(Projects).
	 */
	static function isLinkPermitted($linkData) {
		$moduleName = "ProjectTask";
		if(vtlib_isModuleActive($moduleName) && isPermitted($moduleName, 'EditView') == 'yes') {
			return true;
		}
		return false;
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){

		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentityProjectTask', array('nectarcrm_groupsProjectTask', 'nectarcrm_usersProjectTask', 'nectarcrm_lastModifiedByProjectTask'));

		if (!$queryPlanner->requireTable('nectarcrm_projecttask', $matrix)) {
			return '';
		}
		$matrix->setDependency('nectarcrm_projecttask', array('nectarcrm_crmentityProjectTask'));

		$query .= $this->getRelationQuery($module,$secmodule,"nectarcrm_projecttask","projecttaskid", $queryPlanner);

		if ($queryPlanner->requireTable('nectarcrm_crmentityProjectTask', $matrix)) {
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityProjectTask on nectarcrm_crmentityProjectTask.crmid=nectarcrm_projecttask.projecttaskid and nectarcrm_crmentityProjectTask.deleted=0";
		}
		if ($queryPlanner->requireTable('nectarcrm_projecttaskcf')) {
			$query .= " left join nectarcrm_projecttaskcf on nectarcrm_projecttask.projecttaskid = nectarcrm_projecttaskcf.projecttaskid";
		}
		if ($queryPlanner->requireTable('nectarcrm_groupsProjectTask')) {
			$query .= "	left join nectarcrm_groups as nectarcrm_groupsProjectTask on nectarcrm_groupsProjectTask.groupid = nectarcrm_crmentityProjectTask.smownerid";
		}
		if ($queryPlanner->requireTable('nectarcrm_usersProjectTask')) {
			$query .= " left join nectarcrm_users as nectarcrm_usersProjectTask on nectarcrm_usersProjectTask.id = nectarcrm_crmentityProjectTask.smownerid";
		}
		if ($queryPlanner->requireTable('nectarcrm_lastModifiedByProjectTask')) {
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByProjectTask on nectarcrm_lastModifiedByProjectTask.id = nectarcrm_crmentityProjectTask.modifiedby ";
		}
		if ($queryPlanner->requireTable("nectarcrm_createdbyProjectTask")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyProjectTask on nectarcrm_createdbyProjectTask.id = nectarcrm_crmentityProjectTask.smcreatorid ";
		}
		//if secondary modules custom reference field is selected
        $query .= parent::getReportsUiType10Query($secmodule, $queryPlanner);

		return $query;
	}

	function get_emails($recordId, $currentTabId, $relTabId, $actions=false) {
		global $currentModule,$single_pane_view;
		$relModuleName = vtlib_getModuleNameById($relTabId);
		$singularRelModuleName = vtlib_tosingular($relModuleName);
		require_once "modules/$relModuleName/$relModuleName.php";
		$relModuleFocus = new $relModuleName();
		vtlib_setup_modulevars($relModuleName, $relModuleFocus);


		$returnSet = '&return_module='.$currentModule.'&return_action=CallRelatedList&return_id='.$recordId;

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';
		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($relModuleName,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singularRelModuleName)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$currentModule\",$recordId);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singularRelModuleName)."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime,
				nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start,nectarcrm_activity.time_start, nectarcrm_seactivityrel.crmid as parent_id
				FROM nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_projecttask,nectarcrm_users, nectarcrm_crmentity
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				WHERE nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
				AND nectarcrm_seactivityrel.crmid = $recordId
				AND nectarcrm_users.id=nectarcrm_crmentity.smownerid
				AND nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
				AND nectarcrm_activity.activitytype='Emails'
				AND nectarcrm_projecttask.projecttaskid = $recordId
				AND nectarcrm_crmentity.deleted = 0";

		$returnValue = GetRelatedList($currentModule, $relModuleName, $relModuleFocus, $query, $button, $returnSet);

		if(!$returnValue) $returnValue = array();

		$returnValue['CUSTOM_BUTTON'] = $button;
		return $returnValue;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb;

		$rel_table_arr = Array("Documents" => "nectarcrm_senotesrel");

		$tbl_field_arr = Array("nectarcrm_senotesrel" => "notesid");

		$entity_tbl_field_arr = Array("nectarcrm_senotesrel" => "crmid");

		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_module => $rel_table) {
				if (nectarcrm_Module::getInstance($rel_module) != FALSE) {
					$id_field = $tbl_field_arr[$rel_table];
					$entity_id_field = $entity_tbl_field_arr[$rel_table];
					// IN clause to avoid duplicate entries
					$sel_result = $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
							" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)", array($transferId, $entityId));
					$res_cnt = $adb->num_rows($sel_result);
					if ($res_cnt > 0) {
						for ($i = 0; $i < $res_cnt; $i++) {
							$id_field_value = $adb->query_result($sel_result, $i, $id_field);
							$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?", array($entityId, $transferId, $id_field_value));
						}
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
	}

}

?>
