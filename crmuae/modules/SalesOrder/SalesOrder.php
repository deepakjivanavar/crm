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
class SalesOrder extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "nectarcrm_salesorder";
	var $table_index= 'salesorderid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_salesorder','nectarcrm_sobillads','nectarcrm_soshipads','nectarcrm_salesordercf','nectarcrm_invoice_recurring_info','nectarcrm_inventoryproductrel');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_salesorder'=>'salesorderid','nectarcrm_sobillads'=>'sobilladdressid','nectarcrm_soshipads'=>'soshipaddressid','nectarcrm_salesordercf'=>'salesorderid','nectarcrm_invoice_recurring_info'=>'salesorderid','nectarcrm_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_salesordercf', 'salesorderid');
	var $entity_table = "nectarcrm_crmentity";

	var $billadr_table = "nectarcrm_sobillads";

	var $object_name = "SalesOrder";

	var $new_schema = true;

	var $update_product_array = Array();

	var $column_fields = Array();

	var $sortby_fields = Array('subject','smownerid','accountname','lastname');

	// This is used to retrieve related nectarcrm_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
				// Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),
				'Order No'=>Array('salesorder','salesorder_no'),
				// END
				'Subject'=>Array('salesorder'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				'Quote Name'=>Array('quotes'=>'quoteid'),
				'Total'=>Array('salesorder'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
				        'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Order No'=>Array('salesorder'=>'salesorder_no'),
				'Subject'=>Array('salesorder'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				'Quote Name'=>Array('salesorder'=>'quoteid')
				);

	var $search_fields_name = Array(
					'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id'
				      );

	// This is the list of nectarcrm_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('nectarcrm_sogrouprelation','salesorderid');

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id','quantity', 'listprice', 'productid');

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function SalesOrder() {
		$this->log =LoggerManager::getLogger('SalesOrder');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('SalesOrder');
	}

	function save_module($module)
	{
		/* $_REQUEST['REQUEST_FROM_WS'] is set from webservices script.
		 * Depending on $_REQUEST['totalProductCount'] value inserting line items into DB.
		 * This should be done by webservices, not be normal save of Inventory record.
		 * So unsetting the value $_REQUEST['totalProductCount'] through check point
		 */
		if (isset($_REQUEST['REQUEST_FROM_WS']) && $_REQUEST['REQUEST_FROM_WS']) {
			unset($_REQUEST['totalProductCount']);
		}


		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'SalesOrderAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
				&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
				&& $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'SalesOrder');
		}

		// Update the currency id and the conversion rate for the sales order
		$update_query = "update nectarcrm_salesorder set currency_id=?, conversion_rate=? where salesorderid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params);
	}

	/** Function to get activities associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedActivities() method
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
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.contactid, nectarcrm_activity.*,nectarcrm_seactivityrel.crmid as parent_id,nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime from nectarcrm_activity inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid where nectarcrm_seactivityrel.crmid=".$id." and activitytype='Task' and nectarcrm_crmentity.deleted=0 and (nectarcrm_activity.status is not NULL and nectarcrm_activity.status != 'Completed') and (nectarcrm_activity.status is not NULL and nectarcrm_activity.status !='Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/** Function to get the activities history associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedHistory() method
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname,
			nectarcrm_contactdetails.contactid,nectarcrm_activity.*, nectarcrm_seactivityrel.*,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description, case when
			(nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname
			end as user_name from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid
				left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
                                left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
			where activitytype='Task'
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred')
				and nectarcrm_seactivityrel.crmid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('SalesOrder',$query,$id);
	}



	/** Function to get the invoices associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedInvoices() method.
	 */
	function get_invoices($id)
	{
		global $log,$singlepane_view;
		$log->debug("Entering get_invoices(".$id.") method ...");
		require_once('modules/Invoice/Invoice.php');

		$focus = new Invoice();

		$button = '';
		if($singlepane_view == 'true')
			$returnset = '&return_module=SalesOrder&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=SalesOrder&return_action=CallRelatedList&return_id='.$id;

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select nectarcrm_crmentity.*, nectarcrm_invoice.*, nectarcrm_account.accountname,
			nectarcrm_salesorder.subject as salessubject, case when
			(nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname
			end as user_name from nectarcrm_invoice
			inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_invoice.invoiceid
			left outer join nectarcrm_account on nectarcrm_account.accountid=nectarcrm_invoice.accountid
			inner join nectarcrm_salesorder on nectarcrm_salesorder.salesorderid=nectarcrm_invoice.salesorderid
            LEFT JOIN nectarcrm_invoicecf ON nectarcrm_invoicecf.invoiceid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoicebillads ON nectarcrm_invoicebillads.invoicebilladdressid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoiceshipads ON nectarcrm_invoiceshipads.invoiceshipaddressid = nectarcrm_invoice.invoiceid
			left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
			left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
			where nectarcrm_crmentity.deleted=0 and nectarcrm_salesorder.salesorderid=".$id;

		$log->debug("Exiting get_invoices method ...");
		return GetRelatedList('SalesOrder','Invoice',$focus,$query,$button,$returnset);

	}

	/**	Function used to get the Status history of the Sales Order
	 *	@param $id - salesorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_sostatushistory($id)
	{
		global $log;
		$log->debug("Entering get_sostatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select nectarcrm_sostatushistory.*, nectarcrm_salesorder.salesorder_no from nectarcrm_sostatushistory inner join nectarcrm_salesorder on nectarcrm_salesorder.salesorderid = nectarcrm_sostatushistory.salesorderid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_salesorder.salesorderid where nectarcrm_crmentity.deleted = 0 and nectarcrm_salesorder.salesorderid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Order No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SO_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Total are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$sostatus_access = (getFieldVisibilityPermission('SalesOrder', $current_user->id, 'sostatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('SalesOrder');

		$sostatus_array = ($sostatus_access != 1)? $picklistarray['sostatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($sostatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['salesorderid'];
			$entries[] = $row['salesorder_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['sostatus'], $sostatus_array))? $row['sostatus']: $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_sostatushistory method ...");

		return $return_data;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentitySalesOrder', array('nectarcrm_usersSalesOrder', 'nectarcrm_groupsSalesOrder', 'nectarcrm_lastModifiedBySalesOrder'));
		$matrix->setDependency('nectarcrm_inventoryproductrelSalesOrder', array('nectarcrm_productsSalesOrder', 'nectarcrm_serviceSalesOrder'));
		if (!$queryPlanner->requireTable('nectarcrm_salesorder', $matrix)) {
			return '';
		}
        $matrix->setDependency('nectarcrm_salesorder',array('nectarcrm_crmentitySalesOrder', "nectarcrm_currency_info$secmodule",
				'nectarcrm_salesordercf', 'nectarcrm_potentialRelSalesOrder', 'nectarcrm_sobillads','nectarcrm_soshipads',
				'nectarcrm_inventoryproductrelSalesOrder', 'nectarcrm_contactdetailsSalesOrder', 'nectarcrm_accountSalesOrder',
				'nectarcrm_invoice_recurring_info','nectarcrm_quotesSalesOrder'));


		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_salesorder","salesorderid", $queryPlanner);
		if ($queryPlanner->requireTable("nectarcrm_crmentitySalesOrder",$matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentitySalesOrder on nectarcrm_crmentitySalesOrder.crmid=nectarcrm_salesorder.salesorderid and nectarcrm_crmentitySalesOrder.deleted=0";
		}
		if ($queryPlanner->requireTable("nectarcrm_salesordercf")){
			$query .= " left join nectarcrm_salesordercf on nectarcrm_salesorder.salesorderid = nectarcrm_salesordercf.salesorderid";
		}
		if ($queryPlanner->requireTable("nectarcrm_sobillads")){
			$query .= " left join nectarcrm_sobillads on nectarcrm_salesorder.salesorderid=nectarcrm_sobillads.sobilladdressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_soshipads")){
			$query .= " left join nectarcrm_soshipads on nectarcrm_salesorder.salesorderid=nectarcrm_soshipads.soshipaddressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_currency_info$secmodule")){
			$query .= " left join nectarcrm_currency_info as nectarcrm_currency_info$secmodule on nectarcrm_currency_info$secmodule.id = nectarcrm_salesorder.currency_id";
		}
		if ($queryPlanner->requireTable("nectarcrm_inventoryproductrelSalesOrder", $matrix)){
		}
		if ($queryPlanner->requireTable("nectarcrm_productsSalesOrder")){
			$query .= " left join nectarcrm_products as nectarcrm_productsSalesOrder on nectarcrm_productsSalesOrder.productid = nectarcrm_inventoryproductreltmpSalesOrder.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_serviceSalesOrder")){
			$query .= " left join nectarcrm_service as nectarcrm_serviceSalesOrder on nectarcrm_serviceSalesOrder.serviceid = nectarcrm_inventoryproductreltmpSalesOrder.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_groupsSalesOrder")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsSalesOrder on nectarcrm_groupsSalesOrder.groupid = nectarcrm_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_usersSalesOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_usersSalesOrder on nectarcrm_usersSalesOrder.id = nectarcrm_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_potentialRelSalesOrder")){
			$query .= " left join nectarcrm_potential as nectarcrm_potentialRelSalesOrder on nectarcrm_potentialRelSalesOrder.potentialid = nectarcrm_salesorder.potentialid";
		}
		if ($queryPlanner->requireTable("nectarcrm_contactdetailsSalesOrder")){
			$query .= " left join nectarcrm_contactdetails as nectarcrm_contactdetailsSalesOrder on nectarcrm_salesorder.contactid = nectarcrm_contactdetailsSalesOrder.contactid";
		}
		if ($queryPlanner->requireTable("nectarcrm_invoice_recurring_info")){
			$query .= " left join nectarcrm_invoice_recurring_info on nectarcrm_salesorder.salesorderid = nectarcrm_invoice_recurring_info.salesorderid";
		}
		if ($queryPlanner->requireTable("nectarcrm_quotesSalesOrder")){
			$query .= " left join nectarcrm_quotes as nectarcrm_quotesSalesOrder on nectarcrm_salesorder.quoteid = nectarcrm_quotesSalesOrder.quoteid";
		}
		if ($queryPlanner->requireTable("nectarcrm_accountSalesOrder")){
			$query .= " left join nectarcrm_account as nectarcrm_accountSalesOrder on nectarcrm_accountSalesOrder.accountid = nectarcrm_salesorder.accountid";
		}
		if ($queryPlanner->requireTable("nectarcrm_lastModifiedBySalesOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedBySalesOrder on nectarcrm_lastModifiedBySalesOrder.id = nectarcrm_crmentitySalesOrder.modifiedby ";
		}
		if ($queryPlanner->requireTable("nectarcrm_createdbySalesOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbySalesOrder on nectarcrm_createdbySalesOrder.id = nectarcrm_crmentitySalesOrder.smcreatorid ";
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
			"Calendar" =>array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_salesorder"=>"salesorderid"),
			"Invoice" =>array("nectarcrm_invoice"=>array("salesorderid","invoiceid"),"nectarcrm_salesorder"=>"salesorderid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_salesorder"=>"salesorderid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$this->trash('SalesOrder',$id);
		}
		elseif($return_module == 'Quotes') {
			$relation_query = 'UPDATE nectarcrm_salesorder SET quoteid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Potentials') {
			$relation_query = 'UPDATE nectarcrm_salesorder SET potentialid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Contacts') {
			$relation_query = 'UPDATE nectarcrm_salesorder SET contactid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function getJoinClause($tableName) {
		if ($tableName == 'nectarcrm_invoice_recurring_info') {
			return 'LEFT JOIN';
		}
		return parent::getJoinClause($tableName);
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
	* Returns Export SalesOrder Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("SalesOrder", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN nectarcrm_salesorder ON nectarcrm_salesorder.salesorderid = nectarcrm_crmentity.crmid
				LEFT JOIN nectarcrm_salesordercf ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_sobillads ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_soshipads ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_inventoryproductrel ON nectarcrm_inventoryproductrel.id = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_products ON nectarcrm_products.productid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_service ON nectarcrm_service.serviceid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_salesorder.contactid
				LEFT JOIN nectarcrm_invoice_recurring_info ON nectarcrm_invoice_recurring_info.salesorderid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_potential ON nectarcrm_potential.potentialid = nectarcrm_salesorder.potentialid
				LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_salesorder.accountid
				LEFT JOIN nectarcrm_currency_info ON nectarcrm_currency_info.id = nectarcrm_salesorder.currency_id
				LEFT JOIN nectarcrm_quotes ON nectarcrm_quotes.quoteid = nectarcrm_salesorder.quoteid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('SalesOrder',$current_user);
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
	 * Function which will give the basic query to find duplicates
	 * @param <String> $module
	 * @param <String> $tableColumns
	 * @param <String> $selectedColumns
	 * @param <Boolean> $ignoreEmpty
     * @param <Array> $requiredTables 
	 * @return string
	 */
	// Note : remove getDuplicatesQuery API once nectarcrm5 code is removed
    function getQueryForDuplicates($module, $tableColumns, $selectedColumns = '', $ignoreEmpty = false,$requiredTables = array()) {
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
				if($tableName != 'nectarcrm_crmentity' && $tableName != $this->table_name && $tableName != 'nectarcrm_inventoryproductrel' && in_array($tableName,$requiredTables)) {
                    if($tableName == 'nectarcrm_invoice_recurring_info') {
						$fromClause .= " LEFT JOIN " . $tableName . " ON " . $tableName . '.' . $this->tab_name_index[$tableName] .
							" = $this->table_name.$this->table_index";
					}elseif($this->tab_name_index[$tableName]) {
						$fromClause .= " INNER JOIN " . $tableName . " ON " . $tableName . '.' . $this->tab_name_index[$tableName] .
							" = $this->table_name.$this->table_index";
					}
				}
			}
		}
        $fromClause .= " LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
						LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";

        $whereClause = " WHERE nectarcrm_crmentity.deleted = 0";
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
	 * Function to get importable mandatory fields
	 * By default some fields like Quantity, List Price is not mandaroty for Invertory modules but
	 * import fails if those fields are not mapped during import.
	 */
	function getMandatoryImportableFields() {
		return getInventoryImportableMandatoryFeilds($this->moduleName);
	}
}

?>
