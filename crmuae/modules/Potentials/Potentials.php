<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/nectarcrm_crm/sugarcrm/modules/Potentials/Potentials.php,v 1.65 2005/04/28 08:08:27 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

class Potentials extends CRMEntity {
	var $log;
	var $db;

	var $module_name="Potentials";
	var $table_name = "nectarcrm_potential";
	var $table_index= 'potentialid';

	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_potential','nectarcrm_potentialscf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_potential'=>'potentialid','nectarcrm_potentialscf'=>'potentialid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_potentialscf', 'potentialid');

	var $column_fields = Array();

	var $sortby_fields = Array('potentialname','amount','closingdate','smownerid','accountname');

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
			'Potential'=>Array('potential'=>'potentialname'),
			'Organization Name'=>Array('potential'=>'related_to'),
			'Contact Name'=>Array('potential'=>'contact_id'),
			'Sales Stage'=>Array('potential'=>'sales_stage'),
			'Amount'=>Array('potential'=>'amount'),
			'Expected Close Date'=>Array('potential'=>'closingdate'),
			'Assigned To'=>Array('crmentity','smownerid')
			);

	var $list_fields_name = Array(
			'Potential'=>'potentialname',
			'Organization Name'=>'related_to',
			'Contact Name'=>'contact_id',
			'Sales Stage'=>'sales_stage',
			'Amount'=>'amount',
			'Expected Close Date'=>'closingdate',
			'Assigned To'=>'assigned_user_id');

	var $list_link_field= 'potentialname';

	var $search_fields = Array(
			'Potential'=>Array('potential'=>'potentialname'),
			'Related To'=>Array('potential'=>'related_to'),
			'Expected Close Date'=>Array('potential'=>'closedate')
			);

	var $search_fields_name = Array(
			'Potential'=>'potentialname',
			'Related To'=>'related_to',
			'Expected Close Date'=>'closingdate'
			);

	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'potentialname');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'potentialname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'potentialname';

	var $related_module_table_index = array(
		'Contacts' => array('table_name'=>'nectarcrm_contactdetails','table_index'=>'contactid','rel_index'=>'contactid')
	);

	var $LBL_POTENTIAL_MAPPING = 'LBL_OPPORTUNITY_MAPPING';
	//var $groupTable = Array('nectarcrm_potentialgrouprelation','potentialid');
	function Potentials() {
		$this->log = LoggerManager::getLogger('potential');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Potentials');
	}

	function save_module($module)
	{
	}

	/** Function to create list query
	* @param reference variable - where condition is passed when the query is executed
	* Returns Query.
	*/
	function create_list_query($order_by, $where)
	{
		global $log,$current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
	        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
        	$tab_id = getTabid("Potentials");
		$log->debug("Entering create_list_query(".$order_by.",". $where.") method ...");
		// Determine if the nectarcrm_account name is present in the where clause.
		$account_required = preg_match("/accounts\.name/", $where);

		if($account_required)
		{
			$query = "SELECT nectarcrm_potential.potentialid,  nectarcrm_potential.potentialname, nectarcrm_potential.dateclosed FROM nectarcrm_potential, nectarcrm_account ";
			$where_auto = "account.accountid = nectarcrm_potential.related_to AND nectarcrm_crmentity.deleted=0 ";
		}
		else
		{
			$query = 'SELECT nectarcrm_potential.potentialid, nectarcrm_potential.potentialname, nectarcrm_crmentity.smcreatorid, nectarcrm_potential.closingdate FROM nectarcrm_potential inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_potential.potentialid LEFT JOIN nectarcrm_groups on nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid ';
			$where_auto = ' AND nectarcrm_crmentity.deleted=0';
		}

		$query .= $this->getNonAdminAccessControlQuery('Potentials',$current_user);
		if($where != "")
			$query .= " where $where ".$where_auto;
		else
			$query .= " where ".$where_auto;
		if($order_by != "")
			$query .= " ORDER BY $order_by";

		$log->debug("Exiting create_list_query method ...");
		return $query;
	}

	/** Function to export the Opportunities records in CSV Format
	* @param reference variable - order by is passed when the query is executed
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Potentials Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(". $where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Potentials", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT $fields_list,case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				FROM nectarcrm_potential
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_potential.potentialid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid=nectarcrm_users.id
				LEFT JOIN nectarcrm_account on nectarcrm_potential.related_to=nectarcrm_account.accountid
				LEFT JOIN nectarcrm_contactdetails on nectarcrm_potential.contact_id=nectarcrm_contactdetails.contactid
				LEFT JOIN nectarcrm_potentialscf on nectarcrm_potentialscf.potentialid=nectarcrm_potential.potentialid
                LEFT JOIN nectarcrm_groups
        	        ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_campaign
					ON nectarcrm_campaign.campaignid = nectarcrm_potential.campaignid";

		$query .= $this->getNonAdminAccessControlQuery('Potentials',$current_user);
		$where_auto = "  nectarcrm_crmentity.deleted = 0 ";

                if($where != "")
                   $query .= "  WHERE ($where) AND ".$where_auto;
                else
                   $query .= "  WHERE ".$where_auto;

		$log->debug("Exiting create_export_query method ...");
		return $query;

	}



	/** Returns a list of the associated contacts
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_contacts(".$id.") method ...");
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

		$accountid = $this->column_fields['related_to'];
		$search_string = "&fromPotential=true&acc_id=$accountid";

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab$search_string','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = 'select case when (nectarcrm_users.user_name not like "") then '.$userNameSql.' else nectarcrm_groups.groupname end as user_name,
					nectarcrm_contactdetails.accountid,nectarcrm_potential.potentialid, nectarcrm_potential.potentialname, nectarcrm_contactdetails.contactid,
					nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.title, nectarcrm_contactdetails.department,
					nectarcrm_contactdetails.email, nectarcrm_contactdetails.phone, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
					nectarcrm_crmentity.modifiedtime , nectarcrm_account.accountname from nectarcrm_potential
					left join nectarcrm_contpotentialrel on nectarcrm_contpotentialrel.potentialid = nectarcrm_potential.potentialid
					inner join nectarcrm_contactdetails on ((nectarcrm_contactdetails.contactid = nectarcrm_contpotentialrel.contactid) or (nectarcrm_contactdetails.contactid = nectarcrm_potential.contact_id))
					INNER JOIN nectarcrm_contactaddress ON nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid
					INNER JOIN nectarcrm_contactsubdetails ON nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid
					INNER JOIN nectarcrm_customerdetails ON nectarcrm_contactdetails.contactid = nectarcrm_customerdetails.customerid
					INNER JOIN nectarcrm_contactscf ON nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid
					inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
					left join nectarcrm_account on nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
					left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
					left join nectarcrm_users on nectarcrm_crmentity.smownerid=nectarcrm_users.id
					where nectarcrm_potential.potentialid = '.$id.' and nectarcrm_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/** Returns a list of the associated calls
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
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
		$query = "SELECT nectarcrm_activity.activityid as 'tmp_activity_id',nectarcrm_activity.*,nectarcrm_seactivityrel.crmid as parent_id, nectarcrm_contactdetails.lastname,nectarcrm_contactdetails.firstname,
					nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime,
					case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
					nectarcrm_recurringevents.recurringtype from nectarcrm_activity
					inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
					inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
					left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
					left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
					inner join nectarcrm_potential on nectarcrm_potential.potentialid=nectarcrm_seactivityrel.crmid
					left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
					left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
					left outer join nectarcrm_recurringevents on nectarcrm_recurringevents.activityid=nectarcrm_activity.activityid
					where nectarcrm_seactivityrel.crmid=".$id." and nectarcrm_crmentity.deleted=0
					and ((nectarcrm_activity.activitytype='Task' and nectarcrm_activity.status not in ('Completed','Deferred'))
					or (nectarcrm_activity.activitytype NOT in ('Emails','Task') and  nectarcrm_activity.eventstatus not in ('','Held'))) ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	 /**
	 * Function to get Contact related Products
	 * @param  integer   $id  - contactid
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
				INNER JOIN nectarcrm_seproductsrel ON nectarcrm_products.productid = nectarcrm_seproductsrel.productid and nectarcrm_seproductsrel.setype = 'Potentials'
				INNER JOIN nectarcrm_productcf
				ON nectarcrm_products.productid = nectarcrm_productcf.productid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
				INNER JOIN nectarcrm_potential ON nectarcrm_potential.potentialid = nectarcrm_seproductsrel.crmid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups
					ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_potential.potentialid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/**	Function used to get the Sales Stage history of the Potential
	 *	@param $id - potentialid
	 *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
	 */
	function get_stage_history($id)
	{
		global $log;
		$log->debug("Entering get_stage_history(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select nectarcrm_potstagehistory.*, nectarcrm_potential.potentialname from nectarcrm_potstagehistory inner join nectarcrm_potential on nectarcrm_potential.potentialid = nectarcrm_potstagehistory.potentialid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid where nectarcrm_crmentity.deleted = 0 and nectarcrm_potential.potentialid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SALES_STAGE'];
		$header[] = $app_strings['LBL_PROBABILITY'];
		$header[] = $app_strings['LBL_CLOSE_DATE'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$amount_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'amount') != '0')? 1 : 0;
		$probability_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'probability') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Potentials');

		$potential_stage_array = $picklistarray['sales_stage'];
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = 'Not Accessible';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			$entries[] = ($amount_access != 1)? $row['amount'] : 0;
			$entries[] = (in_array($row['stage'], $potential_stage_array))? $row['stage']: $error_msg;
			$entries[] = ($probability_access != 1) ? $row['probability'] : 0;
			$entries[] = DateTimeField::convertToUserFormat($row['closedate']);
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDate();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_stage_history method ...");

		return $return_data;
	}

	/**
	* Function to get Potential related Task & Event which have activity type Held, Completed or Deferred.
	* @param  integer   $id
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
		nectarcrm_activity.due_date, nectarcrm_activity.time_start,nectarcrm_activity.time_end,
		nectarcrm_crmentity.modifiedtime, nectarcrm_crmentity.createdtime,
		nectarcrm_crmentity.description,case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				where (nectarcrm_activity.activitytype != 'Emails')
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred' or (nectarcrm_activity.eventstatus = 'Held' and nectarcrm_activity.eventstatus != ''))
				and nectarcrm_seactivityrel.crmid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Potentials',$query,$id);
	}


	  /**
	  * Function to get Potential related Quotes
	  * @param  integer   $id  - potentialid
	  * returns related Quotes record in array format
	  */
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
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

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
					nectarcrm_account.accountname, nectarcrm_crmentity.*, nectarcrm_quotes.*, nectarcrm_potential.potentialname from nectarcrm_quotes
					inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_quotes.quoteid
					left outer join nectarcrm_potential on nectarcrm_potential.potentialid=nectarcrm_quotes.potentialid
					left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
                    LEFT JOIN nectarcrm_quotescf ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_quotesbillads ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid
					LEFT JOIN nectarcrm_quotesshipads ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid
					left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
					LEFT join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_quotes.accountid
					where nectarcrm_crmentity.deleted=0 and nectarcrm_potential.potentialid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	}

	/**
	 * Function to get Potential related SalesOrder
 	 * @param  integer   $id  - potentialid
	 * returns related SalesOrder record in array format
	 */
	function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_salesorder(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
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

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select nectarcrm_crmentity.*, nectarcrm_salesorder.*, nectarcrm_quotes.subject as quotename
			, nectarcrm_account.accountname, nectarcrm_potential.potentialname,case when
			(nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname
			end as user_name from nectarcrm_salesorder
			inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_salesorder.salesorderid
			left outer join nectarcrm_quotes on nectarcrm_quotes.quoteid=nectarcrm_salesorder.quoteid
			left outer join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_salesorder.accountid
			left outer join nectarcrm_potential on nectarcrm_potential.potentialid=nectarcrm_salesorder.potentialid
			left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
            LEFT JOIN nectarcrm_salesordercf ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
            LEFT JOIN nectarcrm_invoice_recurring_info ON nectarcrm_invoice_recurring_info.start_period = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_sobillads ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_soshipads ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid
			left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
			 where nectarcrm_crmentity.deleted=0 and nectarcrm_potential.potentialid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
		return $return_value;
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

		$rel_table_arr = Array("Activities"=>"nectarcrm_seactivityrel","Contacts"=>"nectarcrm_contpotentialrel","Products"=>"nectarcrm_seproductsrel",
						"Attachments"=>"nectarcrm_seattachmentsrel","Quotes"=>"nectarcrm_quotes","SalesOrder"=>"nectarcrm_salesorder",
						"Documents"=>"nectarcrm_senotesrel");

		$tbl_field_arr = Array("nectarcrm_seactivityrel"=>"activityid","nectarcrm_contpotentialrel"=>"contactid","nectarcrm_seproductsrel"=>"productid",
						"nectarcrm_seattachmentsrel"=>"attachmentsid","nectarcrm_quotes"=>"quoteid","nectarcrm_salesorder"=>"salesorderid",
						"nectarcrm_senotesrel"=>"notesid");

		$entity_tbl_field_arr = Array("nectarcrm_seactivityrel"=>"crmid","nectarcrm_contpotentialrel"=>"potentialid","nectarcrm_seproductsrel"=>"crmid",
						"nectarcrm_seattachmentsrel"=>"crmid","nectarcrm_quotes"=>"potentialid","nectarcrm_salesorder"=>"potentialid",
						"nectarcrm_senotesrel"=>"crmid");

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
	function generateReportsSecQuery($module,$secmodule,$queryplanner){
		$matrix = $queryplanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentityPotentials',array('nectarcrm_groupsPotentials','nectarcrm_usersPotentials','nectarcrm_lastModifiedByPotentials'));

		if (!$queryplanner->requireTable("nectarcrm_potential",$matrix)){
			return '';
		}
        $matrix->setDependency('nectarcrm_potential', array('nectarcrm_crmentityPotentials','nectarcrm_accountPotentials',
											'nectarcrm_contactdetailsPotentials','nectarcrm_campaignPotentials','nectarcrm_potentialscf'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_potential","potentialid", $queryplanner);

		if ($queryplanner->requireTable("nectarcrm_crmentityPotentials",$matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityPotentials on nectarcrm_crmentityPotentials.crmid=nectarcrm_potential.potentialid and nectarcrm_crmentityPotentials.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_accountPotentials")){
			$query .= " left join nectarcrm_account as nectarcrm_accountPotentials on nectarcrm_potential.related_to = nectarcrm_accountPotentials.accountid";
		}
		if ($queryplanner->requireTable("nectarcrm_contactdetailsPotentials")){
			$query .= " left join nectarcrm_contactdetails as nectarcrm_contactdetailsPotentials on nectarcrm_potential.contact_id = nectarcrm_contactdetailsPotentials.contactid";
		}
		if ($queryplanner->requireTable("nectarcrm_potentialscf")){
			$query .= " left join nectarcrm_potentialscf on nectarcrm_potentialscf.potentialid = nectarcrm_potential.potentialid";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsPotentials")){
			$query .= " left join nectarcrm_groups nectarcrm_groupsPotentials on nectarcrm_groupsPotentials.groupid = nectarcrm_crmentityPotentials.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersPotentials")){
			$query .= " left join nectarcrm_users as nectarcrm_usersPotentials on nectarcrm_usersPotentials.id = nectarcrm_crmentityPotentials.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_campaignPotentials")){
			$query .= " left join nectarcrm_campaign as nectarcrm_campaignPotentials on nectarcrm_potential.campaignid = nectarcrm_campaignPotentials.campaignid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByPotentials")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByPotentials on nectarcrm_lastModifiedByPotentials.id = nectarcrm_crmentityPotentials.modifiedby ";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyPotentials")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyPotentials on nectarcrm_createdbyPotentials.id = nectarcrm_crmentityPotentials.smcreatorid ";
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
			"Calendar" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_potential"=>"potentialid"),
			"Products" => array("nectarcrm_seproductsrel"=>array("crmid","productid"),"nectarcrm_potential"=>"potentialid"),
			"Quotes" => array("nectarcrm_quotes"=>array("potentialid","quoteid"),"nectarcrm_potential"=>"potentialid"),
			"SalesOrder" => array("nectarcrm_salesorder"=>array("potentialid","salesorderid"),"nectarcrm_potential"=>"potentialid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_potential"=>"potentialid"),
			"Accounts" => array("nectarcrm_potential"=>array("potentialid","related_to")),
			"Contacts" => array("nectarcrm_potential"=>array("potentialid","contact_id")),
            "Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_potential"=>"potentialid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		/*//Backup Activity-Potentials Relation
		$act_q = "select activityid from nectarcrm_seactivityrel where crmid = ?";
		$act_res = $this->db->pquery($act_q, array($id));
		if ($this->db->num_rows($act_res) > 0) {
			for($k=0;$k < $this->db->num_rows($act_res);$k++)
			{
				$act_id = $this->db->query_result($act_res,$k,"activityid");
				$params = array($id, RB_RECORD_DELETED, 'nectarcrm_seactivityrel', 'crmid', 'activityid', $act_id);
				$this->db->pquery("insert into nectarcrm_relatedlists_rb values (?,?,?,?,?,?)", $params);
			}
		}
		$sql = 'delete from nectarcrm_seactivityrel where crmid = ?';
		$this->db->pquery($sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$this->trash($this->module_name, $id);
		} elseif($return_module == 'Campaigns') {
			$sql = 'UPDATE nectarcrm_potential SET campaignid = ? WHERE potentialid = ?';
			$this->db->pquery($sql, array(null, $id));
		} elseif($return_module == 'Products') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Contacts') {
			$sql = 'DELETE FROM nectarcrm_contpotentialrel WHERE potentialid=? AND contactid=?';
			$this->db->pquery($sql, array($id, $return_id));
			
			//If contact related to potential through edit of record,that entry will be present in
			//nectarcrm_potential contact_id column,which should be set to zero
			$sql = 'UPDATE nectarcrm_potential SET contact_id = ? WHERE potentialid=? AND contact_id=?';
			$this->db->pquery($sql, array(0,$id, $return_id));

			// Potential directly linked with Contact (not through Account - nectarcrm_contpotentialrel)
			$directRelCheck = $this->db->pquery('SELECT related_to FROM nectarcrm_potential WHERE potentialid=? AND contact_id=?', array($id, $return_id));
			if($this->db->num_rows($directRelCheck)) {
				$this->trash($this->module_name, $id);
			}
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Contacts') { //When we select contact from potential related list
				$sql = "insert into nectarcrm_contpotentialrel values (?,?)";
				$adb->pquery($sql, array($with_crmid, $crmid));

			} elseif($with_module == 'Products') {//when we select product from potential related list
				$sql = 'INSERT INTO nectarcrm_seproductsrel VALUES(?,?,?,?)';
				$adb->pquery($sql, array($crmid, $with_crmid,'Potentials', 1));

			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}
    
    function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $currentModule;
        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);

        $returnset = '&return_module='.$currentModule.'&return_action=CallRelatedList&return_id='.$id;

		$button = '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT CASE WHEN (nectarcrm_users.user_name NOT LIKE '') THEN $userNameSql ELSE nectarcrm_groups.groupname END AS user_name,
                nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime,
                nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start, nectarcrm_activity.time_start,
                nectarcrm_seactivityrel.crmid as parent_id FROM nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_potential, nectarcrm_users,
                nectarcrm_crmentity LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid WHERE 
                nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid AND 
                nectarcrm_potential.potentialid = nectarcrm_seactivityrel.crmid AND nectarcrm_users.id = nectarcrm_crmentity.smownerid
                AND nectarcrm_crmentity.crmid = nectarcrm_activity.activityid  AND nectarcrm_potential.potentialid = $id AND
                nectarcrm_activity.activitytype = 'Emails' AND nectarcrm_crmentity.deleted = 0";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Invoked when special actions are to be performed on the module.
	 * @param String Module name
	 * @param String Event Type
	 */
	function vtlib_handler($moduleName, $eventType) {
		if ($moduleName == 'Potentials') {
			$db = PearDatabase::getInstance();
			if ($eventType == 'module.disabled') {
				$db->pquery('UPDATE nectarcrm_settings_field SET active=1 WHERE name=?', array($this->LBL_POTENTIAL_MAPPING));
			} else if ($eventType == 'module.enabled') {
				$db->pquery('UPDATE nectarcrm_settings_field SET active=0 WHERE name=?', array($this->LBL_POTENTIAL_MAPPING));
			}
		}
	}
}

?>