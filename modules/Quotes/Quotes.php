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
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
class Quotes extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "nectarcrm_quotes";
	var $table_index= 'quoteid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_quotes','nectarcrm_quotesbillads','nectarcrm_quotesshipads','nectarcrm_quotescf','nectarcrm_inventoryproductrel');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_quotes'=>'quoteid','nectarcrm_quotesbillads'=>'quotebilladdressid','nectarcrm_quotesshipads'=>'quoteshipaddressid','nectarcrm_quotescf'=>'quoteid','nectarcrm_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_quotescf', 'quoteid');
	var $entity_table = "nectarcrm_crmentity";

	var $billadr_table = "nectarcrm_quotesbillads";

	var $object_name = "Quote";

	var $new_schema = true;

	var $column_fields = Array();

	var $sortby_fields = Array('subject','crmid','smownerid','accountname','lastname');

	// This is used to retrieve related nectarcrm_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
				//'Quote No'=>Array('crmentity'=>'crmid'),
				// Module Sequence Numbering
				'Quote No'=>Array('quotes'=>'quote_no'),
				// END
				'Subject'=>Array('quotes'=>'subject'),
				'Quote Stage'=>Array('quotes'=>'quotestage'),
				'Potential Name'=>Array('quotes'=>'potentialid'),
				'Account Name'=>Array('account'=> 'accountid'),
				'Total'=>Array('quotes'=> 'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
				        'Quote No'=>'quote_no',
				        'Subject'=>'subject',
				        'Quote Stage'=>'quotestage',
				        'Potential Name'=>'potential_id',
					'Account Name'=>'account_id',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Quote No'=>Array('quotes'=>'quote_no'),
				'Subject'=>Array('quotes'=>'subject'),
				'Account Name'=>Array('quotes'=>'accountid'),
				'Quote Stage'=>Array('quotes'=>'quotestage'),
				);

	var $search_fields_name = Array(
					'Quote No'=>'quote_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Stage'=>'quotestage',
				      );

	// This is the list of nectarcrm_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('nectarcrm_quotegrouprelation','quoteid');

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id', 'quantity', 'listprice', 'productid');

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

	/**	Constructor which will set the column_fields in this object
	 */
	function Quotes() {
		$this->log =LoggerManager::getLogger('quote');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Quotes');
	}

	function save_module()
	{
		global $adb;

		/* $_REQUEST['REQUEST_FROM_WS'] is set from webservices script.
		 * Depending on $_REQUEST['totalProductCount'] value inserting line items into DB.
		 * This should be done by webservices, not be normal save of Inventory record.
		 * So unsetting the value $_REQUEST['totalProductCount'] through check point
		 */
		if (isset($_REQUEST['REQUEST_FROM_WS']) && $_REQUEST['REQUEST_FROM_WS']) {
			unset($_REQUEST['totalProductCount']);
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'QuotesAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
				&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
				&& $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'Quotes');
		}

		// Update the currency id and the conversion rate for the quotes
		$update_query = "update nectarcrm_quotes set currency_id=?, conversion_rate=? where quoteid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$adb->pquery($update_query, $update_params);
	}

	/**	function used to get the list of sales orders which are related to the Quotes
	 *	@param int $id - quote id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	function get_salesorder($id)
	{
		global $log,$singlepane_view;
		$log->debug("Entering get_salesorder(".$id.") method ...");
		require_once('modules/SalesOrder/SalesOrder.php');
	        $focus = new SalesOrder();

		$button = '';

		if($singlepane_view == 'true')
			$returnset = '&return_module=Quotes&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Quotes&return_action=CallRelatedList&return_id='.$id;

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select nectarcrm_crmentity.*, nectarcrm_salesorder.*, nectarcrm_quotes.subject as quotename
			, nectarcrm_account.accountname,case when (nectarcrm_users.user_name not like '') then
			$userNameSql else nectarcrm_groups.groupname end as user_name
		from nectarcrm_salesorder
		inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_salesorder.salesorderid
		left outer join nectarcrm_quotes on nectarcrm_quotes.quoteid=nectarcrm_salesorder.quoteid
		left outer join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_salesorder.accountid
		left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
        LEFT JOIN nectarcrm_salesordercf ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
        LEFT JOIN nectarcrm_invoice_recurring_info ON nectarcrm_invoice_recurring_info.start_period = nectarcrm_salesorder.salesorderid
		LEFT JOIN nectarcrm_sobillads ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid
		LEFT JOIN nectarcrm_soshipads ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid
		left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
		where nectarcrm_crmentity.deleted=0 and nectarcrm_salesorder.quoteid = ".$id;
		$log->debug("Exiting get_salesorder method ...");
		return GetRelatedList('Quotes','SalesOrder',$focus,$query,$button,$returnset);
	}

	/**	function used to get the list of activities which are related to the Quotes
	 *	@param int $id - quote id
	 *	@return array - return an array which will be returned from the function GetRelatedList
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
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else
		nectarcrm_groups.groupname end as user_name, nectarcrm_contactdetails.contactid,
		nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname, nectarcrm_activity.*,
		nectarcrm_seactivityrel.crmid as parent_id,nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
		nectarcrm_crmentity.modifiedtime,nectarcrm_recurringevents.recurringtype
		from nectarcrm_activity
		inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=
		nectarcrm_activity.activityid
		inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
		left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid=
		nectarcrm_activity.activityid
		left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid =
		nectarcrm_cntactivityrel.contactid
		left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
		left outer join nectarcrm_recurringevents on nectarcrm_recurringevents.activityid=
		nectarcrm_activity.activityid
		left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
		where nectarcrm_seactivityrel.crmid=".$id." and nectarcrm_crmentity.deleted=0 and
			activitytype='Task' and (nectarcrm_activity.status is not NULL and
			nectarcrm_activity.status != 'Completed') and (nectarcrm_activity.status is not NULL and
			nectarcrm_activity.status != 'Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - quote id
	 *	@return array - return an array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.status,
			nectarcrm_activity.eventstatus, nectarcrm_activity.activitytype,nectarcrm_activity.date_start,
			nectarcrm_activity.due_date,nectarcrm_activity.time_start, nectarcrm_activity.time_end,
			nectarcrm_contactdetails.contactid,
			nectarcrm_contactdetails.firstname,nectarcrm_contactdetails.lastname, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description, case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
			from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid
				left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid= nectarcrm_cntactivityrel.contactid
                                left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				where nectarcrm_activity.activitytype='Task'
  				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred')
	 	        	and nectarcrm_seactivityrel.crmid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Quotes',$query,$id);
	}





	/**	Function used to get the Quote Stage history of the Quotes
	 *	@param $id - quote id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_quotestagehistory($id)
	{
		global $log;
		$log->debug("Entering get_quotestagehistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select nectarcrm_quotestagehistory.*, nectarcrm_quotes.quote_no from nectarcrm_quotestagehistory inner join nectarcrm_quotes on nectarcrm_quotes.quoteid = nectarcrm_quotestagehistory.quoteid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_quotes.quoteid where nectarcrm_crmentity.deleted = 0 and nectarcrm_quotes.quoteid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Quote No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['Quote Stage'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Total are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$quotestage_access = (getFieldVisibilityPermission('Quotes', $current_user->id, 'quotestage') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Quotes');

		$quotestage_array = ($quotestage_access != 1)? $picklistarray['quotestage']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($quotestage_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['quoteid'];
			$entries[] = $row['quote_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['quotestage'], $quotestage_array))? $row['quotestage']: $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_quotestagehistory method ...");

		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype='') {
		if ($columname == 'potentialid' || $columname == 'contactid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentityQuotes', array('nectarcrm_usersQuotes', 'nectarcrm_groupsQuotes', 'nectarcrm_lastModifiedByQuotes'));
		$matrix->setDependency('nectarcrm_inventoryproductrelQuotes', array('nectarcrm_productsQuotes', 'nectarcrm_serviceQuotes'));
		
		if (!$queryPlanner->requireTable('nectarcrm_quotes', $matrix)) {
			return '';
		}
        $matrix->setDependency('nectarcrm_quotes',array('nectarcrm_crmentityQuotes', "nectarcrm_currency_info$secmodule",
				'nectarcrm_quotescf', 'nectarcrm_potentialRelQuotes', 'nectarcrm_quotesbillads','nectarcrm_quotesshipads',
				'nectarcrm_inventoryproductrelQuotes', 'nectarcrm_contactdetailsQuotes', 'nectarcrm_accountQuotes',
				'nectarcrm_invoice_recurring_info','nectarcrm_quotesQuotes','nectarcrm_usersRel1'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_quotes","quoteid", $queryPlanner);
		if ($queryPlanner->requireTable("nectarcrm_crmentityQuotes", $matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityQuotes on nectarcrm_crmentityQuotes.crmid=nectarcrm_quotes.quoteid and nectarcrm_crmentityQuotes.deleted=0";
		}
		if ($queryPlanner->requireTable("nectarcrm_quotescf")){
			$query .= " left join nectarcrm_quotescf on nectarcrm_quotes.quoteid = nectarcrm_quotescf.quoteid";
		}
		if ($queryPlanner->requireTable("nectarcrm_quotesbillads")){
			$query .= " left join nectarcrm_quotesbillads on nectarcrm_quotes.quoteid=nectarcrm_quotesbillads.quotebilladdressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_quotesshipads")){
			$query .= " left join nectarcrm_quotesshipads on nectarcrm_quotes.quoteid=nectarcrm_quotesshipads.quoteshipaddressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_currency_info$secmodule")){
			$query .= " left join nectarcrm_currency_info as nectarcrm_currency_info$secmodule on nectarcrm_currency_info$secmodule.id = nectarcrm_quotes.currency_id";
		}
		if ($queryPlanner->requireTable("nectarcrm_inventoryproductrelQuotes",$matrix)){
		}
		if ($queryPlanner->requireTable("nectarcrm_productsQuotes")){
			$query .= " left join nectarcrm_products as nectarcrm_productsQuotes on nectarcrm_productsQuotes.productid = nectarcrm_inventoryproductreltmpQuotes.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_serviceQuotes")){
			$query .= " left join nectarcrm_service as nectarcrm_serviceQuotes on nectarcrm_serviceQuotes.serviceid = nectarcrm_inventoryproductreltmpQuotes.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_groupsQuotes")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsQuotes on nectarcrm_groupsQuotes.groupid = nectarcrm_crmentityQuotes.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_usersQuotes")){
			$query .= " left join nectarcrm_users as nectarcrm_usersQuotes on nectarcrm_usersQuotes.id = nectarcrm_crmentityQuotes.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_usersRel1")){
			$query .= " left join nectarcrm_users as nectarcrm_usersRel1 on nectarcrm_usersRel1.id = nectarcrm_quotes.inventorymanager";
		}
		if ($queryPlanner->requireTable("nectarcrm_potentialRelQuotes")){
			$query .= " left join nectarcrm_potential as nectarcrm_potentialRelQuotes on nectarcrm_potentialRelQuotes.potentialid = nectarcrm_quotes.potentialid";
		}
		if ($queryPlanner->requireTable("nectarcrm_contactdetailsQuotes")){
			$query .= " left join nectarcrm_contactdetails as nectarcrm_contactdetailsQuotes on nectarcrm_contactdetailsQuotes.contactid = nectarcrm_quotes.contactid";
		}
		if ($queryPlanner->requireTable("nectarcrm_accountQuotes")){
			$query .= " left join nectarcrm_account as nectarcrm_accountQuotes on nectarcrm_accountQuotes.accountid = nectarcrm_quotes.accountid";
		}
		if ($queryPlanner->requireTable("nectarcrm_lastModifiedByQuotes")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByQuotes on nectarcrm_lastModifiedByQuotes.id = nectarcrm_crmentityQuotes.modifiedby ";
		}
        if ($queryPlanner->requireTable("nectarcrm_createdbyQuotes")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyQuotes on nectarcrm_createdbyQuotes.id = nectarcrm_crmentityQuotes.smcreatorid ";
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
			"SalesOrder" =>array("nectarcrm_salesorder"=>array("quoteid","salesorderid"),"nectarcrm_quotes"=>"quoteid"),
			"Calendar" =>array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_quotes"=>"quoteid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_quotes"=>"quoteid"),
			"Accounts" => array("nectarcrm_quotes"=>array("quoteid","accountid")),
			"Contacts" => array("nectarcrm_quotes"=>array("quoteid","contactid")),
			"Potentials" => array("nectarcrm_quotes"=>array("quoteid","potentialid")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts' ) {
			$this->trash('Quotes',$id);
		} elseif($return_module == 'Potentials') {
			$relation_query = 'UPDATE nectarcrm_quotes SET potentialid=? WHERE quoteid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} elseif($return_module == 'Contacts') {
			$relation_query = 'UPDATE nectarcrm_quotes SET contactid=? WHERE quoteid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } elseif($return_module == 'Leads'){
            $relation_query = 'UPDATE nectarcrm_quotes SET contactid=? WHERE quoteid=?';
            $this->db->pquery($relation_query, array(null, $id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function insertIntoEntityTable($table_name, $module, $fileid = '')  {
		//Ignore relation table insertions while saving of the record
		if($table_name == 'nectarcrm_inventoryproductrel') {
			return;
		}
		parent::insertIntoEntityTable($table_name, $module, $fileid);
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		$createRecords = createRecords($obj);
		return $createRecords;
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		$entityInfo = importRecord($obj, $inventoryFieldData, $lineItemDetails);
		return $entityInfo;
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		$statusCount = getImportStatusCount($obj);
		return $statusCount;
	}

	function undoLastImport($obj, $user) {
		$undoLastImport = undoLastImport($obj, $user);
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Quotes Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Quotes", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN nectarcrm_quotes ON nectarcrm_quotes.quoteid = nectarcrm_crmentity.crmid
				LEFT JOIN nectarcrm_quotescf ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_quotesbillads ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_quotesshipads ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_inventoryproductrel ON nectarcrm_inventoryproductrel.id = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_products ON nectarcrm_products.productid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_service ON nectarcrm_service.serviceid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_quotes.contactid
				LEFT JOIN nectarcrm_potential ON nectarcrm_potential.potentialid = nectarcrm_quotes.potentialid
				LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_quotes.accountid
				LEFT JOIN nectarcrm_currency_info ON nectarcrm_currency_info.id = nectarcrm_quotes.currency_id
				LEFT JOIN nectarcrm_users AS nectarcrm_inventoryManager ON nectarcrm_inventoryManager.id = nectarcrm_quotes.inventorymanager
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Quotes',$current_user);
		$where_auto = " nectarcrm_crmentity.deleted=0";

		if($where != "") {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= " where ".$where_auto;
		}

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/**
	 * Function to get importable mandatory fields
	 * By default some fields like Quantity, List Price is not mandaroty for Invertory modules but
	 * import fails if those fields are not mapped during import.
	 */
	function getMandatoryImportableFields() {
		return getInventoryImportableMandatoryFeilds($this->moduleName);
	}
}

?>
