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


/**	function used to get the permitted blocks
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $blockid_list - list of block ids within the paranthesis with comma seperated
 */
function getPermittedBlocks($module, $disp_view)
{
	global $adb, $log;
	$log->debug("Entering into the function getPermittedBlocks($module, $disp_view)");

        $tabid = getTabid($module);
        $block_detail = Array();
        $query="select blockid,blocklabel,show_title from nectarcrm_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
        $result = $adb->pquery($query, array($tabid));
        $noofrows = $adb->num_rows($result);
	$blockid_list ='(';
	for($i=0; $i<$noofrows; $i++)
	{
		$blockid = $adb->query_result($result,$i,"blockid");
		if($i != 0)
			$blockid_list .= ', ';
		$blockid_list .= $blockid;
		$block_label[$blockid] = $adb->query_result($result,$i,"blocklabel");
	}
	$blockid_list .= ')';

	$log->debug("Exit from the function getPermittedBlocks($module, $disp_view). Return value = $blockid_list");
	return $blockid_list;
}

/**	function used to get the query which will list the permitted fields
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $sql - query to get the list of fields which are permitted to the current user
 */
function getPermittedFieldsQuery($module, $disp_view)
{
	global $adb, $log;
	$log->debug("Entering into the function getPermittedFieldsQuery($module, $disp_view)");

	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	//To get the permitted blocks
	$blockid_list = getPermittedBlocks($module, $disp_view);

        $tabid = getTabid($module);
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users")
	{
 		$sql = "SELECT nectarcrm_field.columnname, nectarcrm_field.fieldlabel, nectarcrm_field.tablename FROM nectarcrm_field WHERE nectarcrm_field.tabid=".$tabid." AND nectarcrm_field.block IN $blockid_list AND nectarcrm_field.displaytype IN (1,2,4,5) and nectarcrm_field.presence in (0,2) ORDER BY block,sequence";
  	}
  	else
  	{
		$profileList = getCurrentUserProfileList();
		$sql = "SELECT nectarcrm_field.columnname, nectarcrm_field.fieldlabel, nectarcrm_field.tablename FROM nectarcrm_field INNER JOIN nectarcrm_profile2field ON nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid INNER JOIN nectarcrm_def_org_field ON nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid WHERE nectarcrm_field.tabid=".$tabid." AND nectarcrm_field.block IN ".$blockid_list." AND nectarcrm_field.displaytype IN (1,2,4,5) AND nectarcrm_profile2field.visible=0 AND nectarcrm_def_org_field.visible=0 AND nectarcrm_profile2field.profileid IN (". implode(",", $profileList) .") and nectarcrm_field.presence in (0,2) GROUP BY nectarcrm_field.fieldid ORDER BY block,sequence";
	}

	$log->debug("Exit from the function getPermittedFieldsQuery($module, $disp_view). Return value = $sql");
	return $sql;
}

/**	function used to get the list of fields from the input query as a comma seperated string
 *	@param string $query - field table query which contains the list of fields
 *	@return string $fields - list of fields as a comma seperated string
 */
