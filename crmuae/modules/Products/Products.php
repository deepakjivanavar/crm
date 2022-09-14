<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Products extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'nectarcrm_products';
	var $table_index= 'productid';
	var $column_fields = Array();

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_productcf','productid');

	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_products','nectarcrm_productcf');

	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_products'=>'productid','nectarcrm_productcf'=>'productid','nectarcrm_seproductsrel'=>'productid','nectarcrm_producttaxrel'=>'productid');



	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
		'Product Name'=>Array('products'=>'productname'),
		'Part Number'=>Array('products'=>'productcode'),
		'Commission Rate'=>Array('products'=>'commissionrate'),
		'Qty/Unit'=>Array('products'=>'qty_per_unit'),
		'Unit Price'=>Array('products'=>'unit_price')
	);
	var $list_fields_name = Array(
		'Product Name'=>'productname',
		'Part Number'=>'productcode',
		'Commission Rate'=>'commissionrate',
		'Qty/Unit'=>'qty_per_unit',
		'Unit Price'=>'unit_price'
	);

	var $list_link_field= 'productname';

	var $search_fields = Array(
		'Product Name'=>Array('products'=>'productname'),
		'Part Number'=>Array('products'=>'productcode'),
		'Unit Price'=>Array('products'=>'unit_price')
	);
	var $search_fields_name = Array(
		'Product Name'=>'productname',
		'Part Number'=>'productcode',
		'Unit Price'=>'unit_price'
	);

	var $required_fields = Array('productname'=>1);

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();
	var $def_basicsearch_col = 'productname';

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'productname';
	var $default_sort_order = 'ASC';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'productname', 'assigned_user_id');
	 // Josh added for importing and exporting -added in patch2
	var $unit_price;

	/**	Constructor which will set the column_fields in this object
	 */
	function Products() {
		$this->log =LoggerManager::getLogger('product');
		$this->log->debug("Entering Products() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Products');
		$this->log->debug("Exiting Product method ...");
	}

	function save_module($module)
	{
		//Inserting into product_taxrel table
		if($_REQUEST['ajxaction'] != 'DETAILVIEW' && $_REQUEST['action'] != 'ProcessDuplicates' && !$this->isWorkFlowFieldUpdate)
		{
			if ($_REQUEST['ajxaction'] != 'CurrencyUpdate') {
				$this->insertTaxInformation('nectarcrm_producttaxrel', 'Products');
			}

			if ($_REQUEST['action'] != 'MassEditSave' ) {
				$this->insertPriceInformation('nectarcrm_productcurrencyrel', 'Products');
			}
		}

		if($_REQUEST['action'] == 'SaveAjax' && isset($_REQUEST['base_currency']) && isset($_REQUEST['unit_price'])){
			$this->insertPriceInformation('nectarcrm_productcurrencyrel', 'Products');
		}
		// Update unit price value in nectarcrm_productcurrencyrel
		$this->updateUnitPrice();
		//Inserting into attachments, handle image save in crmentity uitype 69
		//$this->insertIntoAttachment($this->id,'Products');

	}

	/**	function to save the product tax information in nectarcrm_producttaxrel table
	 *	@param string $tablename - nectarcrm_tablename to save the product tax relationship (producttaxrel)
	 *	@param string $module	 - current module name
	 *	$return void
	*/
	function insertTaxInformation($tablename, $module)
	{
		global $adb, $log;
		$log->debug("Entering into insertTaxInformation($tablename, $module) method ...");
		$tax_details = getAllTaxes();

		$tax_per = '';
		//Save the Product - tax relationship if corresponding tax check box is enabled
		//Delete the existing tax if any
		if($this->mode == 'edit' && $_REQUEST['action'] != 'MassEditSave')
		{
			for($i=0;$i<count($tax_details);$i++)
			{
				$taxid = getTaxId($tax_details[$i]['taxname']);
				$sql = "DELETE FROM nectarcrm_producttaxrel WHERE productid=? AND taxid=?";
				$adb->pquery($sql, array($this->id,$taxid));
			}
		}
		for($i=0;$i<count($tax_details);$i++)
		{
			$tax_name = $tax_details[$i]['taxname'];
			$tax_checkname = $tax_details[$i]['taxname']."_check";
			if($_REQUEST[$tax_checkname] == 'on' || $_REQUEST[$tax_checkname] == 1)
			{
				$taxid = getTaxId($tax_name);
				$tax_per = $_REQUEST[$tax_name];

				$taxRegions = $_REQUEST[$tax_name.'_regions'];
				if ($taxRegions || $_REQUEST[$tax_name.'_defaultPercentage'] != '') {
					$tax_per = $_REQUEST[$tax_name.'_defaultPercentage'];
				} else {
					$taxRegions = array();
				}

				if($tax_per == '')
				{
					$log->debug("Tax selected but value not given so default value will be saved.");
					$tax_per = getTaxPercentage($tax_name);
				}

				$log->debug("Going to save the Product - $tax_name tax relationship");

				if ($_REQUEST['action'] === 'MassEditSave') {
					$adb->pquery('DELETE FROM nectarcrm_producttaxrel WHERE productid=? AND taxid=?', array($this->id, $taxid));
				}

				$query = "INSERT INTO nectarcrm_producttaxrel VALUES(?,?,?,?)";
				$adb->pquery($query, array($this->id, $taxid, $tax_per, Zend_Json::encode($taxRegions)));
			}
		}

		$log->debug("Exiting from insertTaxInformation($tablename, $module) method ...");
	}

	/**	function to save the product price information in nectarcrm_productcurrencyrel table
	 *	@param string $tablename - nectarcrm_tablename to save the product currency relationship (productcurrencyrel)
	 *	@param string $module	 - current module name
	 *	$return void
	*/
	function insertPriceInformation($tablename, $module)
	{
		global $adb, $log, $current_user;
		$log->debug("Entering into insertPriceInformation($tablename, $module) method ...");
		//removed the update of currency_id based on the logged in user's preference : fix 6490

		$currency_details = getAllCurrencies('all');

		//Delete the existing currency relationship if any
		if($this->mode == 'edit' && $_REQUEST['action'] !== 'CurrencyUpdate')
		{
			for($i=0;$i<count($currency_details);$i++)
			{
				$curid = $currency_details[$i]['curid'];
				$sql = "delete from nectarcrm_productcurrencyrel where productid=? and currencyid=?";
				$adb->pquery($sql, array($this->id,$curid));
			}
		}

		$product_base_conv_rate = getBaseConversionRateForProduct($this->id, $this->mode);
		$currencySet = 0;
		//Save the Product - Currency relationship if corresponding currency check box is enabled
		for($i=0;$i<count($currency_details);$i++)
		{
			$curid = $currency_details[$i]['curid'];
			$curname = $currency_details[$i]['currencylabel'];
			$cur_checkname = 'cur_' . $curid . '_check';
			$cur_valuename = 'curname' . $curid;

			$requestPrice = CurrencyField::convertToDBFormat($_REQUEST['unit_price'], null, true);
			$actualPrice = CurrencyField::convertToDBFormat($_REQUEST[$cur_valuename], null, true);
			$isQuickCreate = false;
			if($_REQUEST['action']=='SaveAjax' && isset($_REQUEST['base_currency']) && $_REQUEST['base_currency'] == $cur_valuename){
				$actualPrice = $requestPrice;
				$isQuickCreate = true;
			}
			if($_REQUEST[$cur_checkname] == 'on' || $_REQUEST[$cur_checkname] == 1 || $isQuickCreate)
			{
				$conversion_rate = $currency_details[$i]['conversionrate'];
				$actual_conversion_rate = $product_base_conv_rate * $conversion_rate;
				$converted_price = $actual_conversion_rate * $requestPrice;

				$log->debug("Going to save the Product - $curname currency relationship");

				if ($_REQUEST['action'] === 'CurrencyUpdate') {
					$adb->pquery('DELETE FROM nectarcrm_productcurrencyrel WHERE productid=? AND currencyid=?', array($this->id, $curid));
				}

				$query = "insert into nectarcrm_productcurrencyrel values(?,?,?,?)";
				$adb->pquery($query, array($this->id,$curid,$converted_price,$actualPrice));

				// Update the Product information with Base Currency choosen by the User.
				if ($_REQUEST['base_currency'] == $cur_valuename) {
					$currencySet = 1;
					$adb->pquery("update nectarcrm_products set currency_id=?, unit_price=? where productid=?", array($curid, $actualPrice, $this->id));
				}
			}
			if(!$currencySet){
				$curid = fetchCurrency($current_user->id);
				$adb->pquery("update nectarcrm_products set currency_id=? where productid=?", array($curid, $this->id));
			}
		}

		$log->debug("Exiting from insertPriceInformation($tablename, $module) method ...");
	}

	function updateUnitPrice() {
		$prod_res = $this->db->pquery("select unit_price, currency_id from nectarcrm_products where productid=?", array($this->id));
		$prod_unit_price = $this->db->query_result($prod_res, 0, 'unit_price');
		$prod_base_currency = $this->db->query_result($prod_res, 0, 'currency_id');

		$query = "update nectarcrm_productcurrencyrel set actual_price=? where productid=? and currencyid=?";
		$params = array($prod_unit_price, $this->id, $prod_base_currency);
		$this->db->pquery($query, $params);
	}

	function insertIntoAttachment($id,$module)
	{
		global  $log,$adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;
		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				  if($_REQUEST[$fileindex.'_hidden'] != '')
					  $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				  else
					  $files['original_name'] = stripslashes($files['name']);
				  $files['original_name'] = str_replace('"','',$files['original_name']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		//Updating image information in main table of products
		$existingImageSql = 'SELECT name FROM nectarcrm_seattachmentsrel INNER JOIN nectarcrm_attachments ON
								nectarcrm_seattachmentsrel.attachmentsid = nectarcrm_attachments.attachmentsid LEFT JOIN nectarcrm_products ON
								nectarcrm_products.productid = nectarcrm_seattachmentsrel.crmid WHERE nectarcrm_seattachmentsrel.crmid = ?';
		$existingImages = $adb->pquery($existingImageSql,array($id));
		$numOfRows = $adb->num_rows($existingImages);
		$productImageMap = array();

		for ($i = 0; $i < $numOfRows; $i++) {
			$imageName = $adb->query_result($existingImages, $i, "name");
			array_push($productImageMap, decode_html($imageName));
		}
		$commaSeperatedFileNames = implode(",", $productImageMap);

		$adb->pquery('UPDATE nectarcrm_products SET imagename = ? WHERE productid = ?',array($commaSeperatedFileNames,$id));

		//Remove the deleted nectarcrm_attachments from db - Products
		if($module == 'Products' && $_REQUEST['del_file_list'] != '')
		{
			$del_file_list = explode("###",trim($_REQUEST['del_file_list'],"###"));
			foreach($del_file_list as $del_file_name)
			{
				$attach_res = $adb->pquery("select nectarcrm_attachments.attachmentsid from nectarcrm_attachments inner join nectarcrm_seattachmentsrel on nectarcrm_attachments.attachmentsid=nectarcrm_seattachmentsrel.attachmentsid where crmid=? and name=?", array($id,$del_file_name));
				$attachments_id = $adb->query_result($attach_res,0,'attachmentsid');

				$del_res1 = $adb->pquery("delete from nectarcrm_attachments where attachmentsid=?", array($attachments_id));
				$del_res2 = $adb->pquery("delete from nectarcrm_seattachmentsrel where attachmentsid=?", array($attachments_id));
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}



	/**	function used to get the list of leads which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_leads($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_leads(".$id.") method ...");
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

		$query = "SELECT nectarcrm_leaddetails.leadid, nectarcrm_crmentity.crmid, nectarcrm_leaddetails.firstname, nectarcrm_leaddetails.lastname, nectarcrm_leaddetails.company, nectarcrm_leadaddress.phone, nectarcrm_leadsubdetails.website, nectarcrm_leaddetails.email, case when (nectarcrm_users.user_name not like \"\") then nectarcrm_users.user_name else nectarcrm_groups.groupname end as user_name, nectarcrm_crmentity.smownerid, nectarcrm_products.productname, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_products.expiry_date
			FROM nectarcrm_leaddetails
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_leadaddress ON nectarcrm_leadaddress.leadaddressid = nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_leadsubdetails ON nectarcrm_leadsubdetails.leadsubscriptionid = nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.crmid=nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_products ON nectarcrm_seproductsrel.productid = nectarcrm_products.productid
			INNER JOIN nectarcrm_leadscf ON nectarcrm_leaddetails.leadid = nectarcrm_leadscf.leadid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.converted=0 AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_leads method ...");
		return $return_value;
	}

	/**	function used to get the list of accounts which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_accounts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_accounts(".$id.") method ...");
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

		$query = "SELECT nectarcrm_account.accountid, nectarcrm_crmentity.crmid, nectarcrm_account.accountname, nectarcrm_accountbillads.bill_city, nectarcrm_account.website, nectarcrm_account.phone, case when (nectarcrm_users.user_name not like \"\") then nectarcrm_users.user_name else nectarcrm_groups.groupname end as user_name, nectarcrm_crmentity.smownerid, nectarcrm_products.productname, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_products.expiry_date
			FROM nectarcrm_account
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid
			INNER JOIN nectarcrm_accountbillads ON nectarcrm_accountbillads.accountaddressid = nectarcrm_account.accountid
			LEFT JOIN nectarcrm_accountshipads ON nectarcrm_accountshipads.accountaddressid = nectarcrm_account.accountid
			INNER JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.crmid=nectarcrm_account.accountid
			INNER JOIN nectarcrm_products ON nectarcrm_seproductsrel.productid = nectarcrm_products.productid
			INNER JOIN nectarcrm_accountscf ON nectarcrm_account.accountid = nectarcrm_accountscf.accountid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_accounts method ...");
		return $return_value;
	}

	/**	function used to get the list of contacts which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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

		$query = "SELECT nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.title, nectarcrm_contactdetails.accountid, nectarcrm_contactdetails.email, nectarcrm_contactdetails.phone, nectarcrm_crmentity.crmid, case when (nectarcrm_users.user_name not like \"\") then nectarcrm_users.user_name else nectarcrm_groups.groupname end as user_name, nectarcrm_crmentity.smownerid, nectarcrm_products.productname, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_products.expiry_date,nectarcrm_account.accountname
			FROM nectarcrm_contactdetails
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.crmid=nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_contactaddress ON nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid
			INNER JOIN nectarcrm_contactsubdetails ON nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid
			INNER JOIN nectarcrm_customerdetails ON nectarcrm_contactdetails.contactid = nectarcrm_customerdetails.customerid
			INNER JOIN nectarcrm_contactscf ON nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid
			INNER JOIN nectarcrm_products ON nectarcrm_seproductsrel.productid = nectarcrm_products.productid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}


	/**	function used to get the list of potentials which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_potential.potentialid, nectarcrm_crmentity.crmid,
			nectarcrm_potential.potentialname, nectarcrm_account.accountname, nectarcrm_potential.related_to, nectarcrm_potential.contact_id,
			nectarcrm_potential.sales_stage, nectarcrm_potential.amount, nectarcrm_potential.closingdate,
			case when (nectarcrm_users.user_name not like '') then $userNameSql else
			nectarcrm_groups.groupname end as user_name, nectarcrm_crmentity.smownerid,
			nectarcrm_products.productname, nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price,
			nectarcrm_products.expiry_date FROM nectarcrm_potential
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid
			INNER JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.crmid = nectarcrm_potential.potentialid
			INNER JOIN nectarcrm_products ON nectarcrm_seproductsrel.productid = nectarcrm_products.productid
			INNER JOIN nectarcrm_potentialscf ON nectarcrm_potential.potentialid = nectarcrm_potentialscf.potentialid
			LEFT JOIN nectarcrm_account ON nectarcrm_potential.related_to = nectarcrm_account.accountid
			LEFT JOIN nectarcrm_contactdetails ON nectarcrm_potential.contact_id = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}

	/**	function used to get the list of tickets which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'product_id','readwrite') == '0') {
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
		$query = "SELECT  case when (nectarcrm_users.user_name not like \"\") then $userNameSql else nectarcrm_groups.groupname end as user_name, nectarcrm_users.id,
			nectarcrm_products.productid, nectarcrm_products.productname,
			nectarcrm_troubletickets.ticketid,
			nectarcrm_troubletickets.parent_id, nectarcrm_troubletickets.title,
			nectarcrm_troubletickets.status, nectarcrm_troubletickets.priority,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_crmentity.modifiedtime, nectarcrm_troubletickets.ticket_no
			FROM nectarcrm_troubletickets
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_troubletickets.ticketid
			LEFT JOIN nectarcrm_products
				ON nectarcrm_products.productid = nectarcrm_troubletickets.product_id
			LEFT JOIN nectarcrm_ticketcf ON nectarcrm_troubletickets.ticketid = nectarcrm_ticketcf.ticketid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_products.productid = ".$id;

		$log->debug("Exiting get_tickets method ...");

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_tickets method ...");
		return $return_value;
	}

	/**	function used to get the list of quotes which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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
		$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_quotes.*,
			nectarcrm_potential.potentialname,
			nectarcrm_account.accountname,
			nectarcrm_inventoryproductrel.productid,
			case when (nectarcrm_users.user_name not like '') then $userNameSql
				else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_quotes
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_quotes.quoteid
			INNER JOIN nectarcrm_inventoryproductrel
				ON nectarcrm_inventoryproductrel.id = nectarcrm_quotes.quoteid
			LEFT OUTER JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_quotes.accountid
			LEFT OUTER JOIN nectarcrm_potential
				ON nectarcrm_potential.potentialid = nectarcrm_quotes.potentialid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_quotescf
				ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid
			LEFT JOIN nectarcrm_quotesbillads
				ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid
			LEFT JOIN nectarcrm_quotesshipads
				ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_inventoryproductrel.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	}

	/**	function used to get the list of purchase orders which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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
		$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_purchaseorder.*,
			nectarcrm_products.productname,
			nectarcrm_inventoryproductrel.productid,
			case when (nectarcrm_users.user_name not like '') then $userNameSql
				else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_purchaseorder
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_purchaseorder.purchaseorderid
			INNER JOIN nectarcrm_inventoryproductrel
				ON nectarcrm_inventoryproductrel.id = nectarcrm_purchaseorder.purchaseorderid
			INNER JOIN nectarcrm_products
				ON nectarcrm_products.productid = nectarcrm_inventoryproductrel.productid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_purchaseordercf
				ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_pobillads
				ON nectarcrm_pobillads.pobilladdressid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_poshipads
				ON nectarcrm_poshipads.poshipaddressid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_purchase_orders method ...");
		return $return_value;
	}

	/**	function used to get the list of sales orders which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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
		$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_salesorder.*,
			nectarcrm_products.productname AS productname,
			nectarcrm_account.accountname,
			case when (nectarcrm_users.user_name not like '') then $userNameSql
				else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_salesorder
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_salesorder.salesorderid
			INNER JOIN nectarcrm_inventoryproductrel
				ON nectarcrm_inventoryproductrel.id = nectarcrm_salesorder.salesorderid
			INNER JOIN nectarcrm_products
				ON nectarcrm_products.productid = nectarcrm_inventoryproductrel.productid
			LEFT OUTER JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_salesorder.accountid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_salesordercf
				ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_invoice_recurring_info
				ON nectarcrm_invoice_recurring_info.start_period = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_sobillads
				ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_soshipads
				ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_products.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
		return $return_value;
	}

	/**	function used to get the list of invoices which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
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
		$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_invoice.*,
			nectarcrm_inventoryproductrel.quantity,
			nectarcrm_account.accountname,
			case when (nectarcrm_users.user_name not like '') then $userNameSql
				else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_invoice
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_invoice.invoiceid
			LEFT OUTER JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_invoice.accountid
			INNER JOIN nectarcrm_inventoryproductrel
				ON nectarcrm_inventoryproductrel.id = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_invoicecf
				ON nectarcrm_invoicecf.invoiceid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoicebillads
				ON nectarcrm_invoicebillads.invoicebilladdressid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_invoiceshipads
				ON nectarcrm_invoiceshipads.invoiceshipaddressid = nectarcrm_invoice.invoiceid
			LEFT JOIN nectarcrm_users
				ON  nectarcrm_users.id=nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_inventoryproductrel.productid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_invoices method ...");
		return $return_value;
	}

	/**	function used to get the list of pricebooks which are related to the product
	 *	@param int $id - product id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	function get_product_pricebooks($id, $cur_tab_id, $rel_tab_id, $actions=false)
	{
		global $log,$singlepane_view,$currentModule;
		$log->debug("Entering get_product_pricebooks(".$id.") method ...");

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
		require_once("modules/$related_module/$related_module.php");
		$focus = new $related_module();
		$singular_modname = vtlib_toSingular($related_module);

		$button = '';
		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes' && isPermitted($currentModule,'EditView',$id) == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_TO'). " ". getTranslatedString($related_module) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"AddProductToPriceBooks\";this.form.module.value=\"$currentModule\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_TO'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		if($singlepane_view == 'true')
			$returnset = '&return_module=Products&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Products&return_action=CallRelatedList&return_id='.$id;


		$query = "SELECT nectarcrm_crmentity.crmid,
			nectarcrm_pricebook.*,
			nectarcrm_pricebookproductrel.productid as prodid
			FROM nectarcrm_pricebook
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_pricebook.pricebookid
			INNER JOIN nectarcrm_pricebookproductrel
				ON nectarcrm_pricebookproductrel.pricebookid = nectarcrm_pricebook.pricebookid
			INNER JOIN nectarcrm_pricebookcf
				ON nectarcrm_pricebookcf.pricebookid = nectarcrm_pricebook.pricebookid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_pricebookproductrel.productid = ".$id;
		$log->debug("Exiting get_product_pricebooks method ...");

		$return_value = GetRelatedList($currentModule, $related_module, $focus, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**	function used to get the number of vendors which are related to the product
	 *	@param int $id - product id
	 *	@return int number of rows - return the number of products which do not have relationship with vendor
	 */
	function product_novendor()
	{
		global $log;
		$log->debug("Entering product_novendor() method ...");
		$query = "SELECT nectarcrm_products.productname, nectarcrm_crmentity.deleted
			FROM nectarcrm_products
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_products.vendor_id is NULL";
		$result=$this->db->pquery($query, array());
		$log->debug("Exiting product_novendor method ...");
		return $this->db->num_rows($result);
	}

	/**
	* Function to get Product's related Products
	* @param  integer   $id      - productid
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

		if($actions && $this->ismember_check() === 0) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$query = "SELECT nectarcrm_products.productid, nectarcrm_products.productname,
			nectarcrm_products.productcode, nectarcrm_products.commissionrate,
			nectarcrm_seproductsrel.quantity AS qty_per_unit, nectarcrm_products.unit_price, 
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid
			FROM nectarcrm_products
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
			INNER JOIN nectarcrm_productcf
				ON nectarcrm_products.productid = nectarcrm_productcf.productid
			LEFT JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.crmid = nectarcrm_products.productid AND nectarcrm_seproductsrel.setype='Products'
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_seproductsrel.productid = $id ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/**
	* Function to get Product's related Products
	* @param  integer   $id      - productid
	* returns related Products record in array format
	*/
	function get_parent_products($id)
	{
		global $log, $singlepane_view;
				$log->debug("Entering get_products(".$id.") method ...");

		global $app_strings;

		$focus = new Products();

		$button = '';

		if(isPermitted("Products",1,"") == 'yes')
		{
			$button .= '<input title="'.$app_strings['LBL_NEW_PRODUCT'].'" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Products\';this.form.return_module.value=\'Products\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_PRODUCT'].'">&nbsp;';
		}
		if($singlepane_view == 'true')
			$returnset = '&return_module=Products&return_action=DetailView&is_parent=1&return_id='.$id;
		else
			$returnset = '&return_module=Products&return_action=CallRelatedList&is_parent=1&return_id='.$id;

		$query = "SELECT nectarcrm_products.productid, nectarcrm_products.productname,
			nectarcrm_products.productcode, nectarcrm_products.commissionrate,
			nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid
			FROM nectarcrm_products
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
			INNER JOIN nectarcrm_seproductsrel ON nectarcrm_seproductsrel.productid = nectarcrm_products.productid AND nectarcrm_seproductsrel.setype='Products'
			INNER JOIN nectarcrm_productcf ON nectarcrm_products.productid = nectarcrm_productcf.productid

			WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_seproductsrel.crmid = $id ";

		$log->debug("Exiting get_products method ...");
		return GetRelatedList('Products','Products',$focus,$query,$button,$returnset);
	}

	/**	function used to get the export query for product
	 *	@param reference $where - reference of the where variable which will be added with the query
	 *	@return string $query - return the query which will give the list of products to export
	 */
	function create_export_query($where)
	{
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Products", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list FROM ".$this->table_name ."
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
			LEFT JOIN nectarcrm_productcf
				ON nectarcrm_products.productid = nectarcrm_productcf.productid
			LEFT JOIN nectarcrm_vendor
				ON nectarcrm_vendor.vendorid = nectarcrm_products.vendor_id";

		$query .= " LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
		$query .= " LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id AND nectarcrm_users.status='Active'";
		$query .= $this->getNonAdminAccessControlQuery('Products',$current_user);
		$where_auto = " nectarcrm_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/** Function to check if the product is parent of any other product
	*/
	function isparent_check(){
		global $adb;
		$isparent_query = $adb->pquery(getListQuery("Products")." AND (nectarcrm_products.productid IN (SELECT productid from nectarcrm_seproductsrel WHERE nectarcrm_seproductsrel.productid = ? AND nectarcrm_seproductsrel.setype='Products'))",array($this->id));
		$isparent = $adb->num_rows($isparent_query);
		return $isparent;
	}

	/** Function to check if the product is member of other product
	*/
	function ismember_check(){
		global $adb;
		$ismember_query = $adb->pquery(getListQuery("Products")." AND (nectarcrm_products.productid IN (SELECT crmid from nectarcrm_seproductsrel WHERE nectarcrm_seproductsrel.crmid = ? AND nectarcrm_seproductsrel.setype='Products'))",array($this->id));
		$ismember = $adb->num_rows($ismember_query);
		return $ismember;
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

		$rel_table_arr = Array("HelpDesk"=>"nectarcrm_troubletickets","Products"=>"nectarcrm_seproductsrel","Attachments"=>"nectarcrm_seattachmentsrel",
				"Quotes"=>"nectarcrm_inventoryproductrel","PurchaseOrder"=>"nectarcrm_inventoryproductrel","SalesOrder"=>"nectarcrm_inventoryproductrel",
				"Invoice"=>"nectarcrm_inventoryproductrel","PriceBooks"=>"nectarcrm_pricebookproductrel","Leads"=>"nectarcrm_seproductsrel",
				"Accounts"=>"nectarcrm_seproductsrel","Potentials"=>"nectarcrm_seproductsrel","Contacts"=>"nectarcrm_seproductsrel",
				"Documents"=>"nectarcrm_senotesrel",'Assets'=>'nectarcrm_assets',);

		$tbl_field_arr = Array("nectarcrm_troubletickets"=>"ticketid","nectarcrm_seproductsrel"=>"crmid","nectarcrm_seattachmentsrel"=>"attachmentsid",
				"nectarcrm_inventoryproductrel"=>"id","nectarcrm_pricebookproductrel"=>"pricebookid","nectarcrm_seproductsrel"=>"crmid",
				"nectarcrm_senotesrel"=>"notesid",'nectarcrm_assets'=>'assetsid');

		$entity_tbl_field_arr = Array("nectarcrm_troubletickets"=>"product_id","nectarcrm_seproductsrel"=>"crmid","nectarcrm_seattachmentsrel"=>"crmid",
				"nectarcrm_inventoryproductrel"=>"productid","nectarcrm_pricebookproductrel"=>"productid","nectarcrm_seproductsrel"=>"productid",
				"nectarcrm_senotesrel"=>"crmid",'nectarcrm_assets'=>'product');

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

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryplanner) {
		global $current_user;
		$matrix = $queryplanner->newDependencyMatrix();

		$matrix->setDependency("nectarcrm_crmentityProducts",array("nectarcrm_groupsProducts","nectarcrm_usersProducts","nectarcrm_lastModifiedByProducts"));
		//query planner Support  added
		if (!$queryplanner->requireTable('nectarcrm_products', $matrix)) {
			return '';
		}
		$matrix->setDependency("nectarcrm_products",array("innerProduct","nectarcrm_crmentityProducts","nectarcrm_productcf","nectarcrm_vendorRelProducts"));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_products","productid", $queryplanner);
		if ($queryplanner->requireTable("innerProduct")){
			$query .= " LEFT JOIN (
					SELECT nectarcrm_products.productid,
							(CASE WHEN (nectarcrm_products.currency_id = 1 ) THEN nectarcrm_products.unit_price
								ELSE (nectarcrm_products.unit_price / nectarcrm_currency_info.conversion_rate) END
							) AS actual_unit_price
					FROM nectarcrm_products
					LEFT JOIN nectarcrm_currency_info ON nectarcrm_products.currency_id = nectarcrm_currency_info.id
					LEFT JOIN nectarcrm_productcurrencyrel ON nectarcrm_products.productid = nectarcrm_productcurrencyrel.productid
					AND nectarcrm_productcurrencyrel.currencyid = ". $current_user->currency_id . "
				) AS innerProduct ON innerProduct.productid = nectarcrm_products.productid";
		}
		if ($queryplanner->requireTable("nectarcrm_crmentityProducts")){
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityProducts on nectarcrm_crmentityProducts.crmid=nectarcrm_products.productid and nectarcrm_crmentityProducts.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_productcf")){
			$query .= " left join nectarcrm_productcf on nectarcrm_products.productid = nectarcrm_productcf.productid";
		}
			if ($queryplanner->requireTable("nectarcrm_groupsProducts")){
			$query .= " left join nectarcrm_groups as nectarcrm_groupsProducts on nectarcrm_groupsProducts.groupid = nectarcrm_crmentityProducts.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersProducts")){
			$query .= " left join nectarcrm_users as nectarcrm_usersProducts on nectarcrm_usersProducts.id = nectarcrm_crmentityProducts.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_vendorRelProducts")){
			$query .= " left join nectarcrm_vendor as nectarcrm_vendorRelProducts on nectarcrm_vendorRelProducts.vendorid = nectarcrm_products.vendor_id";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByProducts")){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedByProducts on nectarcrm_lastModifiedByProducts.id = nectarcrm_crmentityProducts.modifiedby ";
		}
		if ($queryplanner->requireTable("nectarcrm_createdbyProducts")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyProducts on nectarcrm_createdbyProducts.id = nectarcrm_crmentityProducts.smcreatorid ";
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
			"HelpDesk" => array("nectarcrm_troubletickets"=>array("product_id","ticketid"),"nectarcrm_products"=>"productid"),
			"Quotes" => array("nectarcrm_inventoryproductrel"=>array("productid","id"),"nectarcrm_products"=>"productid"),
			"PurchaseOrder" => array("nectarcrm_inventoryproductrel"=>array("productid","id"),"nectarcrm_products"=>"productid"),
			"SalesOrder" => array("nectarcrm_inventoryproductrel"=>array("productid","id"),"nectarcrm_products"=>"productid"),
			"Invoice" => array("nectarcrm_inventoryproductrel"=>array("productid","id"),"nectarcrm_products"=>"productid"),
			"Leads" => array("nectarcrm_seproductsrel"=>array("productid","crmid"),"nectarcrm_products"=>"productid"),
			"Accounts" => array("nectarcrm_seproductsrel"=>array("productid","crmid"),"nectarcrm_products"=>"productid"),
			"Contacts" => array("nectarcrm_seproductsrel"=>array("productid","crmid"),"nectarcrm_products"=>"productid"),
			"Potentials" => array("nectarcrm_seproductsrel"=>array("productid","crmid"),"nectarcrm_products"=>"productid"),
			"Products" => array("nectarcrm_products"=>array("productid","product_id"),"nectarcrm_products"=>"productid"),
			"PriceBooks" => array("nectarcrm_pricebookproductrel"=>array("productid","pricebookid"),"nectarcrm_products"=>"productid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_products"=>"productid"),
		);
		return $rel_tables[$secmodule];
	}

	function deleteProduct2ProductRelation($record,$return_id,$is_parent){
		global $adb;
		if($is_parent==0){
			$sql = "delete from nectarcrm_seproductsrel WHERE crmid = ? AND productid = ?";
			$adb->pquery($sql, array($record,$return_id));
		} else {
			$sql = "delete from nectarcrm_seproductsrel WHERE crmid = ? AND productid = ?";
			$adb->pquery($sql, array($return_id,$record));
		}
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		//Backup Campaigns-Product Relation
		$cmp_q = 'SELECT campaignid FROM nectarcrm_campaign WHERE product_id = ?';
		$cmp_res = $this->db->pquery($cmp_q, array($id));
		if ($this->db->num_rows($cmp_res) > 0) {
			$cmp_ids_list = array();
			for($k=0;$k < $this->db->num_rows($cmp_res);$k++)
			{
				$cmp_ids_list[] = $this->db->query_result($cmp_res,$k,"campaignid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_campaign', 'product_id', 'campaignid', implode(",", $cmp_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//we have to update the product_id as null for the campaigns which are related to this product
		$this->db->pquery('UPDATE nectarcrm_campaign SET product_id=0 WHERE product_id = ?', array($id));

		// restoring products relations
		$productRelRB = $this->db->pquery('SELECT * FROM nectarcrm_seproductsrel WHERE productid = ?' ,array($id));
		$rows = $this->db->num_rows($productRelRB);
		if($this->db->num_rows($productRelRB)) {
			for($i=0; $i<$rows; $i++) {
				$crmid = $this->db->query_result($productRelRB, $i, "crmid");
				$params = array($id, RB_RECORD_DELETED, 'nectarcrm_seproductsrel', 'productid', 'crmid', $crmid);
				$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb(entityid, action, rel_table, rel_column, ref_column, related_crm_ids)
						VALUES (?,?,?,?,?,?)', $params);
			}
		}
		$this->db->pquery('DELETE from nectarcrm_seproductsrel WHERE productid=? or crmid=?',array($id,$id));

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Calendar') {
			$sql = 'DELETE FROM nectarcrm_seactivityrel WHERE crmid = ? AND activityid = ?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Leads' || $return_module == 'Contacts' || $return_module == 'Potentials') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE productid = ? AND crmid = ?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Vendors') {
			$sql = 'UPDATE nectarcrm_products SET vendor_id = ? WHERE productid = ?';
			$this->db->pquery($sql, array(null, $id));
		} elseif($return_module == 'Accounts') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE productid = ? AND (crmid = ? OR crmid IN (SELECT contactid FROM nectarcrm_contactdetails WHERE accountid=?))';
			$param = array($id, $return_id,$return_id);
			$this->db->pquery($sql, $param);
		} elseif($return_module == 'Documents') {
			$sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		$qtysList = array();
		if ($otherParams && is_array($otherParams['quantities'])) {
			$qtysList = $otherParams['quantities'];
		}

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			$qty = 0;
			if (array_key_exists($with_crmid, $qtysList)) {
				$qty = (float) $qtysList[$with_crmid];
			}
			if (!$qty) {
				$qty = 1;
			}

			if (in_array($with_module, array('Leads', 'Accounts', 'Contacts', 'Potentials', 'Products'))) {
				$query = $adb->pquery("SELECT * FROM nectarcrm_seproductsrel WHERE crmid=? AND productid=?", array($crmid, $with_crmid));
				if($adb->num_rows($query) == 0) {
					$adb->pquery('INSERT INTO nectarcrm_seproductsrel VALUES (?,?,?,?)', array($with_crmid, $crmid, $with_module, $qty));
				}
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

}
?>
