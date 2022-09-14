<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of txhe License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
class Leads extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "nectarcrm_leaddetails";
	var $table_index= 'leadid';

	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_leaddetails','nectarcrm_leadsubdetails','nectarcrm_leadaddress','nectarcrm_leadscf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_leaddetails'=>'leadid','nectarcrm_leadsubdetails'=>'leadsubscriptionid','nectarcrm_leadaddress'=>'leadaddressid','nectarcrm_leadscf'=>'leadid');

	var $entity_table = "nectarcrm_crmentity";

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_leadscf', 'leadid');

	//construct this from database;
	var $column_fields = Array();
	var $sortby_fields = Array('lastname','firstname','email','phone','company','smownerid','website');

	// This is used to retrieve related nectarcrm_fields from form posts.
	var $additional_column_fields = Array('smcreatorid', 'smownerid', 'contactid','potentialid' ,'crmid');

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
		'First Name'=>Array('leaddetails'=>'firstname'),
		'Last Name'=>Array('leaddetails'=>'lastname'),
		'Company'=>Array('leaddetails'=>'company'),
		'Phone'=>Array('leadaddress'=>'phone'),
		'Website'=>Array('leadsubdetails'=>'website'),
		'Email'=>Array('leaddetails'=>'email'),
		'Assigned To'=>Array('crmentity'=>'smownerid')
	);
	var $list_fields_name = Array(
		'First Name'=>'firstname',
		'Last Name'=>'lastname',
		'Company'=>'company',
		'Phone'=>'phone',
		'Website'=>'website',
		'Email'=>'email',
		'Assigned To'=>'assigned_user_id'
	);
	var $list_link_field= 'lastname';

	var $search_fields = Array(
		'Name'=>Array('leaddetails'=>'lastname'),
		'Company'=>Array('leaddetails'=>'company')
	);
	var $search_fields_name = Array(
		'Name'=>'lastname',
		'Company'=>'company'
	);

	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'lastname', 'createdtime' ,'modifiedtime');

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array('firstname','lastname','leadsource','leadstatus','rating','industry','secondaryemail','email','annualrevenue','designation','salutation');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'lastname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'lastname';

	var $LBL_LEAD_MAPPING = 'LBL_LEAD_MAPPING';
	//var $groupTable = Array('nectarcrm_leadgrouprelation','leadid');

	function Leads()	{
		$this->log = LoggerManager::getLogger('lead');
		$this->log->debug("Entering Leads() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Leads');
		$this->log->debug("Exiting Lead method ...");
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module)
	{
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Leads Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Leads", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT $fields_list,case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
					FROM ".$this->entity_table."
				INNER JOIN nectarcrm_leaddetails
					ON nectarcrm_crmentity.crmid=nectarcrm_leaddetails.leadid
				LEFT JOIN nectarcrm_leadsubdetails
					ON nectarcrm_leaddetails.leadid = nectarcrm_leadsubdetails.leadsubscriptionid
				LEFT JOIN nectarcrm_leadaddress
					ON nectarcrm_leaddetails.leadid=nectarcrm_leadaddress.leadaddressid
				LEFT JOIN nectarcrm_leadscf
					ON nectarcrm_leadscf.leadid=nectarcrm_leaddetails.leadid
							LEFT JOIN nectarcrm_groups
									ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_crmentity.smownerid = nectarcrm_users.id and nectarcrm_users.status='Active'
				";

		$query .= $this->getNonAdminAccessControlQuery('Leads',$current_user);
		$where_auto = " nectarcrm_crmentity.deleted=0 AND nectarcrm_leaddetails.converted =0";

		if($where != "")
			$query .= " where ($where) AND ".$where_auto;
		else
			$query .= " where ".$where_auto;

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}



	/** Returns a list of the associated tasks
	 * @param  integer   $id      - leadid
	 * returns related Task or Event record in array format
	*/
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
				if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_activity.*,nectarcrm_seactivityrel.crmid as parent_id, nectarcrm_contactdetails.lastname,
			nectarcrm_contactdetails.contactid, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_crmentity.modifiedtime,case when (nectarcrm_users.user_name not like '') then
		$userNameSql else nectarcrm_groups.groupname end as user_name,
		nectarcrm_recurringevents.recurringtype
		from nectarcrm_activity inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=
		nectarcrm_activity.activityid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=
		nectarcrm_activity.activityid left join nectarcrm_cntactivityrel on
		nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid left join
		nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
		left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
		left outer join nectarcrm_recurringevents on nectarcrm_recurringevents.activityid=
		nectarcrm_activity.activityid left join nectarcrm_groups on nectarcrm_groups.groupid=
		nectarcrm_crmentity.smownerid where nectarcrm_seactivityrel.crmid=".$id." and
			nectarcrm_crmentity.deleted = 0 and ((nectarcrm_activity.activitytype='Task' and
			nectarcrm_activity.status not in ('Completed','Deferred')) or
			(nectarcrm_activity.activitytype NOT in ('Emails','Task') and
			nectarcrm_activity.eventstatus not in ('','Held'))) ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_quotes(".$id.") method ...");
		$this_module = $currentModule;
		$related_module = vtlib_getModuleNameById($rel_tab_id);

		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();
		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';
		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT nectarcrm_crmentity.*, nectarcrm_quotes.*, nectarcrm_leaddetails.leadid,
					case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
					FROM nectarcrm_quotes
					INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_leaddetails ON nectarcrm_leaddetails.leadid = nectarcrm_quotes.contactid
					LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
					LEFT JOIN nectarcrm_quotescf ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_quotesbillads ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_quotesshipads ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
					WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.leadid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	}

	/** Returns a list of the associated Campaigns
	  * @param $id -- campaign id :: Type Integer
	  * @returns list of campaigns in array format
	  */
	function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_campaigns(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name ,
				nectarcrm_campaign.campaignid, nectarcrm_campaign.campaignname, nectarcrm_campaign.campaigntype, nectarcrm_campaign.campaignstatus,
				nectarcrm_campaign.expectedrevenue, nectarcrm_campaign.closingdate, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
				nectarcrm_crmentity.modifiedtime from nectarcrm_campaign
				inner join nectarcrm_campaignleadrel on nectarcrm_campaignleadrel.campaignid=nectarcrm_campaign.campaignid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_campaign.campaignid
				inner join nectarcrm_campaignscf ON nectarcrm_campaignscf.campaignid = nectarcrm_campaign.campaignid
				left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid
				where nectarcrm_campaignleadrel.leadid=".$id." and nectarcrm_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}


		/** Returns a list of the associated emails
		 * @param  integer   $id      - leadid
		 * returns related emails record in array format
		*/
	function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_emails(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query ="select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name," .
				" nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.semodule, nectarcrm_activity.activitytype," .
				" nectarcrm_activity.date_start, nectarcrm_activity.time_start, nectarcrm_activity.status, nectarcrm_activity.priority, nectarcrm_crmentity.crmid," .
				" nectarcrm_crmentity.smownerid,nectarcrm_crmentity.modifiedtime, nectarcrm_users.user_name, nectarcrm_seactivityrel.crmid as parent_id " .
				" from nectarcrm_activity" .
				" inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid" .
				" inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid" .
				" left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid" .
				" left join nectarcrm_users on  nectarcrm_users.id=nectarcrm_crmentity.smownerid" .
				" where nectarcrm_activity.activitytype='Emails' and nectarcrm_crmentity.deleted=0 and nectarcrm_seactivityrel.crmid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
		return $return_value;
	}

	/**
	 * Function to get Lead related Task & Event which have activity type Held, Completed or Deferred.
	 * @param  integer   $id      - leadid
	 * returns related Task or Event record in array format
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.status,
			nectarcrm_activity.eventstatus, nectarcrm_activity.activitytype,nectarcrm_activity.date_start,
			nectarcrm_activity.due_date,nectarcrm_activity.time_start,nectarcrm_activity.time_end,
			nectarcrm_crmentity.modifiedtime,nectarcrm_crmentity.createdtime,
			nectarcrm_crmentity.description, $userNameSql as user_name,nectarcrm_groups.groupname
				from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_crmentity.smownerid= nectarcrm_users.id
				where (nectarcrm_activity.activitytype != 'Emails')
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred' or (nectarcrm_activity.eventstatus = 'Held' and nectarcrm_activity.eventstatus != ''))
				and nectarcrm_seactivityrel.crmid=".$id."
							and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Leads',$query,$id);
	}

	/**
	* Function to get lead related Products
	* @param  integer   $id      - leadid
	* returns related Products record in array format
	*/
	function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_products(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$query = "SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode,
				nectarcrm_products.commissionrate, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price,
				nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid
				FROM nectarcrm_products
				INNER JOIN nectarcrm_seproductsrel ON nectarcrm_products.productid = nectarcrm_seproductsrel.productid  and nectarcrm_seproductsrel.setype = 'Leads'
				INNER JOIN nectarcrm_productcf
					ON nectarcrm_products.productid = nectarcrm_productcf.productid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
				INNER JOIN nectarcrm_leaddetails ON nectarcrm_leaddetails.leadid = nectarcrm_seproductsrel.crmid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups
					ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			   WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.leadid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/** Function to get the Columnnames of the Leads Record
	* Used By nectarcrmCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	function getColumnNames_Lead()
	{
		global $log,$current_user;
		$log->debug("Entering getColumnNames_Lead() method ...");
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql1 = "select fieldlabel from nectarcrm_field where tabid=7 and nectarcrm_field.presence in (0,2)";
			$params1 = array();
		}else
		{
			$profileList = getCurrentUserProfileList();
			$sql1 = "select nectarcrm_field.fieldid,fieldlabel from nectarcrm_field inner join nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=7 and nectarcrm_field.displaytype in (1,2,3,4) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .")  group by fieldid";
				array_push($params1, $profileList);
			}
		}
		$result = $this->db->pquery($sql1, $params1);
		$numRows = $this->db->num_rows($result);
		for($i=0; $i < $numRows;$i++)
		{
		$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
		$custom_fields[$i] = preg_replace("/\s+/","",$custom_fields[$i]);
		$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug("Exiting getColumnNames_Lead method ...");
		return $mergeflds;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Activities"=>"nectarcrm_seactivityrel","Documents"=>"nectarcrm_senotesrel","Attachments"=>"nectarcrm_seattachmentsrel",
					"Products"=>"nectarcrm_seproductsrel","Campaigns"=>"nectarcrm_campaignleadrel","Emails"=>"nectarcrm_seactivityrel");

		$tbl_field_arr = Array("nectarcrm_seactivityrel"=>"activityid","nectarcrm_senotesrel"=>"notesid","nectarcrm_seattachmentsrel"=>"attachmentsid",
					"nectarcrm_seproductsrel"=>"productid","nectarcrm_campaignleadrel"=>"campaignid","nectarcrm_seactivityrel"=>"activityid");

		$entity_tbl_field_arr = Array("nectarcrm_seactivityrel"=>"crmid","nectarcrm_senotesrel"=>"crmid","nectarcrm_seattachmentsrel"=>"crmid",
					"nectarcrm_seproductsrel"=>"crmid","nectarcrm_campaignleadrel"=>"leadid","nectarcrm_seactivityrel"=>"crmid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
						array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule, $queryPlanner) {
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentityLeads',array('nectarcrm_groupsLeads','nectarcrm_usersLeads','nectarcrm_lastModifiedByLeads'));

		// TODO Support query planner
		if (!$queryPlanner->requireTable("nectarcrm_leaddetails",$matrix)){
			return '';
		}

		$matrix->setDependency('nectarcrm_leaddetails',array('nectarcrm_crmentityLeads', 'nectarcrm_leadaddress','nectarcrm_leadsubdetails','nectarcrm_leadscf','nectarcrm_email_trackLeads'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_leaddetails","leadid", $queryPlanner);
		if ($queryPlanner->requireTable("nectarcrm_crmentityLeads",$matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityLeads on nectarcrm_crmentityLeads.crmid = nectarcrm_leaddetails.leadid and nectarcrm_crmentityLeads.deleted=0";
		}
		if ($queryPlanner->requireTable("nectarcrm_leadaddress")){
			$query .= " left join nectarcrm_leadaddress on nectarcrm_leaddetails.leadid = nectarcrm_leadaddress.leadaddressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_leadsubdetails")){
			$query .= " left join nectarcrm_leadsubdetails on nectarcrm_leadsubdetails.leadsubscriptionid = nectarcrm_leaddetails.leadid";
		}
		if ($queryPlanner->requireTable("nectarcrm_leadscf")){
			$query .= " left join nectarcrm_leadscf on nectarcrm_leadscf.leadid = nectarcrm_leaddetails.leadid";
		}
		if ($queryPlanner->requireTable("nectarcrm_email_trackLeads")){
			$query .= " LEFT JOIN nectarcrm_email_track AS nectarcrm_email_trackLeads ON nectarcrm_email_trackLeads.crmid = nectarcrm_leaddetails.leadid";
		}
		if ($queryPlanner->requireTable("nectarcrm_groupsLeads")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsLeads on nectarcrm_groupsLeads.groupid = nectarcrm_crmentityLeads.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_usersLeads")){
			$query .= " left join nectarcrm_users as nectarcrm_usersLeads on nectarcrm_usersLeads.id = nectarcrm_crmentityLeads.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_lastModifiedByLeads")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByLeads on nectarcrm_lastModifiedByLeads.id = nectarcrm_crmentityLeads.modifiedby ";
		}
		if ($queryPlanner->requireTable("nectarcrm_createdbyLeads")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyLeads on nectarcrm_createdbyLeads.id = nectarcrm_crmentityLeads.smcreatorid ";
		}

		//if secondary modules custom reference field is selected
        $query .= parent::getReportsUiType10Query($secmodule, $queryPlanner);
        
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_leaddetails"=>"leadid"),
			"Products" => array("nectarcrm_seproductsrel"=>array("crmid","productid"),"nectarcrm_leaddetails"=>"leadid"),
			"Campaigns" => array("nectarcrm_campaignleadrel"=>array("leadid","campaignid"),"nectarcrm_leaddetails"=>"leadid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_leaddetails"=>"leadid"),
			"Services" => array("nectarcrm_crmentityrel"=>array("crmid","relcrmid"),"nectarcrm_leaddetails"=>"leadid"),
			"Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_leaddetails"=>"leadid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Campaigns') {
			$sql = 'DELETE FROM nectarcrm_campaignleadrel WHERE leadid=? AND campaignid=?';
			$this->db->pquery($sql, array($id, $return_id));
		}
		elseif($return_module == 'Products') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Documents') {
			$sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function getListButtons($app_strings) {
		$list_buttons = Array();

		if(isPermitted('Leads','Delete','') == 'yes') {
			$list_buttons['del'] =	$app_strings[LBL_MASS_DELETE];
		}
		if(isPermitted('Leads','EditView','') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
			$list_buttons['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
		}
		if(isPermitted('Emails','EditView','') == 'yes')
			$list_buttons['s_mail'] = $app_strings[LBL_SEND_MAIL_BUTTON];

		// end of mailer export
		return $list_buttons;
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Products')
				$adb->pquery('INSERT INTO nectarcrm_seproductsrel VALUES(?,?,?,?)', array($crmid, $with_crmid, $module, 1));
			elseif($with_module == 'Campaigns')
				$adb->pquery("insert into  nectarcrm_campaignleadrel values(?,?,1)", array($with_crmid, $crmid));
			else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	function getQueryForDuplicates($module, $tableColumns, $selectedColumns = '', $ignoreEmpty = false, $requiredTables = array()) {
		if(is_array($tableColumns)) {
			$tableColumnsString = implode(',', $tableColumns);
		}
		$selectClause = "SELECT " . $this->table_name . "." . $this->table_index . " AS recordid," . $tableColumnsString;

		// Select Custom Field Table Columns if present
		if (isset($this->customFieldTable))
			$query .= ", " . $this->customFieldTable[0] . ".* ";

		$fromClause = " FROM $this->table_name";

		$fromClause .= " INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $this->table_name.$this->table_index";

		if($this->tab_name) {
			foreach($this->tab_name as $tableName) {
				if($tableName != 'nectarcrm_crmentity' && $tableName != $this->table_name && in_array($tableName, $requiredTables)) {
					if($this->tab_name_index[$tableName]) {
						$fromClause .= " INNER JOIN " . $tableName . " ON " . $tableName . '.' . $this->tab_name_index[$tableName] .
							" = $this->table_name.$this->table_index";
					}
				}
			}
		}

		$whereClause = " WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.converted=0 ";
		$whereClause .= $this->getListViewSecurityParameter($module);

		if($ignoreEmpty) {
			foreach($tableColumns as $tableColumn){
				$whereClause .= " AND ($tableColumn IS NOT NULL AND $tableColumn != '') ";
			}
		}

		if (isset($selectedColumns) && trim($selectedColumns) != '') {
			$sub_query = "SELECT $selectedColumns FROM $this->table_name AS t " .
					" INNER JOIN nectarcrm_crmentity AS crm ON crm.crmid = t." . $this->table_index;
			// Consider custom table join as well.
			if (isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN " . $this->customFieldTable[0] . " tcf ON tcf." . $this->customFieldTable[1] . " = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $selectedColumns HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $tableColumnsString $fromClause $whereClause GROUP BY $tableColumnsString HAVING COUNT(*)>1";
		}

		$i = 1;
		foreach($tableColumns as $tableColumn){
			$tableInfo = explode('.', $tableColumn);
			$duplicateCheckClause .= " ifnull($tableColumn,'null') = ifnull(temp.$tableInfo[1],'null')";
			if (count($tableColumns) != $i++) $duplicateCheckClause .= " AND ";
		}

		$query = $selectClause . $fromClause .
				" LEFT JOIN nectarcrm_users_last_import ON nectarcrm_users_last_import.bean_id=" . $this->table_name . "." . $this->table_index .
				" INNER JOIN (" . $sub_query . ") AS temp ON " . $duplicateCheckClause .
				$whereClause .
				" ORDER BY $tableColumnsString," . $this->table_name . "." . $this->table_index . " ASC";
		return $query;
	}

	/**
	 * Invoked when special actions are to be performed on the module.
	 * @param String Module name
	 * @param String Event Type
	 */
	function vtlib_handler($moduleName, $eventType) {
		if ($moduleName == 'Leads') {
			$db = PearDatabase::getInstance();
			if ($eventType == 'module.disabled') {
				$db->pquery('UPDATE nectarcrm_settings_field SET active=1 WHERE name=?', array($this->LBL_LEAD_MAPPING));
			} else if ($eventType == 'module.enabled') {
				$db->pquery('UPDATE nectarcrm_settings_field SET active=0 WHERE name=?', array($this->LBL_LEAD_MAPPING));
			}
		}
	}
}

?>