function getFieldsListFromQuery($query)
{
	global $adb, $log;
	$log->debug("Entering into the function getFieldsListFromQuery($query)");

	$result = $adb->query($query);
	$num_rows = $adb->num_rows($result);

	for($i=0; $i < $num_rows;$i++)
	{
		$columnName = $adb->query_result($result,$i,"columnname");
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$tablename = $adb->query_result($result,$i,"tablename");

		//HANDLE HERE - Mismatch fieldname-tablename in field table, in future we have to avoid these if elses
		if($columnName == 'smownerid')//for all assigned to user name
		{
			$fields .= "case when (nectarcrm_users.user_name not like '') then nectarcrm_users.user_name else nectarcrm_groups.groupname end as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_account' && $columnName == 'parentid')//Account - Member Of
		{
			 $fields .= "nectarcrm_account2.accountname as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_contactdetails' && $columnName == 'accountid')//Contact - Account Name
		{
			$fields .= "nectarcrm_account.accountname as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_contactdetails' && $columnName == 'reportsto')//Contact - Reports To
		{
			$fields .= " concat(nectarcrm_contactdetails2.lastname,' ',nectarcrm_contactdetails2.firstname) as 'Reports To Contact',";
		}
		elseif($tablename == 'nectarcrm_potential' && $columnName == 'related_to')//Potential - Related to (changed for B2C model support)
		{
			$fields .= "nectarcrm_potential.related_to as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_potential' && $columnName == 'campaignid')//Potential - Campaign Source
		{
			$fields .= "nectarcrm_campaign.campaignname as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_seproductsrel' && $columnName == 'crmid')//Product - Related To
		{
			$fields .= "case nectarcrm_crmentityRelatedTo.setype
					when 'Leads' then concat('Leads :::: ',nectarcrm_ProductRelatedToLead.lastname,' ',nectarcrm_ProductRelatedToLead.firstname)
					when 'Accounts' then concat('Accounts :::: ',nectarcrm_ProductRelatedToAccount.accountname)
					when 'Potentials' then concat('Potentials :::: ',nectarcrm_ProductRelatedToPotential.potentialname)
				    End as 'Related To',";
		}
		elseif($tablename == 'nectarcrm_products' && $columnName == 'contactid')//Product - Contact
		{
			$fields .= " concat(nectarcrm_contactdetails.lastname,' ',nectarcrm_contactdetails.firstname) as 'Contact Name',";
		}
		elseif($tablename == 'nectarcrm_products' && $columnName == 'vendor_id')//Product - Vendor Name
		{
			$fields .= "nectarcrm_vendor.vendorname as '".$fieldlabel."',";
		}
		elseif($tablename == 'nectarcrm_producttaxrel' && $columnName == 'taxclass')//avoid product - taxclass
		{
			$fields .= "";
		}
		elseif($tablename == 'nectarcrm_attachments' && $columnName == 'name')//Emails filename
		{
			$fields .= $tablename.".name as '".$fieldlabel."',";
		}
		//By Pavani...Handling mismatch field and table name for trouble tickets
      	elseif($tablename == 'nectarcrm_troubletickets' && $columnName == 'product_id')//Ticket - Product
        {
			$fields .= "nectarcrm_products.productname as '".$fieldlabel."',";
        }
        elseif($tablename == 'nectarcrm_notes' && ($columnName == 'filename' || $columnName == 'filetype' || $columnName == 'filesize' || $columnName == 'filelocationtype' || $columnName == 'filestatus' || $columnName == 'filedownloadcount' ||$columnName == 'folderid')){
			continue;
		}
		elseif(($tablename == 'nectarcrm_invoice' || $tablename == 'nectarcrm_quotes' || $tablename == 'nectarcrm_salesorder')&& $columnName == 'accountid') {
			$fields .= 'concat("Accounts::::",nectarcrm_account.accountname) as "'.$fieldlabel.'",';
		}
		elseif(($tablename == 'nectarcrm_invoice' || $tablename == 'nectarcrm_quotes' || $tablename == 'nectarcrm_salesorder' || $tablename == 'nectarcrm_purchaseorder') && $columnName == 'contactid') {
			$fields .= 'concat("Contacts::::",nectarcrm_contactdetails.lastname," ",nectarcrm_contactdetails.firstname) as "'.$fieldlabel.'",';
		}
		elseif($tablename == 'nectarcrm_invoice' && $columnName == 'salesorderid') {
			$fields .= 'concat("SalesOrder::::",nectarcrm_salesorder.subject) as "'.$fieldlabel.'",';
		}
		elseif(($tablename == 'nectarcrm_quotes' || $tablename == 'nectarcrm_salesorder') && $columnName == 'potentialid') {
			$fields .= 'concat("Potentials::::",nectarcrm_potential.potentialname) as "'.$fieldlabel.'",';
		}
		elseif($tablename == 'nectarcrm_quotes' && $columnName == 'inventorymanager') {
			$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_inventoryManager.first_name', 'last_name' => 'nectarcrm_inventoryManager.last_name'), 'Users');
			$fields .= $userNameSql. ' as "'.$fieldlabel.'",';
		}
		elseif($tablename == 'nectarcrm_salesorder' && $columnName == 'quoteid') {
			$fields .= 'concat("Quotes::::",nectarcrm_quotes.subject) as "'.$fieldlabel.'",';
		}
		elseif($tablename == 'nectarcrm_purchaseorder' && $columnName == 'vendorid') {
			$fields .= 'concat("Vendors::::",nectarcrm_vendor.vendorname) as "'.$fieldlabel.'",';
		}
		else
		{
			$fields .= $tablename.".".$columnName. " as '" .$fieldlabel."',";
		}
	}
	$fields = trim($fields,",");

	$log->debug("Exit from the function getFieldsListFromQuery($query). Return value = $fields");
	return $fields;
}



?>
