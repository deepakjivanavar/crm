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
class PurchaseOrder extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "nectarcrm_purchaseorder";
	var $table_index= 'purchaseorderid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_purchaseorder','nectarcrm_pobillads','nectarcrm_poshipads','nectarcrm_purchaseordercf','nectarcrm_inventoryproductrel');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_purchaseorder'=>'purchaseorderid','nectarcrm_pobillads'=>'pobilladdressid','nectarcrm_poshipads'=>'poshipaddressid','nectarcrm_purchaseordercf'=>'purchaseorderid','nectarcrm_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_purchaseordercf', 'purchaseorderid');
	var $entity_table = "nectarcrm_crmentity";

	var $billadr_table = "nectarcrm_pobillads";

	var $column_fields = Array();

	var $sortby_fields = Array('subject','tracking_no','smownerid','lastname');

	// This is used to retrieve related nectarcrm_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
				//  Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),
				'Order No'=>Array('purchaseorder'=>'purchaseorder_no'),
				// END
				'Subject'=>Array('purchaseorder'=>'subject'),
				'Vendor Name'=>Array('purchaseorder'=>'vendorid'),
				'Tracking Number'=>Array('purchaseorder'=> 'tracking_no'),
				'Total'=>Array('purchaseorder'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
				        'Order No'=>'purchaseorder_no',
				        'Subject'=>'subject',
				        'Vendor Name'=>'vendor_id',
					'Tracking Number'=>'tracking_no',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Order No'=>Array('purchaseorder'=>'purchaseorder_no'),
				'Subject'=>Array('purchaseorder'=>'subject'),
				);

	var $search_fields_name = Array(
				        'Order No'=>'purchaseorder_no',
				        'Subject'=>'subject',
				      );
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('subject', 'vendor_id','createdtime' ,'modifiedtime', 'assigned_user_id', 'quantity', 'listprice', 'productid');

	// This is the list of nectarcrm_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

	//var $groupTable = Array('nectarcrm_pogrouprelation','purchaseorderid');
	/** Constructor Function for Order class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of Order class.
	 */
	function PurchaseOrder() {
		$this->log =LoggerManager::getLogger('PurchaseOrder');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('PurchaseOrder');
	}

	function save_module($module)
	{
		global $adb, $updateInventoryProductRel_deduct_stock;
		$updateInventoryProductRel_deduct_stock = false;

		$requestProductIdsList = $requestQuantitiesList = array();
		$totalNoOfProducts = $_REQUEST['totalProductCount'];
		for($i=1; $i<=$totalNoOfProducts; $i++) {
			$productId = $_REQUEST['hdnProductId'.$i];
			$requestProductIdsList[$productId] = $productId;
			//Checking same item more than once
			if(array_key_exists($productId, $requestQuantitiesList)) {
				$requestQuantitiesList[$productId] = $requestQuantitiesList[$productId] + $_REQUEST['qty'.$i];
				continue;
			}
			$requestQuantitiesList[$productId] = $_REQUEST['qty'.$i];
		}

		global $itemQuantitiesList, $isItemsRequest;
		$itemQuantitiesList = array();
		$statusValue = $this->column_fields['postatus'];

		if ($totalNoOfProducts) {
			$isItemsRequest = true;
		}

		if ($this->mode == '' && $statusValue === 'Received Shipment') {
			$itemQuantitiesList['new'] = $requestQuantitiesList;

		} else if ($this->mode != '' && in_array($statusValue, array('Received Shipment', 'Cancelled'))) {

			$productIdsList = $quantitiesList = array();
			$recordId = $this->id;
			$result = $adb->pquery("SELECT productid, quantity FROM nectarcrm_inventoryproductrel WHERE id = ?", array($recordId));
			$numOfRows = $adb->num_rows($result);
			for ($i=0; $i<$numOfRows; $i++) {
				$productId = $adb->query_result($result, $i, 'productid');
				$productIdsList[$productId] = $productId;
				if(array_key_exists($productId, $quantitiesList)) {
					$quantitiesList[$productId] = $quantitiesList[$productId] + $adb->query_result($result, $i, 'quantity');
					continue;
				}
				$qty = $adb->query_result($result, $i, 'quantity');
				$quantitiesList[$productId] = $qty;
				$subProductQtys = $this->getSubProductsQty($productId);
				if ($statusValue === 'Cancelled' && !empty($subProductQtys)) {
					foreach ($subProductQtys as $subProdId => $subProdQty) {
						$subProdQty = $subProdQty * $qty;
						if (array_key_exists($subProdId, $quantitiesList)) {
							$quantitiesList[$subProdId] = $quantitiesList[$subProdId] + $subProdQty;
							continue;
						}
						$quantitiesList[$subProdId] = $subProdQty;
					}
				}
			}
				
			if ($statusValue === 'Cancelled') {
				$itemQuantitiesList = $quantitiesList;
			} else {

				//Constructing quantities array for newly added line items
				$newProductIds = array_diff($requestProductIdsList, $productIdsList);
				if ($newProductIds) {
					$newQuantitiesList = array();
					foreach ($newProductIds as $productId) {
						$newQuantitiesList[$productId] = $requestQuantitiesList[$productId];
					}
					if ($newQuantitiesList) {
						$itemQuantitiesList['new'] = $newQuantitiesList;
					}
				}

				//Constructing quantities array for deleted line items
				$deletedProductIds = array_diff($productIdsList, $requestProductIdsList);
				if ($deletedProductIds && $totalNoOfProducts) {//$totalNoOfProducts is exist means its not ajax save
					$deletedQuantitiesList = array();
					foreach ($deletedProductIds as $productId) {
						//Checking same item more than once
						if(array_key_exists($productId, $deletedQuantitiesList)) {
							$deletedQuantitiesList[$productId] = $deletedQuantitiesList[$productId] + $quantitiesList[$productId];
							continue;
						}
						$deletedQuantitiesList[$productId] = $quantitiesList[$productId];
					}

					if ($deletedQuantitiesList) {
						$itemQuantitiesList['deleted'] = $deletedQuantitiesList;
					}
				}

				//Constructing quantities array for updated line items
				$updatedProductIds = array_intersect($productIdsList, $requestProductIdsList);
				if (!$totalNoOfProducts) {//$totalNoOfProducts is null then its ajax save
					$updatedProductIds = $productIdsList;
				}
				if ($updatedProductIds) {
					$updatedQuantitiesList = array();
					foreach ($updatedProductIds as $productId) {
						//Checking same item more than once
						if(array_key_exists($productId, $updatedQuantitiesList)) {
							$updatedQuantitiesList[$productId] = $updatedQuantitiesList[$productId] + $quantitiesList[$productId];
							continue;
						}
						
						$quantity = $quantitiesList[$productId];
						if ($totalNoOfProducts) {
							$quantity = $requestQuantitiesList[$productId] - $quantitiesList[$productId];
						}

						if ($quantity) {
							$updatedQuantitiesList[$productId] = $quantity;
						}
						//Check for subproducts
						$subProductQtys = $this->getSubProductsQty($productId);
						if (!empty($subProductQtys) && $quantity) {
							foreach ($subProductQtys as $subProdId => $subProductQty) {
								$subProductQty = $subProductQty * $quantity;
								if (array_key_exists($subProdId, $updatedQuantitiesList)) {
									$updatedQuantitiesList[$subProdId] = $updatedQuantitiesList[$subProdId] + ($subProductQty);
									continue;
								}
								$updatedQuantitiesList[$subProdId] = $subProductQty;
							}
						}
					}
					if ($updatedQuantitiesList) {
						$itemQuantitiesList['updated'] = $updatedQuantitiesList;
					}
				}
			}
		}

		/* $_REQUEST['REQUEST_FROM_WS'] is set from webservices script.
		 * Depending on $_REQUEST['totalProductCount'] value inserting line items into DB.
		 * This should be done by webservices, not be normal save of Inventory record.
		 * So unsetting the value $_REQUEST['totalProductCount'] through check point
		 */
		if (isset($_REQUEST['REQUEST_FROM_WS']) && $_REQUEST['REQUEST_FROM_WS']) {
			unset($_REQUEST['totalProductCount']);
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'PurchaseOrderAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
				&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
				&& $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false && $_REQUEST['action'] != 'FROM_WS') {

			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'PurchaseOrder');
		}

		// Update the currency id and the conversion rate for the purchase order
		$update_query = "update nectarcrm_purchaseorder set currency_id=?, conversion_rate=? where purchaseorderid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$adb->pquery($update_query, $update_params);
	}

	/** Function to get subproducts quantity for given product
	 *  This function accepts the productId as arguments and returns array of subproduct qty for given productId
	 */
	function getSubProductsQty($productId) {
		$subProductQtys = array();
		$adb = PearDatabase::getInstance();
		$result = $adb->pquery("SELECT sequence_no FROM nectarcrm_inventoryproductrel WHERE id = ? and productid=?", array($this->id, $productId));
		$numOfRows = $adb->num_rows($result);
		if ($numOfRows > 0) {
			for ($i = 0; $i < $numOfRows; $i++) {
				$sequenceNo = $adb->query_result($result, $i, 'sequence_no');
				$subProdQuery = $adb->pquery("SELECT productid, quantity FROM nectarcrm_inventorysubproductrel WHERE id=? AND sequence_no=?", array($this->id, $sequenceNo));
				if ($adb->num_rows($subProdQuery) > 0) {
					for ($j = 0; $j < $adb->num_rows($subProdQuery); $j++) {
						$subProdId = $adb->query_result($subProdQuery, $j, 'productid');
						$subProdQty = $adb->query_result($subProdQuery, $j, 'quantity');
						$subProductQtys[$subProdId] = $subProdQty;
					}
				}
			}
		}
		return $subProductQtys;
	}

	/** Function to get activities associated with the Purchase Order
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
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.contactid,nectarcrm_activity.*,nectarcrm_seactivityrel.crmid as parent_id,nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime from nectarcrm_activity inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid where nectarcrm_seactivityrel.crmid=".$id." and activitytype='Task' and nectarcrm_crmentity.deleted=0 and (nectarcrm_activity.status is not NULL && nectarcrm_activity.status != 'Completed') and (nectarcrm_activity.status is not NULL and nectarcrm_activity.status != 'Deferred') ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/** Function to get the activities history associated with the Purchase Order
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
			nectarcrm_contactdetails.contactid,nectarcrm_activity.* ,nectarcrm_seactivityrel.*,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description,case when
			(nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end
			as user_name from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
				left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid= nectarcrm_activity.activityid
				left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
                                left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
			where nectarcrm_activity.activitytype='Task'
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred')
				and nectarcrm_seactivityrel.crmid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

        $returnValue = getHistory('PurchaseOrder',$query,$id);
		$log->debug("Exiting get_history method ...");
		return $returnValue;
	}


	/**	Function used to get the Status history of the Purchase Order
	 *	@param $id - purchaseorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_postatushistory($id)
	{
		global $log;
		$log->debug("Entering get_postatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select nectarcrm_postatushistory.*, nectarcrm_purchaseorder.purchaseorder_no from nectarcrm_postatushistory inner join nectarcrm_purchaseorder on nectarcrm_purchaseorder.purchaseorderid = nectarcrm_postatushistory.purchaseorderid inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_purchaseorder.purchaseorderid where nectarcrm_crmentity.deleted = 0 and nectarcrm_purchaseorder.purchaseorderid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Order No'];
		$header[] = $app_strings['Vendor Name'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_PO_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Vendor, Total are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$postatus_access = (getFieldVisibilityPermission('PurchaseOrder', $current_user->id, 'postatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('PurchaseOrder');

		$postatus_array = ($postatus_access != 1)? $picklistarray['postatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($postatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			//Module Sequence Numbering
			//$entries[] = $row['purchaseorderid'];
			$entries[] = $row['purchaseorder_no'];
			// END
			$entries[] = $row['vendorname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['postatus'], $postatus_array))? $row['postatus']: $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_postatushistory method ...");

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
		$matrix->setDependency('nectarcrm_crmentityPurchaseOrder', array('nectarcrm_usersPurchaseOrder', 'nectarcrm_groupsPurchaseOrder', 'nectarcrm_lastModifiedByPurchaseOrder'));
		$matrix->setDependency('nectarcrm_inventoryproductrelPurchaseOrder', array('nectarcrm_productsPurchaseOrder', 'nectarcrm_servicePurchaseOrder'));
		
		if (!$queryPlanner->requireTable('nectarcrm_purchaseorder', $matrix)) {
			return '';
		}
        $matrix->setDependency('nectarcrm_purchaseorder',array('nectarcrm_crmentityPurchaseOrder', "nectarcrm_currency_info$secmodule",
				'nectarcrm_purchaseordercf', 'nectarcrm_vendorRelPurchaseOrder', 'nectarcrm_pobillads',
				'nectarcrm_poshipads', 'nectarcrm_inventoryproductrelPurchaseOrder', 'nectarcrm_contactdetailsPurchaseOrder'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_purchaseorder","purchaseorderid",$queryPlanner);
		if ($queryPlanner->requireTable("nectarcrm_crmentityPurchaseOrder", $matrix)){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityPurchaseOrder on nectarcrm_crmentityPurchaseOrder.crmid=nectarcrm_purchaseorder.purchaseorderid and nectarcrm_crmentityPurchaseOrder.deleted=0";
		}
		if ($queryPlanner->requireTable("nectarcrm_purchaseordercf")){
			$query .= " left join nectarcrm_purchaseordercf on nectarcrm_purchaseorder.purchaseorderid = nectarcrm_purchaseordercf.purchaseorderid";
		}
		if ($queryPlanner->requireTable("nectarcrm_pobillads")){
			$query .= " left join nectarcrm_pobillads on nectarcrm_purchaseorder.purchaseorderid=nectarcrm_pobillads.pobilladdressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_poshipads")){
			$query .= " left join nectarcrm_poshipads on nectarcrm_purchaseorder.purchaseorderid=nectarcrm_poshipads.poshipaddressid";
		}
		if ($queryPlanner->requireTable("nectarcrm_currency_info$secmodule")){
			$query .= " left join nectarcrm_currency_info as nectarcrm_currency_info$secmodule on nectarcrm_currency_info$secmodule.id = nectarcrm_purchaseorder.currency_id";
		}
		if ($queryPlanner->requireTable("nectarcrm_inventoryproductrelPurchaseOrder", $matrix)){
		}
		if ($queryPlanner->requireTable("nectarcrm_productsPurchaseOrder")){
			$query .= " left join nectarcrm_products as nectarcrm_productsPurchaseOrder on nectarcrm_productsPurchaseOrder.productid = nectarcrm_inventoryproductreltmpPurchaseOrder.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_servicePurchaseOrder")){
			$query .= " left join nectarcrm_service as nectarcrm_servicePurchaseOrder on nectarcrm_servicePurchaseOrder.serviceid = nectarcrm_inventoryproductreltmpPurchaseOrder.productid";
		}
		if ($queryPlanner->requireTable("nectarcrm_usersPurchaseOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_usersPurchaseOrder on nectarcrm_usersPurchaseOrder.id = nectarcrm_crmentityPurchaseOrder.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_groupsPurchaseOrder")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsPurchaseOrder on nectarcrm_groupsPurchaseOrder.groupid = nectarcrm_crmentityPurchaseOrder.smownerid";
		}
		if ($queryPlanner->requireTable("nectarcrm_vendorRelPurchaseOrder")){
			$query .= " left join nectarcrm_vendor as nectarcrm_vendorRelPurchaseOrder on nectarcrm_vendorRelPurchaseOrder.vendorid = nectarcrm_purchaseorder.vendorid";
		}
		if ($queryPlanner->requireTable("nectarcrm_contactdetailsPurchaseOrder")){
			$query .= " left join nectarcrm_contactdetails as nectarcrm_contactdetailsPurchaseOrder on nectarcrm_contactdetailsPurchaseOrder.contactid = nectarcrm_purchaseorder.contactid";
		}
		if ($queryPlanner->requireTable("nectarcrm_lastModifiedByPurchaseOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByPurchaseOrder on nectarcrm_lastModifiedByPurchaseOrder.id = nectarcrm_crmentityPurchaseOrder.modifiedby ";
		}
        if ($queryPlanner->requireTable("nectarcrm_createdbyPurchaseOrder")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyPurchaseOrder on nectarcrm_createdbyPurchaseOrder.id = nectarcrm_crmentityPurchaseOrder.smcreatorid ";
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
			"Calendar" =>array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_purchaseorder"=>"purchaseorderid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_purchaseorder"=>"purchaseorderid"),
			"Contacts" => array("nectarcrm_purchaseorder"=>array("purchaseorderid","contactid")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Vendors') {
			$sql_req ='UPDATE nectarcrm_crmentity SET deleted = 1 WHERE crmid= ?';
			$this->db->pquery($sql_req, array($id));
		} elseif($return_module == 'Contacts') {
			$sql_req ='UPDATE nectarcrm_purchaseorder SET contactid=? WHERE purchaseorderid = ?';
			$this->db->pquery($sql_req, array(null, $id));
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Accounts') {
			$sql ='UPDATE nectarcrm_purchaseorder SET accountid=? WHERE purchaseorderid=?';
			$this->db->pquery($sql, array(null, $id));
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
	* Returns Export PurchaseOrder Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("PurchaseOrder", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN nectarcrm_purchaseorder ON nectarcrm_purchaseorder.purchaseorderid = nectarcrm_crmentity.crmid
				LEFT JOIN nectarcrm_purchaseordercf ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid
				LEFT JOIN nectarcrm_pobillads ON nectarcrm_pobillads.pobilladdressid = nectarcrm_purchaseorder.purchaseorderid
				LEFT JOIN nectarcrm_poshipads ON nectarcrm_poshipads.poshipaddressid = nectarcrm_purchaseorder.purchaseorderid
				LEFT JOIN nectarcrm_inventoryproductrel ON nectarcrm_inventoryproductrel.id = nectarcrm_purchaseorder.purchaseorderid
				LEFT JOIN nectarcrm_products ON nectarcrm_products.productid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_service ON nectarcrm_service.serviceid = nectarcrm_inventoryproductrel.productid
				LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_purchaseorder.contactid
				LEFT JOIN nectarcrm_vendor ON nectarcrm_vendor.vendorid = nectarcrm_purchaseorder.vendorid
				LEFT JOIN nectarcrm_currency_info ON nectarcrm_currency_info.id = nectarcrm_purchaseorder.currency_id
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('PurchaseOrder',$current_user);
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