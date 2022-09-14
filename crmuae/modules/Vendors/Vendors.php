<?php
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
*
 ********************************************************************************/
class Vendors extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "nectarcrm_vendor";
	var $table_index= 'vendorid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_vendor','nectarcrm_vendorcf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_vendor'=>'vendorid','nectarcrm_vendorcf'=>'vendorid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_vendorcf', 'vendorid');
	var $column_fields = Array();

        //Pavani: Assign value to entity_table
        var $entity_table = "nectarcrm_crmentity";
        var $sortby_fields = Array('vendorname','category');

        // This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
                                'Vendor Name'=>Array('vendor'=>'vendorname'),
                                'Phone'=>Array('vendor'=>'phone'),
                                'Email'=>Array('vendor'=>'email'),
                                'Category'=>Array('vendor'=>'category')
                                );
        var $list_fields_name = Array(
                                        'Vendor Name'=>'vendorname',
                                        'Phone'=>'phone',
                                        'Email'=>'email',
                                        'Category'=>'category'
                                     );
        var $list_link_field= 'vendorname';

	var $search_fields = Array(
                                'Vendor Name'=>Array('vendor'=>'vendorname'),
                                'Phone'=>Array('vendor'=>'phone')
                                );
        var $search_fields_name = Array(
                                        'Vendor Name'=>'vendorname',
                                        'Phone'=>'phone'
                                     );
	//Specifying required fields for vendors
        var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'vendorname', 'assigned_user_id');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'vendorname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'vendorname';

	/**	Constructor which will set the column_fields in this object
	 */
	function Vendors() {
		$this->log =LoggerManager::getLogger('vendor');
		$this->log->debug("Entering Vendors() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Vendors');
		$this->log->debug("Exiting Vendor method ...");
	}

	function save_module($module)
	{
	}

	/**	function used to get the list of products which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_products(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.parent_id.value=\"\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>";
			}
		}

		$query = "SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode,
					nectarcrm_products.commissionrate, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price,
					nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,nectarcrm_vendor.vendorname
			  		FROM nectarcrm_products
			  		INNER JOIN nectarcrm_vendor ON nectarcrm_vendor.vendorid = nectarcrm_products.vendor_id
			  		INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid INNER JOIN nectarcrm_productcf
				    ON nectarcrm_products.productid = nectarcrm_productcf.productid
					LEFT JOIN nectarcrm_users
						ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
					LEFT JOIN nectarcrm_groups
						ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			  		WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_vendor.vendorid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/**	function used to get the list of purchase orders which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_purchase_orders(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "select case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.*, nectarcrm_purchaseorder.*,nectarcrm_vendor.vendorname from nectarcrm_purchaseorder inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_purchaseorder.purchaseorderid left outer join nectarcrm_vendor on nectarcrm_purchaseorder.vendorid=nectarcrm_vendor.vendorid LEFT JOIN nectarcrm_purchaseordercf ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid LEFT JOIN nectarcrm_pobillads ON nectarcrm_pobillads.pobilladdressid = nectarcrm_purchaseorder.purchaseorderid LEFT JOIN nectarcrm_poshipads ON nectarcrm_poshipads.poshipaddressid = nectarcrm_purchaseorder.purchaseorderid  left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid where nectarcrm_crmentity.deleted=0 and nectarcrm_purchaseorder.vendorid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_purchase_orders method ...");
		return $return_value;
	}
	//Pavani: Function to create, export query for vendors module
        /** Function to export the vendors in CSV Format
        * @param reference variable - where condition is passed when the query is executed
        * Returns Export Vendors Query.
        */
        function create_export_query($where)
        {
                global $log;
                global $current_user;
                $log->debug("Entering create_export_query(".$where.") method ...");

                include("include/utils/ExportUtils.php");

                //To get the Permitted fields query and the permitted fields list
                $sql = getPermittedFieldsQuery("Vendors", "detail_view");
                $fields_list = getFieldsListFromQuery($sql);

                $query = "SELECT $fields_list FROM ".$this->entity_table."
                                INNER JOIN nectarcrm_vendor
                                        ON nectarcrm_crmentity.crmid = nectarcrm_vendor.vendorid
                                LEFT JOIN nectarcrm_vendorcf
                                        ON nectarcrm_vendorcf.vendorid=nectarcrm_vendor.vendorid
                                LEFT JOIN nectarcrm_seattachmentsrel
                                        ON nectarcrm_vendor.vendorid=nectarcrm_seattachmentsrel.crmid
                                LEFT JOIN nectarcrm_attachments
                                ON nectarcrm_seattachmentsrel.attachmentsid = nectarcrm_attachments.attachmentsid
                                LEFT JOIN nectarcrm_users
                                        ON nectarcrm_crmentity.smownerid = nectarcrm_users.id and nectarcrm_users.status='Active'
                                ";
                $where_auto = " nectarcrm_crmentity.deleted = 0 ";

                 if($where != "")
                   $query .= "  WHERE ($where) AND ".$where_auto;
                else
                   $query .= "  WHERE ".$where_auto;

                $log->debug("Exiting create_export_query method ...");
                return $query;
        }

	/**	function used to get the list of contacts which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_contacts(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_contactdetails.*, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,nectarcrm_vendorcontactrel.vendorid,nectarcrm_account.accountname from nectarcrm_contactdetails
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
				inner join nectarcrm_vendorcontactrel on nectarcrm_vendorcontactrel.contactid=nectarcrm_contactdetails.contactid
				INNER JOIN nectarcrm_contactaddress ON nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid
				INNER JOIN nectarcrm_contactsubdetails ON nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid
				INNER JOIN nectarcrm_customerdetails ON nectarcrm_contactdetails.contactid = nectarcrm_customerdetails.customerid
				INNER JOIN nectarcrm_contactscf ON nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid
				left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_account on nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				where nectarcrm_crmentity.deleted=0 and nectarcrm_vendorcontactrel.vendorid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
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

		$rel_table_arr = Array("Products"=>"nectarcrm_products","PurchaseOrder"=>"nectarcrm_purchaseorder","Contacts"=>"nectarcrm_vendorcontactrel","Emails"=>"nectarcrm_seactivityrel");

		$tbl_field_arr = Array("nectarcrm_products"=>"productid","nectarcrm_vendorcontactrel"=>"contactid","nectarcrm_purchaseorder"=>"purchaseorderid","nectarcrm_seactivityrel"=>"activityid");

		$entity_tbl_field_arr = Array("nectarcrm_products"=>"vendor_id","nectarcrm_vendorcontactrel"=>"vendorid","nectarcrm_purchaseorder"=>"vendorid","nectarcrm_seactivityrel"=>"crmid");

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
		$log->debug("Exiting transferRelatedRecords...");
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
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
			nectarcrm_activity.activityid, nectarcrm_activity.subject,
			nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start,nectarcrm_activity.time_start, nectarcrm_seactivityrel.crmid as parent_id
			FROM nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_vendor, nectarcrm_users, nectarcrm_crmentity
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
			WHERE nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
				AND nectarcrm_vendor.vendorid = nectarcrm_seactivityrel.crmid
				AND nectarcrm_users.id=nectarcrm_crmentity.smownerid
				AND nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
				AND nectarcrm_vendor.vendorid = ".$id."
				AND nectarcrm_activity.activitytype='Emails'
				AND nectarcrm_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
		return $return_value;
	}

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	function generateReportsQuery($module, $queryPlanner) {
		$moduletable = $this->table_name;
		$moduleindex = $this->table_index;
		$modulecftable = $this->tab_name[2];
		$modulecfindex = $this->tab_name_index[$modulecftable];

		$query = "from $moduletable
			inner join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$moduletable.$moduleindex
			inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=$moduletable.$moduleindex
			left join nectarcrm_groups as nectarcrm_groups$module on nectarcrm_groups$module.groupid = nectarcrm_crmentity.smownerid
			left join nectarcrm_users as nectarcrm_users".$module." on nectarcrm_users".$module.".id = nectarcrm_crmentity.smownerid
			left join nectarcrm_groups on nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid 
            left join nectarcrm_users as nectarcrm_createdby".$module." on nectarcrm_createdby".$module.".id = nectarcrm_crmentity.smcreatorid 
			left join nectarcrm_users as nectarcrm_lastModifiedByVendors on nectarcrm_lastModifiedByVendors.id = nectarcrm_crmentity.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule, $queryplanner) {

		$matrix = $queryplanner->newDependencyMatrix();

		$matrix->setDependency("nectarcrm_crmentityVendors",array("nectarcrm_usersVendors","nectarcrm_lastModifiedByVendors"));
		if (!$queryplanner->requireTable('nectarcrm_vendor', $matrix)) {
			return '';
		}
        $matrix->setDependency("nectarcrm_vendor",array("nectarcrm_crmentityVendors","nectarcrm_vendorcf","nectarcrm_email_trackVendors"));
		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_vendor","vendorid", $queryplanner);
		// TODO Support query planner
		if ($queryplanner->requireTable("nectarcrm_crmentityVendors",$matrix)){
		    $query .=" left join nectarcrm_crmentity as nectarcrm_crmentityVendors on nectarcrm_crmentityVendors.crmid=nectarcrm_vendor.vendorid and nectarcrm_crmentityVendors.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_vendorcf")){
		    $query .=" left join nectarcrm_vendorcf on nectarcrm_vendorcf.vendorid = nectarcrm_crmentityVendors.crmid";
		}
		if ($queryplanner->requireTable("nectarcrm_email_trackVendors")){
		    $query .=" LEFT JOIN nectarcrm_email_track AS nectarcrm_email_trackVendors ON nectarcrm_email_trackVendors.crmid = nectarcrm_vendor.vendorid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersVendors")){
		    $query .=" left join nectarcrm_users as nectarcrm_usersVendors on nectarcrm_usersVendors.id = nectarcrm_crmentityVendors.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByVendors")){
		    $query .=" left join nectarcrm_users as nectarcrm_lastModifiedByVendors on nectarcrm_lastModifiedByVendors.id = nectarcrm_crmentityVendors.modifiedby ";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyVendors")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyVendors on nectarcrm_createdbyVendors.id = nectarcrm_crmentityVendors.smcreatorid ";
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
			"Products" =>array("nectarcrm_products"=>array("vendor_id","productid"),"nectarcrm_vendor"=>"vendorid"),
			"PurchaseOrder" =>array("nectarcrm_purchaseorder"=>array("vendorid","purchaseorderid"),"nectarcrm_vendor"=>"vendorid"),
			"Contacts" =>array("nectarcrm_vendorcontactrel"=>array("vendorid","contactid"),"nectarcrm_vendor"=>"vendorid"),
			"Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_vendor"=>"vendorid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		//Deleting Vendor related PO.
		$po_q = 'SELECT nectarcrm_crmentity.crmid FROM nectarcrm_crmentity
			INNER JOIN nectarcrm_purchaseorder ON nectarcrm_crmentity.crmid=nectarcrm_purchaseorder.purchaseorderid
			INNER JOIN nectarcrm_vendor ON nectarcrm_vendor.vendorid=nectarcrm_purchaseorder.vendorid
			WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_purchaseorder.vendorid=?';
		$po_res = $this->db->pquery($po_q, array($id));
		$po_ids_list = array();
		for($k=0;$k < $this->db->num_rows($po_res);$k++)
		{
			$po_id = $this->db->query_result($po_res,$k,"crmid");
			$po_ids_list[] = $po_id;
			$sql = 'UPDATE nectarcrm_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($po_id));
		}
		//Backup deleted Vendors related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_crmentity', 'deleted', 'crmid', implode(",", $po_ids_list));
		$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);

		//Backup Product-Vendor Relation
		$pro_q = 'SELECT productid FROM nectarcrm_products WHERE vendor_id=?';
		$pro_res = $this->db->pquery($pro_q, array($id));
		if ($this->db->num_rows($pro_res) > 0) {
			$pro_ids_list = array();
			for($k=0;$k < $this->db->num_rows($pro_res);$k++)
			{
				$pro_ids_list[] = $this->db->query_result($pro_res,$k,"productid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_products', 'vendor_id', 'productid', implode(",", $pro_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//Deleting Product-Vendor Relation.
		$pro_q = 'UPDATE nectarcrm_products SET vendor_id = 0 WHERE vendor_id = ?';
		$this->db->pquery($pro_q, array($id));

		/*//Backup Contact-Vendor Relaton
		$con_q = 'SELECT contactid FROM nectarcrm_vendorcontactrel WHERE vendorid = ?';
		$con_res = $this->db->pquery($con_q, array($id));
		if ($this->db->num_rows($con_res) > 0) {
			for($k=0;$k < $this->db->num_rows($con_res);$k++)
			{
				$con_id = $this->db->query_result($con_res,$k,"contactid");
				$params = array($id, RB_RECORD_DELETED, 'nectarcrm_vendorcontactrel', 'vendorid', 'contactid', $con_id);
				$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
			}
		}
		//Deleting Contact-Vendor Relaton
		$vc_sql = 'DELETE FROM nectarcrm_vendorcontactrel WHERE vendorid=?';
		$this->db->pquery($vc_sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Contacts')
				$adb->pquery("insert into nectarcrm_vendorcontactrel values (?,?)", array($crmid, $with_crmid));
			elseif($with_module == 'Products')
				$adb->pquery("update nectarcrm_products set vendor_id=? where productid=?", array($crmid, $with_crmid));
			else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;
		if($return_module == 'Contacts') {
			$sql = 'DELETE FROM nectarcrm_vendorcontactrel WHERE vendorid=? AND contactid=?';
			$this->db->pquery($sql, array($id,$return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

}
?>
