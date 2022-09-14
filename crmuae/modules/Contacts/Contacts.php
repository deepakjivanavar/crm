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
 * $Header: /advent/projects/wesat/nectarcrm_crm/sugarcrm/modules/Contacts/Contacts.php,v 1.70 2005/04/27 11:21:49 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
// Contact is used to store customer information.
class Contacts extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "nectarcrm_contactdetails";
	var $table_index= 'contactid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_contactdetails','nectarcrm_contactaddress','nectarcrm_contactsubdetails','nectarcrm_contactscf','nectarcrm_customerdetails');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_contactdetails'=>'contactid','nectarcrm_contactaddress'=>'contactaddressid','nectarcrm_contactsubdetails'=>'contactsubscriptionid','nectarcrm_contactscf'=>'contactid','nectarcrm_customerdetails'=>'customerid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_contactscf', 'contactid');

	var $column_fields = Array();

	var $sortby_fields = Array('lastname','firstname','title','email','phone','smownerid','accountname');

	var $list_link_field= 'lastname';

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
	'First Name' => Array('contactdetails'=>'firstname'),
	'Last Name' => Array('contactdetails'=>'lastname'),
	'Title' => Array('contactdetails'=>'title'),
	'Account Name' => Array('account'=>'accountid'),
	'Email' => Array('contactdetails'=>'email'),
	'Office Phone' => Array('contactdetails'=>'phone'),
	'Assigned To' => Array('crmentity'=>'smownerid')
	);

	var $range_fields = Array(
		'first_name',
		'last_name',
		'primary_address_city',
		'account_name',
		'account_id',
		'id',
		'email1',
		'salutation',
		'title',
		'phone_mobile',
		'reports_to_name',
		'primary_address_street',
		'primary_address_city',
		'primary_address_state',
		'primary_address_postalcode',
		'primary_address_country',
		'alt_address_city',
		'alt_address_street',
		'alt_address_city',
		'alt_address_state',
		'alt_address_postalcode',
		'alt_address_country',
		'office_phone',
		'home_phone',
		'other_phone',
		'fax',
		'department',
		'birthdate',
		'assistant_name',
		'assistant_phone');


	var $list_fields_name = Array(
	'First Name' => 'firstname',
	'Last Name' => 'lastname',
	'Title' => 'title',
	'Account Name' => 'account_id',
	'Email' => 'email',
	'Office Phone' => 'phone',
	'Assigned To' => 'assigned_user_id'
	);

	var $search_fields = Array(
	'First Name' => Array('contactdetails'=>'firstname'),
	'Last Name' => Array('contactdetails'=>'lastname'),
	'Title' => Array('contactdetails'=>'title'),
	'Account Name'=>Array('contactdetails'=>'account_id'),
	'Assigned To'=>Array('crmentity'=>'smownerid'),
		);

	var $search_fields_name = Array(
	'First Name' => 'firstname',
	'Last Name' => 'lastname',
	'Title' => 'title',
	'Account Name'=>'account_id',
	'Assigned To'=>'assigned_user_id'
	);

	// This is the list of nectarcrm_fields that are required
	var $required_fields =  array("lastname"=>1);

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id','lastname','createdtime' ,'modifiedtime');

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array('firstname','lastname','salutation','title','email','department','phone','mobile','support_start_date','support_end_date');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'lastname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'lastname';

	var $related_module_table_index = array(
		'Potentials' => array('table_name' => 'nectarcrm_potential', 'table_index' => 'potentialid', 'rel_index' => 'contact_id'),
		'Quotes' => array('table_name' => 'nectarcrm_quotes', 'table_index' => 'quoteid', 'rel_index' => 'contactid'),
		'SalesOrder' => array('table_name' => 'nectarcrm_salesorder', 'table_index' => 'salesorderid', 'rel_index' => 'contactid'),
		'PurchaseOrder' => array('table_name' => 'nectarcrm_purchaseorder', 'table_index' => 'purchaseorderid', 'rel_index' => 'contactid'),
		'Invoice' => array('table_name' => 'nectarcrm_invoice', 'table_index' => 'invoiceid', 'rel_index' => 'contactid'),
		'HelpDesk' => array('table_name' => 'nectarcrm_troubletickets', 'table_index' => 'ticketid', 'rel_index' => 'contact_id'),
		'Products' => array('table_name' => 'nectarcrm_seproductsrel', 'table_index' => 'productid', 'rel_index' => 'crmid'),
		'Calendar' => array('table_name' => 'nectarcrm_cntactivityrel', 'table_index' => 'activityid', 'rel_index' => 'contactid'),
		'Documents' => array('table_name' => 'nectarcrm_senotesrel', 'table_index' => 'notesid', 'rel_index' => 'crmid'),
		'ServiceContracts' => array('table_name' => 'nectarcrm_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),
		'Services' => array('table_name' => 'nectarcrm_crmentityrel', 'table_index' => 'crmid', 'rel_index' => 'crmid'),
		'Campaigns' => array('table_name' => 'nectarcrm_campaigncontrel', 'table_index' => 'campaignid', 'rel_index' => 'contactid'),
		'Assets' => array('table_name' => 'nectarcrm_assets', 'table_index' => 'assetsid', 'rel_index' => 'contact'),
		'Project' => array('table_name' => 'nectarcrm_project', 'table_index' => 'projectid', 'rel_index' => 'linktoaccountscontacts'),
		'Emails' => array('table_name' => 'nectarcrm_seactivityrel', 'table_index' => 'crmid', 'rel_index' => 'activityid'),
        'Vendors' => array('table_name' => 'nectarcrm_vendorcontactrel', 'table_index' => 'vendorid', 'rel_index' => 'contactid'),
	);

	function Contacts() {
		$this->log = LoggerManager::getLogger('contact');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Contacts');
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/** Function to get the number of Contacts assigned to a particular User.
	*  @param varchar $user name - Assigned to User
	*  Returns the count of contacts assigned to user.
	*/
	function getCount($user_name)
	{
		global $log;
		$log->debug("Entering getCount(".$user_name.") method ...");
		$query = "select count(*) from nectarcrm_contactdetails  inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_contactdetails.contactid inner join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid where user_name=? and nectarcrm_crmentity.deleted=0";
		$result = $this->db->pquery($query,array($user_name),true,"Error retrieving contacts count");
		$rows_found =  $this->db->getRowCount($result);
		$row = $this->db->fetchByAssoc($result, 0);


		$log->debug("Exiting getCount method ...");
		return $row["count(*)"];
	}

	// This function doesn't seem to be used anywhere. Need to check and remove it.
	/** Function to get the Contact Details assigned to a particular User based on the starting count and the number of subsequent records.
	*  @param varchar $user_name - Assigned User
	*  @param integer $from_index - Initial record number to be displayed
	*  @param integer $offset - Count of the subsequent records to be displayed.
	*  Returns Query.
	*/
    function get_contacts($user_name,$from_index,$offset)
    {
	global $log;
	$log->debug("Entering get_contacts(".$user_name.",".$from_index.",".$offset.") method ...");
      $query = "select nectarcrm_users.user_name,nectarcrm_groups.groupname,nectarcrm_contactdetails.department department, nectarcrm_contactdetails.phone office_phone, nectarcrm_contactdetails.fax fax, nectarcrm_contactsubdetails.assistant assistant_name, nectarcrm_contactsubdetails.otherphone other_phone, nectarcrm_contactsubdetails.homephone home_phone,nectarcrm_contactsubdetails.birthday birthdate, nectarcrm_contactdetails.lastname last_name,nectarcrm_contactdetails.firstname first_name,nectarcrm_contactdetails.contactid as id, nectarcrm_contactdetails.salutation as salutation, nectarcrm_contactdetails.email as email1,nectarcrm_contactdetails.title as title,nectarcrm_contactdetails.mobile as phone_mobile,nectarcrm_account.accountname as account_name,nectarcrm_account.accountid as account_id, nectarcrm_contactaddress.mailingcity as primary_address_city,nectarcrm_contactaddress.mailingstreet as primary_address_street, nectarcrm_contactaddress.mailingcountry as primary_address_country,nectarcrm_contactaddress.mailingstate as primary_address_state, nectarcrm_contactaddress.mailingzip as primary_address_postalcode,   nectarcrm_contactaddress.othercity as alt_address_city,nectarcrm_contactaddress.otherstreet as alt_address_street, nectarcrm_contactaddress.othercountry as alt_address_country,nectarcrm_contactaddress.otherstate as alt_address_state, nectarcrm_contactaddress.otherzip as alt_address_postalcode  from nectarcrm_contactdetails inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_contactdetails.contactid inner join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid left join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_contactdetails.accountid left join nectarcrm_contactaddress on nectarcrm_contactaddress.contactaddressid=nectarcrm_contactdetails.contactid left join nectarcrm_contactsubdetails on nectarcrm_contactsubdetails.contactsubscriptionid = nectarcrm_contactdetails.contactid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid left join nectarcrm_users on nectarcrm_crmentity.smownerid=nectarcrm_users.id where user_name='" .$user_name ."' and nectarcrm_crmentity.deleted=0 limit " .$from_index ."," .$offset;

	$log->debug("Exiting get_contacts method ...");
      return $this->process_list_query1($query);
    }


    /** Function to process list query for a given query
    *  @param $query
    *  Returns the results of query in array format
    */
    function process_list_query1($query)
    {
	global $log;
	$log->debug("Entering process_list_query1(".$query.") method ...");

        $result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
        $list = Array();
        $rows_found =  $this->db->getRowCount($result);
        if($rows_found != 0)
        {
		   $contact = Array();
               for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))

             {
                foreach($this->range_fields as $columnName)
                {
                    if (isset($row[$columnName])) {

                        $contact[$columnName] = $row[$columnName];
                    }
                    else
                    {
                            $contact[$columnName] = "";
                    }
	     }
// TODO OPTIMIZE THE QUERY ACCOUNT NAME AND ID are set separetly for every nectarcrm_contactdetails and hence
// nectarcrm_account query goes for ecery single nectarcrm_account row

                    $list[] = $contact;
                }
        }

        $response = Array();
        $response['list'] = $list;
        $response['row_count'] = $rows_found;
        $response['next_offset'] = $next_offset;
        $response['previous_offset'] = $previous_offset;


	$log->debug("Exiting process_list_query1 method ...");
        return $response;
    }


    /** Function to process list query for Plugin with Security Parameters for a given query
    *  @param $query
    *  Returns the results of query in array format
    */
    function plugin_process_list_query($query)
    {
          global $log,$adb,$current_user;
          $log->debug("Entering process_list_query1(".$query.") method ...");
          $permitted_field_lists = Array();
          require('user_privileges/user_privileges_'.$current_user->id.'.php');
          if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
          {
              $sql1 = "select columnname from nectarcrm_field where tabid=4 and block <> 75 and nectarcrm_field.presence in (0,2)";
			  $params1 = array();
          }else
          {
              $profileList = getCurrentUserProfileList();
              $sql1 = "select columnname from nectarcrm_field inner join nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=4 and nectarcrm_field.block <> 6 and nectarcrm_field.block <> 75 and nectarcrm_field.displaytype in (1,2,4,3) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
			  $params1 = array();
			  if (count($profileList) > 0) {
			  	 $sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			  	 array_push($params1, $profileList);
			  }
          }
          $result1 = $this->db->pquery($sql1, $params1);
          for($i=0;$i < $adb->num_rows($result1);$i++)
          {
              $permitted_field_lists[] = $adb->query_result($result1,$i,'columnname');
          }

          $result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
          $list = Array();
          $rows_found =  $this->db->getRowCount($result);
          if($rows_found != 0)
          {
              for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
              {
                  $contact = Array();

		  $contact[lastname] = in_array("lastname",$permitted_field_lists) ? $row[lastname] : "";
		  $contact[firstname] = in_array("firstname",$permitted_field_lists)? $row[firstname] : "";
		  $contact[email] = in_array("email",$permitted_field_lists) ? $row[email] : "";


                  if(in_array("accountid",$permitted_field_lists))
                  {
                      $contact[accountname] = $row[accountname];
                      $contact[account_id] = $row[accountid];
                  }else
		  {
                      $contact[accountname] = "";
                      $contact[account_id] = "";
		  }
                  $contact[contactid] =  $row[contactid];
                  $list[] = $contact;
              }
          }

          $response = Array();
          $response['list'] = $list;
          $response['row_count'] = $rows_found;
          $response['next_offset'] = $next_offset;
          $response['previous_offset'] = $previous_offset;
          $log->debug("Exiting process_list_query1 method ...");
          return $response;
    }


	/** Returns a list of the associated opportunities
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_opportunities(".$id.") method ...");
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
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_action.value=\"updateRelations\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		// Should Opportunities be listed on Secondary Contacts ignoring the boundaries of Organization.
		// Useful when the Reseller are working to gain Potential for other Organization.
		$ignoreOrganizationCheck = true;

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query ='select case when (nectarcrm_users.user_name not like "") then '.$userNameSql.' else nectarcrm_groups.groupname end as user_name,
		nectarcrm_contactdetails.accountid, nectarcrm_contactdetails.contactid , nectarcrm_potential.potentialid, nectarcrm_potential.potentialname,
		nectarcrm_potential.potentialtype, nectarcrm_potential.sales_stage, nectarcrm_potential.amount, nectarcrm_potential.closingdate,
		nectarcrm_potential.related_to, nectarcrm_potential.contact_id, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_account.accountname
		from nectarcrm_contactdetails
		left join nectarcrm_contpotentialrel on nectarcrm_contpotentialrel.contactid=nectarcrm_contactdetails.contactid
		left join nectarcrm_potential on (nectarcrm_potential.potentialid = nectarcrm_contpotentialrel.potentialid or nectarcrm_potential.contact_id=nectarcrm_contactdetails.contactid)
		inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid
		left join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_contactdetails.accountid
		LEFT JOIN nectarcrm_potentialscf ON nectarcrm_potential.potentialid = nectarcrm_potentialscf.potentialid
		left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
		left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
		where  nectarcrm_crmentity.deleted=0 and nectarcrm_contactdetails.contactid ='.$id;

		if (!$ignoreOrganizationCheck) {
			// Restrict the scope of listing to only related contacts of the organization linked to potential via related_to of Potential
			$query .= ' and (nectarcrm_contactdetails.accountid = nectarcrm_potential.related_to or nectarcrm_contactdetails.contactid=nectarcrm_potential.contact_id)';
		}

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}


	/** Returns a list of the associated tasks
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
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
				if(getFieldVisibilityPermission('Events',$current_user->id,'contact_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name," .
				" nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname,  nectarcrm_activity.activityid ," .
				" nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_activity.date_start, nectarcrm_activity.due_date," .
				" nectarcrm_activity.time_start,nectarcrm_activity.time_end, nectarcrm_cntactivityrel.contactid, nectarcrm_crmentity.crmid," .
				" nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime, nectarcrm_recurringevents.recurringtype," .
				" case when (nectarcrm_activity.activitytype = 'Task') then nectarcrm_activity.status else nectarcrm_activity.eventstatus end as status, " .
				" nectarcrm_seactivityrel.crmid as parent_id " .
				" from nectarcrm_contactdetails " .
				" inner join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.contactid = nectarcrm_contactdetails.contactid" .
				" inner join nectarcrm_activity on nectarcrm_cntactivityrel.activityid=nectarcrm_activity.activityid" .
				" inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_cntactivityrel.activityid " .
				" left join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid = nectarcrm_cntactivityrel.activityid " .
				" left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid" .
				" left outer join nectarcrm_recurringevents on nectarcrm_recurringevents.activityid=nectarcrm_activity.activityid" .
				" left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid" .
				" where nectarcrm_contactdetails.contactid=".$id." and nectarcrm_crmentity.deleted = 0" .
						" and ((nectarcrm_activity.activitytype='Task' and nectarcrm_activity.status not in ('Completed','Deferred'))" .
						" or (nectarcrm_activity.activitytype Not in ('Emails','Task') and  nectarcrm_activity.eventstatus not in ('','Held')))";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}
	/**
	* Function to get Contact related Task & Event which have activity type Held, Completed or Deferred.
	* @param  integer   $id      - contactid
	* returns related Task or Event record in array format
	*/
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.status
			, nectarcrm_activity.eventstatus,nectarcrm_activity.activitytype, nectarcrm_activity.date_start,
			nectarcrm_activity.due_date,nectarcrm_activity.time_start,nectarcrm_activity.time_end,
			nectarcrm_contactdetails.contactid, nectarcrm_contactdetails.firstname,
			nectarcrm_contactdetails.lastname, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description,nectarcrm_crmentity.crmid,
			case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				from nectarcrm_activity
				inner join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid
				inner join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid= nectarcrm_cntactivityrel.contactid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
                left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				where (nectarcrm_activity.activitytype != 'Emails')
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred' or (nectarcrm_activity.eventstatus = 'Held' and nectarcrm_activity.eventstatus != ''))
				and nectarcrm_cntactivityrel.contactid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('Contacts',$query,$id);
	}
	/**
	* Function to get Contact related Tickets.
	* @param  integer   $id      - contactid
	* returns related Ticket records in array format
	*/
	function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_tickets(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id', 'readwrite') == '0') {
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
				nectarcrm_crmentity.crmid, nectarcrm_troubletickets.title, nectarcrm_contactdetails.contactid, nectarcrm_troubletickets.parent_id,
				nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.lastname, nectarcrm_troubletickets.status, nectarcrm_troubletickets.priority,
				nectarcrm_crmentity.smownerid, nectarcrm_troubletickets.ticket_no, nectarcrm_troubletickets.contact_id
				from nectarcrm_troubletickets inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_troubletickets.ticketid
				left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid=nectarcrm_troubletickets.contact_id
				LEFT JOIN nectarcrm_ticketcf ON nectarcrm_troubletickets.ticketid = nectarcrm_ticketcf.ticketid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				where nectarcrm_crmentity.deleted=0 and nectarcrm_contactdetails.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_tickets method ...");
		return $return_value;
	}
    
    /**
	  * Function to get Contact related Quotes
	  * @param  integer   $id  - contactid
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
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
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.*, nectarcrm_quotes.*,nectarcrm_potential.potentialname,nectarcrm_contactdetails.lastname,nectarcrm_account.accountname from nectarcrm_quotes inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_quotes.quoteid left outer join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid=nectarcrm_quotes.contactid left outer join nectarcrm_potential on nectarcrm_potential.potentialid=nectarcrm_quotes.potentialid  left join nectarcrm_account on nectarcrm_account.accountid = nectarcrm_quotes.accountid LEFT JOIN nectarcrm_quotescf ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid LEFT JOIN nectarcrm_quotesbillads ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid LEFT JOIN nectarcrm_quotesshipads ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid where nectarcrm_crmentity.deleted=0 and nectarcrm_contactdetails.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	  }
	/**
	 * Function to get Contact related SalesOrder
 	 * @param  integer   $id  - contactid
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
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
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.*, nectarcrm_salesorder.*, nectarcrm_quotes.subject as quotename, nectarcrm_account.accountname, nectarcrm_contactdetails.lastname from nectarcrm_salesorder inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_salesorder.salesorderid LEFT JOIN nectarcrm_salesordercf ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid LEFT JOIN nectarcrm_sobillads ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid LEFT JOIN nectarcrm_soshipads ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid left outer join nectarcrm_quotes on nectarcrm_quotes.quoteid=nectarcrm_salesorder.quoteid left outer join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_salesorder.accountid LEFT JOIN nectarcrm_invoice_recurring_info ON nectarcrm_invoice_recurring_info.start_period = nectarcrm_salesorder.salesorderid left outer join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid=nectarcrm_salesorder.contactid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid where nectarcrm_crmentity.deleted=0  and  nectarcrm_salesorder.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
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

		$query = 'SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode,
		 		  nectarcrm_products.commissionrate, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price,
				  nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,nectarcrm_contactdetails.lastname
				FROM nectarcrm_products
				INNER JOIN nectarcrm_seproductsrel
					ON nectarcrm_seproductsrel.productid=nectarcrm_products.productid and nectarcrm_seproductsrel.setype="Contacts"
				INNER JOIN nectarcrm_productcf
					ON nectarcrm_products.productid = nectarcrm_productcf.productid
				INNER JOIN nectarcrm_crmentity
					ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
				INNER JOIN nectarcrm_contactdetails
					ON nectarcrm_contactdetails.contactid = nectarcrm_seproductsrel.crmid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups
					ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			   WHERE nectarcrm_contactdetails.contactid = '.$id.' and nectarcrm_crmentity.deleted = 0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	 }

	/**
	 * Function to get Contact related PurchaseOrder
 	 * @param  integer   $id  - contactid
	 * returns related PurchaseOrder record in array format
	 */
	 function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_purchase_orders(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
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
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.*, nectarcrm_purchaseorder.*,nectarcrm_vendor.vendorname,nectarcrm_contactdetails.lastname from nectarcrm_purchaseorder inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_purchaseorder.purchaseorderid left outer join nectarcrm_vendor on nectarcrm_purchaseorder.vendorid=nectarcrm_vendor.vendorid left outer join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid=nectarcrm_purchaseorder.contactid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid LEFT JOIN nectarcrm_purchaseordercf ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid LEFT JOIN nectarcrm_pobillads ON nectarcrm_pobillads.pobilladdressid = nectarcrm_purchaseorder.purchaseorderid LEFT JOIN nectarcrm_poshipads ON nectarcrm_poshipads.poshipaddressid = nectarcrm_purchaseorder.purchaseorderid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid where nectarcrm_crmentity.deleted=0 and nectarcrm_purchaseorder.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_purchase_orders method ...");
		return $return_value;
	 }

	/** Returns a list of the associated emails
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
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
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}
        
        $relatedIds = array_merge(array($id), $this->getRelatedPotentialIds($id), $this->getRelatedTicketIds($id));
        $relatedIds = implode(', ', $relatedIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name," .
				" nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime," .
				" nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start, nectarcrm_activity.time_start, nectarcrm_seactivityrel.crmid as parent_id " .
				" from nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_contactdetails, nectarcrm_users, nectarcrm_crmentity" .
				" left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid" .
				" where nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid" .
				" and nectarcrm_seactivityrel.crmid IN ($relatedIds) and nectarcrm_users.id=nectarcrm_crmentity.smownerid" .
				" and nectarcrm_crmentity.crmid = nectarcrm_activity.activityid  and nectarcrm_contactdetails.contactid = ".$id." and" .
						" nectarcrm_activity.activitytype='Emails' and nectarcrm_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
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
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
					nectarcrm_campaign.campaignid, nectarcrm_campaign.campaignname, nectarcrm_campaign.campaigntype, nectarcrm_campaign.campaignstatus,
					nectarcrm_campaign.expectedrevenue, nectarcrm_campaign.closingdate, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
					nectarcrm_crmentity.modifiedtime from nectarcrm_campaign
					inner join nectarcrm_campaigncontrel on nectarcrm_campaigncontrel.campaignid=nectarcrm_campaign.campaignid
					inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_campaign.campaignid
					inner join nectarcrm_campaignscf ON nectarcrm_campaignscf.campaignid = nectarcrm_campaign.campaignid
					left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
					left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid
					where nectarcrm_campaigncontrel.contactid=".$id." and nectarcrm_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}

	/**
	* Function to get Contact related Invoices
	* @param  integer   $id      - contactid
	* returns related Invoices record in array format
	*/
	function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_invoices(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
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
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
			nectarcrm_crmentity.*,
			nectarcrm_invoice.*,
			nectarcrm_contactdetails.lastname,nectarcrm_contactdetails.firstname,
			nectarcrm_salesorder.subject AS salessubject
			FROM nectarcrm_invoice
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_invoice.invoiceid
			LEFT OUTER JOIN nectarcrm_contactdetails
				ON nectarcrm_contactdetails.contactid = nectarcrm_invoice.contactid
			LEFT OUTER JOIN nectarcrm_salesorder
				ON nectarcrm_salesorder.salesorderid = nectarcrm_invoice.salesorderid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
            LEFT JOIN nectarcrm_invoicecf
                ON nectarcrm_invoicecf.invoiceid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoicebillads
				ON nectarcrm_invoicebillads.invoicebilladdressid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoiceshipads
				ON nectarcrm_invoiceshipads.invoiceshipaddressid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_contactdetails.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_invoices method ...");
		return $return_value;
	}

    /**
	* Function to get Contact related vendors.
	* @param  integer   $id      - contactid
	* returns related vendor records in array format
	*/
	function get_vendors($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_vendors(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id', 'readwrite') == '0') {
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
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_crmentity.crmid, nectarcrm_vendor.*,  nectarcrm_vendorcf.*
				from nectarcrm_vendor inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_vendor.vendorid
                INNER JOIN nectarcrm_vendorcontactrel on nectarcrm_vendorcontactrel.vendorid=nectarcrm_vendor.vendorid
				LEFT JOIN nectarcrm_vendorcf on nectarcrm_vendorcf.vendorid=nectarcrm_vendor.vendorid
				LEFT JOIN nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted=0 and nectarcrm_vendorcontactrel.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_vendors method ...");
		return $return_value;
	}

	/** Function to export the contact records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Contacts Query.
	*/
        function create_export_query($where)
        {
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Contacts", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT nectarcrm_contactdetails.salutation as 'Salutation',$fields_list,case when (nectarcrm_users.user_name not like '') then nectarcrm_users.user_name else nectarcrm_groups.groupname end as user_name
                                FROM nectarcrm_contactdetails
                                inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_contactdetails.contactid
                                LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid=nectarcrm_users.id and nectarcrm_users.status='Active'
                                LEFT JOIN nectarcrm_account on nectarcrm_contactdetails.accountid=nectarcrm_account.accountid
				left join nectarcrm_contactaddress on nectarcrm_contactaddress.contactaddressid=nectarcrm_contactdetails.contactid
				left join nectarcrm_contactsubdetails on nectarcrm_contactsubdetails.contactsubscriptionid=nectarcrm_contactdetails.contactid
			        left join nectarcrm_contactscf on nectarcrm_contactscf.contactid=nectarcrm_contactdetails.contactid
			        left join nectarcrm_customerdetails on nectarcrm_customerdetails.customerid=nectarcrm_contactdetails.contactid
	                        LEFT JOIN nectarcrm_groups
                        	        ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_contactdetails nectarcrm_contactdetails2
					ON nectarcrm_contactdetails2.contactid = nectarcrm_contactdetails.reportsto";
		$query .= getNonAdminAccessControlQuery('Contacts',$current_user);
		$where_auto = " nectarcrm_crmentity.deleted = 0 ";

                if($where != "")
                   $query .= "  WHERE ($where) AND ".$where_auto;
                else
                   $query .= "  WHERE ".$where_auto;

		$log->info("Export Query Constructed Successfully");
		$log->debug("Exiting create_export_query method ...");
		return $query;
        }


/** Function to get the Columnnames of the Contacts
* Used By nectarcrmCRM Word Plugin
* Returns the Merge Fields for Word Plugin
*/
function getColumnNames()
{
	global $log, $current_user;
	$log->debug("Entering getColumnNames() method ...");
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
	 $sql1 = "select fieldlabel from nectarcrm_field where tabid=4 and block <> 75 and nectarcrm_field.presence in (0,2)";
	 $params1 = array();
	}else
	{
	 $profileList = getCurrentUserProfileList();
	 $sql1 = "select nectarcrm_field.fieldid,fieldlabel from nectarcrm_field inner join nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=4 and nectarcrm_field.block <> 75 and nectarcrm_field.displaytype in (1,2,4,3) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
	 $params1 = array();
	 if (count($profileList) > 0) {
	 	$sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by fieldid";
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
	$log->debug("Exiting getColumnNames method ...");
	return $mergeflds;
}
//End
/** Function to get the Contacts assigned to a user with a valid email address.
* @param varchar $username - User Name
* @param varchar $emailaddress - Email Addr for each contact.
* Used By nectarcrmCRM Outlook Plugin
* Returns the Query
*/
function get_searchbyemailid($username,$emailaddress)
{
	global $log;
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($username);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$log->debug("Entering get_searchbyemailid(".$username.",".$emailaddress.") method ...");
	$query = "select nectarcrm_contactdetails.lastname,nectarcrm_contactdetails.firstname,
					nectarcrm_contactdetails.contactid, nectarcrm_contactdetails.salutation,
					nectarcrm_contactdetails.email,nectarcrm_contactdetails.title,
					nectarcrm_contactdetails.mobile,nectarcrm_account.accountname,
					nectarcrm_account.accountid as accountid  from nectarcrm_contactdetails
						inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_contactdetails.contactid
						inner join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
						left join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_contactdetails.accountid
						left join nectarcrm_contactaddress on nectarcrm_contactaddress.contactaddressid=nectarcrm_contactdetails.contactid
			      LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
	$query .= getNonAdminAccessControlQuery('Contacts',$current_user);
	$query .= "where nectarcrm_crmentity.deleted=0";
	if(trim($emailaddress) != '') {
		$query .= " and ((nectarcrm_contactdetails.email like '". formatForSqlLike($emailaddress) .
		"') or nectarcrm_contactdetails.lastname REGEXP REPLACE('".$emailaddress.
		"',' ','|') or nectarcrm_contactdetails.firstname REGEXP REPLACE('".$emailaddress.
		"',' ','|'))  and nectarcrm_contactdetails.email != ''";
	} else {
		$query .= " and (nectarcrm_contactdetails.email like '". formatForSqlLike($emailaddress) .
		"' and nectarcrm_contactdetails.email != '')";
	}

	$log->debug("Exiting get_searchbyemailid method ...");
	return $this->plugin_process_list_query($query);
}

/** Function to get the Contacts associated with the particular User Name.
*  @param varchar $user_name - User Name
*  Returns query
*/

function get_contactsforol($user_name)
{
	global $log,$adb;
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
  {
    $sql1 = "select tablename,columnname from nectarcrm_field where tabid=4 and nectarcrm_field.presence in (0,2)";
	$params1 = array();
  }else
  {
    $profileList = getCurrentUserProfileList();
    $sql1 = "select tablename,columnname from nectarcrm_field inner join nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=4 and nectarcrm_field.displaytype in (1,2,4,3) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
	$params1 = array();
	if (count($profileList) > 0) {
		$sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
		array_push($params1, $profileList);
	}
  }
  $result1 = $adb->pquery($sql1, $params1);
  for($i=0;$i < $adb->num_rows($result1);$i++)
  {
      $permitted_lists[] = $adb->query_result($result1,$i,'tablename');
      $permitted_lists[] = $adb->query_result($result1,$i,'columnname');
      if($adb->query_result($result1,$i,'columnname') == "accountid")
      {
        $permitted_lists[] = 'nectarcrm_account';
        $permitted_lists[] = 'accountname';
      }
  }
	$permitted_lists = array_chunk($permitted_lists,2);
	$column_table_lists = array();
	for($i=0;$i < count($permitted_lists);$i++)
	{
	   $column_table_lists[] = implode(".",$permitted_lists[$i]);
  }

	$log->debug("Entering get_contactsforol(".$user_name.") method ...");
	$query = "select nectarcrm_contactdetails.contactid as id, ".implode(',',$column_table_lists)." from nectarcrm_contactdetails
						inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_contactdetails.contactid
						inner join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
						left join nectarcrm_customerdetails on nectarcrm_customerdetails.customerid=nectarcrm_contactdetails.contactid
						left join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_contactdetails.accountid
						left join nectarcrm_contactaddress on nectarcrm_contactaddress.contactaddressid=nectarcrm_contactdetails.contactid
						left join nectarcrm_contactsubdetails on nectarcrm_contactsubdetails.contactsubscriptionid = nectarcrm_contactdetails.contactid
                        left join nectarcrm_campaigncontrel on nectarcrm_contactdetails.contactid = nectarcrm_campaigncontrel.contactid
                        left join nectarcrm_campaignrelstatus on nectarcrm_campaignrelstatus.campaignrelstatusid = nectarcrm_campaigncontrel.campaignrelstatusid
			      LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
						where nectarcrm_crmentity.deleted=0 and nectarcrm_users.user_name='".$user_name."'";
  $log->debug("Exiting get_contactsforol method ...");
	return $query;
}


	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module)
	{
		// now handling in the crmentity for uitype 69
		//$this->insertIntoAttachment($this->id,$module);
	}

	/**
	 *      This function is used to add the nectarcrm_attachments. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 *      @param int $id  - entity id to which the nectarcrm_files to be uploaded
	 *      @param string $module  - the current module name
	*/
	function insertIntoAttachment($id,$module)
	{
		global $log, $adb,$upload_badext;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;
		//This is to added to store the existing attachment id of the contact where we should delete this when we give new image
		$old_attachmentid = $adb->query_result($adb->pquery("select nectarcrm_crmentity.crmid from nectarcrm_seattachmentsrel inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_seattachmentsrel.attachmentsid where  nectarcrm_seattachmentsrel.crmid=?", array($id)),0,'crmid');
		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		$imageNameSql = 'SELECT name FROM nectarcrm_seattachmentsrel INNER JOIN nectarcrm_attachments ON
								nectarcrm_seattachmentsrel.attachmentsid = nectarcrm_attachments.attachmentsid LEFT JOIN nectarcrm_contactdetails ON
								nectarcrm_contactdetails.contactid = nectarcrm_seattachmentsrel.crmid WHERE nectarcrm_seattachmentsrel.crmid = ?';
		$imageNameResult = $adb->pquery($imageNameSql,array($id));
		$imageName = decode_html($adb->query_result($imageNameResult, 0, "name"));

		//Inserting image information of record into base table
		$adb->pquery('UPDATE nectarcrm_contactdetails SET imagename = ? WHERE contactid = ?',array($imageName,$id));

		//This is to handle the delete image for contacts
		if($module == 'Contacts' && $file_saved)
		{
			if($old_attachmentid != '')
			{
				$setype = $adb->query_result($adb->pquery("select setype from nectarcrm_crmentity where crmid=?", array($old_attachmentid)),0,'setype');
				if($setype == 'Contacts Image')
				{
					$del_res1 = $adb->pquery("delete from nectarcrm_attachments where attachmentsid=?", array($old_attachmentid));
					$del_res2 = $adb->pquery("delete from nectarcrm_seattachmentsrel where attachmentsid=?", array($old_attachmentid));
				}
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
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

		$rel_table_arr = Array("Potentials"=>"nectarcrm_contpotentialrel","Potentials"=>"nectarcrm_potential","Activities"=>"nectarcrm_cntactivityrel",
				"Emails"=>"nectarcrm_seactivityrel","HelpDesk"=>"nectarcrm_troubletickets","Quotes"=>"nectarcrm_quotes","PurchaseOrder"=>"nectarcrm_purchaseorder",
				"SalesOrder"=>"nectarcrm_salesorder","Products"=>"nectarcrm_seproductsrel","Documents"=>"nectarcrm_senotesrel",
				"Attachments"=>"nectarcrm_seattachmentsrel","Campaigns"=>"nectarcrm_campaigncontrel",'Invoice'=>'nectarcrm_invoice',
                'ServiceContracts'=>'nectarcrm_servicecontracts','Project'=>'nectarcrm_project','Assets'=>'nectarcrm_assets',
				'Vendors'=>'nectarcrm_vendorcontactrel');

		$tbl_field_arr = Array("nectarcrm_contpotentialrel"=>"potentialid","nectarcrm_potential"=>"potentialid","nectarcrm_cntactivityrel"=>"activityid",
				"nectarcrm_seactivityrel"=>"activityid","nectarcrm_troubletickets"=>"ticketid","nectarcrm_quotes"=>"quoteid","nectarcrm_purchaseorder"=>"purchaseorderid",
				"nectarcrm_salesorder"=>"salesorderid","nectarcrm_seproductsrel"=>"productid","nectarcrm_senotesrel"=>"notesid",
				"nectarcrm_seattachmentsrel"=>"attachmentsid","nectarcrm_campaigncontrel"=>"campaignid",'nectarcrm_invoice'=>'invoiceid',
                'nectarcrm_servicecontracts'=>'servicecontractsid','nectarcrm_project'=>'projectid','nectarcrm_assets'=>'assetsid',
				'nectarcrm_vendorcontactrel'=>'vendorid');

		$entity_tbl_field_arr = Array("nectarcrm_contpotentialrel"=>"contactid","nectarcrm_potential"=>"contact_id","nectarcrm_cntactivityrel"=>"contactid",
				"nectarcrm_seactivityrel"=>"crmid","nectarcrm_troubletickets"=>"contact_id","nectarcrm_quotes"=>"contactid","nectarcrm_purchaseorder"=>"contactid",
				"nectarcrm_salesorder"=>"contactid","nectarcrm_seproductsrel"=>"crmid","nectarcrm_senotesrel"=>"crmid",
				"nectarcrm_seattachmentsrel"=>"crmid","nectarcrm_campaigncontrel"=>"contactid",'nectarcrm_invoice'=>'contactid',
                'nectarcrm_servicecontracts'=>'sc_related_to','nectarcrm_project'=>'linktoaccountscontacts','nectarcrm_assets'=>'contact',
				'nectarcrm_vendorcontactrel'=>'contactid');

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
                $relModuleModel = nectarcrm_Module::getInstance($rel_module);
				if($relModuleModel) {
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
			$adb->pquery("UPDATE nectarcrm_potential SET related_to = ? WHERE related_to = ?", array($entityId, $transferId));
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
		$matrix->setDependency('nectarcrm_crmentityContacts',array('nectarcrm_groupsContacts','nectarcrm_usersContacts','nectarcrm_lastModifiedByContacts'));
		
		if (!$queryplanner->requireTable('nectarcrm_contactdetails', $matrix)) {
			return '';
		}

        $matrix->setDependency('nectarcrm_contactdetails', array('nectarcrm_crmentityContacts','nectarcrm_contactaddress',
								'nectarcrm_customerdetails','nectarcrm_contactsubdetails','nectarcrm_contactscf'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_contactdetails","contactid", $queryplanner);

		if ($queryplanner->requireTable("nectarcrm_crmentityContacts",$matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityContacts on nectarcrm_crmentityContacts.crmid = nectarcrm_contactdetails.contactid  and nectarcrm_crmentityContacts.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_contactdetailsContacts")){
			$query .= " left join nectarcrm_contactdetails as nectarcrm_contactdetailsContacts on nectarcrm_contactdetailsContacts.contactid = nectarcrm_contactdetails.reportsto";
		}
		if ($queryplanner->requireTable("nectarcrm_contactaddress")){
			$query .= " left join nectarcrm_contactaddress on nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid";
		}
		if ($queryplanner->requireTable("nectarcrm_customerdetails")){
			$query .= " left join nectarcrm_customerdetails on nectarcrm_customerdetails.customerid = nectarcrm_contactdetails.contactid";
		}
		if ($queryplanner->requireTable("nectarcrm_contactsubdetails")){
			$query .= " left join nectarcrm_contactsubdetails on nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid";
		}
		if ($queryplanner->requireTable("nectarcrm_accountContacts")){
			$query .= " left join nectarcrm_account as nectarcrm_accountContacts on nectarcrm_accountContacts.accountid = nectarcrm_contactdetails.accountid";
		}
		if ($queryplanner->requireTable("nectarcrm_contactscf")){
			$query .= " left join nectarcrm_contactscf on nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid";
		}
		if ($queryplanner->requireTable("nectarcrm_email_trackContacts")){
			$query .= " LEFT JOIN nectarcrm_email_track AS nectarcrm_email_trackContacts ON nectarcrm_email_trackContacts.crmid = nectarcrm_contactdetails.contactid";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsContacts")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsContacts on nectarcrm_groupsContacts.groupid = nectarcrm_crmentityContacts.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersContacts")){
			$query .= " left join nectarcrm_users as nectarcrm_usersContacts on nectarcrm_usersContacts.id = nectarcrm_crmentityContacts.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByContacts")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByContacts on nectarcrm_lastModifiedByContacts.id = nectarcrm_crmentityContacts.modifiedby ";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyContacts")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyContacts on nectarcrm_createdbyContacts.id = nectarcrm_crmentityContacts.smcreatorid ";
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
			"Calendar" => array("nectarcrm_cntactivityrel"=>array("contactid","activityid"),"nectarcrm_contactdetails"=>"contactid"),
			"HelpDesk" => array("nectarcrm_troubletickets"=>array("contact_id","ticketid"),"nectarcrm_contactdetails"=>"contactid"),
			"Quotes" => array("nectarcrm_quotes"=>array("contactid","quoteid"),"nectarcrm_contactdetails"=>"contactid"),
			"PurchaseOrder" => array("nectarcrm_purchaseorder"=>array("contactid","purchaseorderid"),"nectarcrm_contactdetails"=>"contactid"),
			"SalesOrder" => array("nectarcrm_salesorder"=>array("contactid","salesorderid"),"nectarcrm_contactdetails"=>"contactid"),
			"Products" => array("nectarcrm_seproductsrel"=>array("crmid","productid"),"nectarcrm_contactdetails"=>"contactid"),
			"Campaigns" => array("nectarcrm_campaigncontrel"=>array("contactid","campaignid"),"nectarcrm_contactdetails"=>"contactid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_contactdetails"=>"contactid"),
			"Accounts" => array("nectarcrm_contactdetails"=>array("contactid","accountid")),
			"Invoice" => array("nectarcrm_invoice"=>array("contactid","invoiceid"),"nectarcrm_contactdetails"=>"contactid"),
			"Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_contactdetails"=>"contactid"),
			"Vendors" =>array("nectarcrm_vendorcontactrel"=>array("contactid","vendorid"),"nectarcrm_contactdetails"=>"contactid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;

		//Deleting Contact related Potentials.
		$pot_q = 'SELECT nectarcrm_crmentity.crmid FROM nectarcrm_crmentity
			INNER JOIN nectarcrm_potential ON nectarcrm_crmentity.crmid=nectarcrm_potential.potentialid
			LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid=nectarcrm_potential.related_to
			WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_potential.related_to=?';
		$pot_res = $this->db->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for($k=0;$k < $this->db->num_rows($pot_res);$k++)
		{
			$pot_id = $this->db->query_result($pot_res,$k,"crmid");
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE nectarcrm_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($pot_id));
		}
		//Backup deleted Contact related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_crmentity', 'deleted', 'crmid', implode(",", $pot_ids_list));
		$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Trouble Tickets Relation
		$tkt_q = 'SELECT ticketid FROM nectarcrm_troubletickets WHERE contact_id=?';
		$tkt_res = $this->db->pquery($tkt_q, array($id));
		if ($this->db->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for($k=0;$k < $this->db->num_rows($tkt_res);$k++)
			{
				$tkt_ids_list[] = $this->db->query_result($tkt_res,$k,"ticketid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_troubletickets', 'contact_id', 'ticketid', implode(",", $tkt_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Trouble Tickets
		$this->db->pquery('UPDATE nectarcrm_troubletickets SET contact_id=0 WHERE contact_id=?', array($id));

		//Backup Contact-PurchaseOrder Relation
		$po_q = 'SELECT purchaseorderid FROM nectarcrm_purchaseorder WHERE contactid=?';
		$po_res = $this->db->pquery($po_q, array($id));
		if ($this->db->num_rows($po_res) > 0) {
			$po_ids_list = array();
			for($k=0;$k < $this->db->num_rows($po_res);$k++)
			{
				$po_ids_list[] = $this->db->query_result($po_res,$k,"purchaseorderid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_purchaseorder', 'contactid', 'purchaseorderid', implode(",", $po_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with PurchaseOrder
		$this->db->pquery('UPDATE nectarcrm_purchaseorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-SalesOrder Relation
		$so_q = 'SELECT salesorderid FROM nectarcrm_salesorder WHERE contactid=?';
		$so_res = $this->db->pquery($so_q, array($id));
		if ($this->db->num_rows($so_res) > 0) {
			$so_ids_list = array();
			for($k=0;$k < $this->db->num_rows($so_res);$k++)
			{
				$so_ids_list[] = $this->db->query_result($so_res,$k,"salesorderid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_salesorder', 'contactid', 'salesorderid', implode(",", $so_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with SalesOrder
		$this->db->pquery('UPDATE nectarcrm_salesorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-Quotes Relation
		$quo_q = 'SELECT quoteid FROM nectarcrm_quotes WHERE contactid=?';
		$quo_res = $this->db->pquery($quo_q, array($id));
		if ($this->db->num_rows($quo_res) > 0) {
			$quo_ids_list = array();
			for($k=0;$k < $this->db->num_rows($quo_res);$k++)
			{
				$quo_ids_list[] = $this->db->query_result($quo_res,$k,"quoteid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_quotes', 'contactid', 'quoteid', implode(",", $quo_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Quotes
		$this->db->pquery('UPDATE nectarcrm_quotes SET contactid=0 WHERE contactid=?', array($id));
		//remove the portal info the contact
		$this->db->pquery('DELETE FROM nectarcrm_portalinfo WHERE id = ?', array($id));
		$this->db->pquery('UPDATE nectarcrm_customerdetails SET portal=0,support_start_date=NULL,support_end_date=NULl WHERE customerid=?', array($id));
		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$sql = 'UPDATE nectarcrm_contactdetails SET accountid = ? WHERE contactid = ?';
			$this->db->pquery($sql, array(null, $id));
		} elseif($return_module == 'Potentials') {
			$sql = 'DELETE FROM nectarcrm_contpotentialrel WHERE contactid=? AND potentialid=?';
			$this->db->pquery($sql, array($id, $return_id));
			
			//If contact related to potential through edit of record,that entry will be present in
			//nectarcrm_potential contact_id column,which should be set to zero
			$sql = 'UPDATE nectarcrm_potential SET contact_id = ? WHERE contact_id=? AND potentialid=?';
			$this->db->pquery($sql, array(0,$id, $return_id));
		} elseif($return_module == 'Campaigns') {
			$sql = 'DELETE FROM nectarcrm_campaigncontrel WHERE contactid=? AND campaignid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Products') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Vendors') {
			$sql = 'DELETE FROM nectarcrm_vendorcontactrel WHERE vendorid=? AND contactid=?';
			$this->db->pquery($sql, array($return_id, $id));
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	//added to get mail info for portal user
	//type argument included when when addin customizable tempalte for sending portal login details
	public static function getPortalEmailContents($entityData, $password, $type='') {
        require_once 'config.inc.php';
		global $PORTAL_URL, $HELPDESK_SUPPORT_EMAIL_ID;

		$adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();

		$companyDetails = getCompanyDetails();

		$portalURL = vtranslate('Please ',$moduleName).'<a href="'.$PORTAL_URL.'" style="font-family:Arial, Helvetica, sans-serif;font-size:13px;">'.  vtranslate('click here', $moduleName).'</a>';

		//here id is hardcoded with 5. it is for support start notification in nectarcrm_notificationscheduler
		$query='SELECT nectarcrm_emailtemplates.subject,nectarcrm_emailtemplates.body
					FROM nectarcrm_notificationscheduler
						INNER JOIN nectarcrm_emailtemplates ON nectarcrm_emailtemplates.templateid=nectarcrm_notificationscheduler.notificationbody
					WHERE schedulednotificationid=5';

		$result = $adb->pquery($query, array());
		$body=decode_html($adb->query_result($result,0,'body'));
		$contents=$body;
		$contents = str_replace('$contact_name$',$entityData->get('firstname')." ".$entityData->get('lastname'),$contents);
		$contents = str_replace('$login_name$',$entityData->get('email'),$contents);
		$contents = str_replace('$password$',$password,$contents);
		$contents = str_replace('$URL$',$portalURL,$contents);
		$contents = str_replace('$support_team$',getTranslatedString('Support Team', $moduleName),$contents);
		$contents = str_replace('$logo$','<img src="cid:logo" />',$contents);

		//Company Details
		$contents = str_replace('$address$',$companyDetails['address'],$contents);
		$contents = str_replace('$companyname$',$companyDetails['companyname'],$contents);
		$contents = str_replace('$phone$',$companyDetails['phone'],$contents);
		$contents = str_replace('$companywebsite$',$companyDetails['website'],$contents);
		$contents = str_replace('$supportemail$',$HELPDESK_SUPPORT_EMAIL_ID,$contents);

		if($type == "LoginDetails") {
			$temp=$contents;
			$value["subject"]=decode_html($adb->query_result($result,0,'subject'));
			$value["body"]=$temp;
			return $value;
		}
		return $contents;
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Products') {
				$adb->pquery('INSERT INTO nectarcrm_seproductsrel VALUES(?,?,?,?)', array($crmid, $with_crmid, 'Contacts', 1));

			} elseif($with_module == 'Campaigns') {
				$adb->pquery("insert into nectarcrm_campaigncontrel values(?,?,1)", array($with_crmid, $crmid));

			} elseif($with_module == 'Potentials') {
				$adb->pquery("insert into nectarcrm_contpotentialrel values(?,?)", array($crmid, $with_crmid));

			}
            else if($with_module == 'Vendors'){
        		$adb->pquery("insert into nectarcrm_vendorcontactrel values (?,?)", array($with_crmid,$crmid));
            }else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	function getListButtons($app_strings,$mod_strings = false) {
		$list_buttons = Array();

		if(isPermitted('Contacts','Delete','') == 'yes') {
			$list_buttons['del'] = $app_strings[LBL_MASS_DELETE];
		}
		if(isPermitted('Contacts','EditView','') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
			$list_buttons['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
		}
		if(isPermitted('Emails','EditView','') == 'yes'){
			$list_buttons['s_mail'] = $app_strings[LBL_SEND_MAIL_BUTTON];
		}
		return $list_buttons;
	}
    
    function getRelatedPotentialIds($id) {
        $relatedIds = array();
        $db = PearDatabase::getInstance();
        $query = "SELECT DISTINCT nectarcrm_crmentity.crmid FROM nectarcrm_contactdetails LEFT JOIN nectarcrm_contpotentialrel ON 
            nectarcrm_contpotentialrel.contactid = nectarcrm_contactdetails.contactid LEFT JOIN nectarcrm_potential ON 
            (nectarcrm_potential.potentialid = nectarcrm_contpotentialrel.potentialid OR nectarcrm_potential.contact_id = 
            nectarcrm_contactdetails.contactid) INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid 
            WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_contactdetails.contactid = ?";
        $result = $db->pquery($query, array($id));
		for ($i = 0; $i < $db->num_rows($result); $i++) {
            $relatedIds[] = $db->query_result($result, $i, 'crmid');
        }
        return $relatedIds;
    }
    
    function getRelatedTicketIds($id) {
        $relatedIds = array();
        $db = PearDatabase::getInstance();
        $query = "SELECT DISTINCT nectarcrm_crmentity.crmid FROM nectarcrm_troubletickets INNER JOIN nectarcrm_crmentity ON 
            nectarcrm_crmentity.crmid = nectarcrm_troubletickets.ticketid LEFT JOIN nectarcrm_contactdetails ON 
            nectarcrm_contactdetails.contactid = nectarcrm_troubletickets.contact_id WHERE nectarcrm_crmentity.deleted = 0 AND 
            nectarcrm_contactdetails.contactid = ?";
        $result = $db->pquery($query, array($id));
		for ($i = 0; $i < $db->num_rows($result); $i++) {
            $relatedIds[] = $db->query_result($result, $i, 'crmid');
        }
        return $relatedIds;
    }

}

?>