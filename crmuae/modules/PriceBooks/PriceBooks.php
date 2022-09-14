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

class PriceBooks extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "nectarcrm_pricebook";
	var $table_index= 'pricebookid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_pricebook','nectarcrm_pricebookcf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_pricebook'=>'pricebookid','nectarcrm_pricebookcf'=>'pricebookid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_pricebookcf', 'pricebookid');
	var $column_fields = Array();

	var $sortby_fields = Array('bookname');

        // This is the list of fields that are in the lists.
	var $list_fields = Array(
                                'Price Book Name'=>Array('pricebook'=>'bookname'),
                                'Active'=>Array('pricebook'=>'active')
                                );

	var $list_fields_name = Array(
                                        'Price Book Name'=>'bookname',
                                        'Active'=>'active'
                                     );
	var $list_link_field= 'bookname';

	var $search_fields = Array(
                                'Price Book Name'=>Array('pricebook'=>'bookname')
                                );
	var $search_fields_name = Array(
                                        'Price Book Name'=>'bookname'
                                     );

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'bookname';
	var $default_sort_order = 'ASC';

	var $mandatory_fields = Array('bookname','currency_id','pricebook_no','createdtime' ,'modifiedtime');

	// For Alphabetical search
	var $def_basicsearch_col = 'bookname';

	/**	Constructor which will set the column_fields in this object
	 */
	function PriceBooks() {
		$this->log =LoggerManager::getLogger('pricebook');
		$this->log->debug("Entering PriceBooks() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('PriceBooks');
		$this->log->debug("Exiting PriceBook method ...");
	}

	function save_module($module)
	{
		// Update the list prices in the price book with the unit price, if the Currency has been changed
		$this->updateListPrices();
	}

	/* Function to Update the List prices for all the products of a current price book
	   with its Unit price, if the Currency for Price book has changed. */
	function updateListPrices() {
		global $log, $adb;
		$log->debug("Entering function updateListPrices...");
		$pricebook_currency = $this->column_fields['currency_id'];
		$prod_res = $adb->pquery("select * from nectarcrm_pricebookproductrel where pricebookid=? AND usedcurrency != ?",
							array($this->id, $pricebook_currency));
		$numRows = $adb->num_rows($prod_res);

		for($i=0;$i<$numRows;$i++) {
			$product_id = $adb->query_result($prod_res,$i,'productid');
			$list_price = $adb->query_result($prod_res,$i,'listprice');
			$used_currency = $adb->query_result($prod_res,$i,'usedcurrency');
			$product_currency_info = getCurrencySymbolandCRate($used_currency);
			$product_conv_rate = $product_currency_info['rate'];
			$pricebook_currency_info = getCurrencySymbolandCRate($pricebook_currency);
			$pb_conv_rate = $pricebook_currency_info['rate'];
			$conversion_rate = $pb_conv_rate / $product_conv_rate;
			$computed_list_price = $list_price * $conversion_rate;

			$query = "update nectarcrm_pricebookproductrel set listprice=?, usedcurrency=? where pricebookid=? and productid=?";
			$params = array($computed_list_price, $pricebook_currency, $this->id, $product_id);
			$adb->pquery($query, $params);
		}
		$log->debug("Exiting function updateListPrices...");
	}

	/**	function used to get the products which are related to the pricebook
	 *	@param int $id - pricebook id
         *      @return array - return an array which will be returned from the function getPriceBookRelatedProducts
        **/
	function get_pricebook_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_pricebook_products(".$id.") method ...");
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddProductsToPriceBook';this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode, nectarcrm_products.commissionrate,
						nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
						nectarcrm_pricebookproductrel.listprice
				FROM nectarcrm_products
				INNER JOIN nectarcrm_pricebookproductrel ON nectarcrm_products.productid = nectarcrm_pricebookproductrel.productid
				INNER JOIN nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_products.productid
				INNER JOIN nectarcrm_pricebook on nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid '
				. getNonAdminAccessControlQuery($related_module, $current_user) .'
				WHERE nectarcrm_pricebook.pricebookid = '.$id.' and nectarcrm_crmentity.deleted = 0';

		$this->retrieve_entity_info($id,$this_module);
		$return_value = getPriceBookRelatedProducts($query,$this,$returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_pricebook_products method ...");
		return $return_value;
	}

	/**	function used to get the services which are related to the pricebook
	 *	@param int $id - pricebook id
         *      @return array - return an array which will be returned from the function getPriceBookRelatedServices
        **/
	function get_pricebook_services($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_pricebook_services(".$id.") method ...");
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddServicesToPriceBook';this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT nectarcrm_service.serviceid, nectarcrm_service.servicename, nectarcrm_service.commissionrate,
					nectarcrm_service.qty_per_unit, nectarcrm_service.unit_price, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
					nectarcrm_pricebookproductrel.listprice
			FROM nectarcrm_service
			INNER JOIN nectarcrm_pricebookproductrel on nectarcrm_service.serviceid = nectarcrm_pricebookproductrel.productid
			INNER JOIN nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_service.serviceid
			INNER JOIN nectarcrm_pricebook on nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid '
			. getNonAdminAccessControlQuery($related_module, $current_user) .'
			WHERE nectarcrm_pricebook.pricebookid = '.$id.' and nectarcrm_crmentity.deleted = 0';

		$this->retrieve_entity_info($id,$this_module);
		$return_value = $other->getPriceBookRelatedServices($query,$this,$returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_pricebook_services method ...");
		return $return_value;
	}

	/**	function used to get whether the pricebook has related with a product or not
	 *	@param int $id - product id
	 *	@return true or false - if there are no pricebooks available or associated pricebooks for the product is equal to total number of pricebooks then return false, else return true
	 */
	function get_pricebook_noproduct($id)
	{
		global $log;
		$log->debug("Entering get_pricebook_noproduct(".$id.") method ...");

		$query = "select nectarcrm_crmentity.crmid, nectarcrm_pricebook.* from nectarcrm_pricebook inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_pricebook.pricebookid where nectarcrm_crmentity.deleted=0";
		$result = $this->db->pquery($query, array());
		$no_count = $this->db->num_rows($result);
		if($no_count !=0)
		{
       	 	$pb_query = 'select nectarcrm_crmentity.crmid, nectarcrm_pricebook.pricebookid,nectarcrm_pricebookproductrel.productid from nectarcrm_pricebook inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_pricebook.pricebookid inner join nectarcrm_pricebookproductrel on nectarcrm_pricebookproductrel.pricebookid=nectarcrm_pricebook.pricebookid where nectarcrm_crmentity.deleted=0 and nectarcrm_pricebookproductrel.productid=?';
			$result_pb = $this->db->pquery($pb_query, array($id));
			if($no_count == $this->db->num_rows($result_pb))
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return false;
			}
			elseif($this->db->num_rows($result_pb) == 0)
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return true;
			}
			elseif($this->db->num_rows($result_pb) < $no_count)
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return true;
			}
		}
		else
		{
			$log->debug("Exiting get_pricebook_noproduct method ...");
			return false;
		}
	}

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	function generateReportsQuery($module,$queryplanner){
	 			$moduletable = $this->table_name;
	 			$moduleindex = $this->table_index;
				$modulecftable = $this->customFieldTable[0];
				$modulecfindex = $this->customFieldTable[1];

				$cfquery = '';
				if(isset($modulecftable) && $queryplanner->requireTable($modulecftable) ){
					$cfquery = "inner join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$moduletable.$moduleindex";
				}

	 			$query = "from $moduletable $cfquery
					inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=$moduletable.$moduleindex";
				if ($queryplanner->requireTable("nectarcrm_currency_info$module")){
				    $query .= "  left join nectarcrm_currency_info as nectarcrm_currency_info$module on nectarcrm_currency_info$module.id = $moduletable.currency_id";
				}
				if ($queryplanner->requireTable("nectarcrm_groups$module")){
				    $query .= " left join nectarcrm_groups as nectarcrm_groups$module on nectarcrm_groups$module.groupid = nectarcrm_crmentity.smownerid";
				}
				if ($queryplanner->requireTable("nectarcrm_users$module")){
				    $query .= " left join nectarcrm_users as nectarcrm_users$module on nectarcrm_users$module.id = nectarcrm_crmentity.smownerid";
				}
				$query .= " left join nectarcrm_groups on nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
				$query .= " left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid";

				if ($queryplanner->requireTable("nectarcrm_lastModifiedByPriceBooks")){
				    $query .= " left join nectarcrm_users as nectarcrm_lastModifiedByPriceBooks on nectarcrm_lastModifiedByPriceBooks.id = nectarcrm_crmentity.modifiedby ";
				}
                if($queryplanner->requireTable('nectarcrm_createdby'.$module)){
                    $query .= " left join nectarcrm_users as nectarcrm_createdby".$module." on nectarcrm_createdby".$module.".id = nectarcrm_crmentity.smcreatorid";
                }
				return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryplanner) {

		$matrix = $queryplanner->newDependencyMatrix();

		$matrix->setDependency("nectarcrm_crmentityPriceBooks",array("nectarcrm_usersPriceBooks","nectarcrm_groupsPriceBooks"));
		if (!$queryplanner->requireTable('nectarcrm_pricebook', $matrix)) {
			return '';
		}
        $matrix->setDependency("nectarcrm_pricebook",array("nectarcrm_crmentityPriceBooks","nectarcrm_currency_infoPriceBooks"));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_pricebook","pricebookid", $queryplanner);
		// TODO Support query planner
		if ($queryplanner->requireTable("nectarcrm_crmentityPriceBooks",$matrix)){
		$query .=" left join nectarcrm_crmentity as nectarcrm_crmentityPriceBooks on nectarcrm_crmentityPriceBooks.crmid=nectarcrm_pricebook.pricebookid and nectarcrm_crmentityPriceBooks.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_currency_infoPriceBooks")){
		$query .=" left join nectarcrm_currency_info as nectarcrm_currency_infoPriceBooks on nectarcrm_currency_infoPriceBooks.id = nectarcrm_pricebook.currency_id";
		}
		if ($queryplanner->requireTable("nectarcrm_usersPriceBooks")){
		    $query .=" left join nectarcrm_users as nectarcrm_usersPriceBooks on nectarcrm_usersPriceBooks.id = nectarcrm_crmentityPriceBooks.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsPriceBooks")){
		    $query .=" left join nectarcrm_groups as nectarcrm_groupsPriceBooks on nectarcrm_groupsPriceBooks.groupid = nectarcrm_crmentityPriceBooks.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByPriceBooks")){
		    $query .=" left join nectarcrm_users as nectarcrm_lastModifiedByPriceBooks on nectarcrm_lastModifiedByPriceBooks.id = nectarcrm_crmentityPriceBooks.smownerid";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyPriceBooks")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyPriceBooks on nectarcrm_createdbyPriceBooks.id = nectarcrm_crmentityPriceBooks.smcreatorid ";
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
			"Products" => array("nectarcrm_pricebookproductrel"=>array("pricebookid","productid"),"nectarcrm_pricebook"=>"pricebookid"),
			"Services" => array("nectarcrm_pricebookproductrel"=>array("pricebookid","productid"),"nectarcrm_pricebook"=>"pricebookid"),
		);
		return $rel_tables[$secmodule];
	}

	function createRecords($obj){
		global $adb;
		$moduleName = $obj->module;
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, $obj->user);
		$moduleMeta = $moduleHandler->getMeta();
		$moduleObjectId = $moduleMeta->getEntityId();
		$moduleFields = $moduleMeta->getModuleFields();
		$focus = CRMEntity::getInstance($moduleName);
        $moduleSubject = 'bookname';

		$tableName = Import_Utils_Helper::getDbTableName($obj->user);
		$sql = 'SELECT * FROM ' . $tableName . ' WHERE status = '. Import_Data_Action::$IMPORT_RECORD_NONE .' GROUP BY '. $moduleSubject;

		if($obj->batchImport) {
			$importBatchLimit = getImportBatchLimit();
			$sql .= ' LIMIT '. $importBatchLimit;
		}
		$result = $adb->query($sql);
		$numberOfRecords = $adb->num_rows($result);

		if ($numberOfRecords <= 0) {
			return;
		}
		$bookNameList = array();
		$fieldMapping = $obj->fieldMapping;
		$fieldColumnMapping = $moduleMeta->getFieldColumnMapping();
		for ($i = 0; $i < $numberOfRecords; ++$i) {
			$row = $adb->raw_query_result_rowdata($result, $i);
			$rowId = $row['id'];
			$subject = $row['bookname'];
			$entityInfo = null;
			$fieldData = array();
			$subject = str_replace("\\", "\\\\", $subject);
			$subject = str_replace('"', '""', $subject);
			$sql = 'SELECT * FROM ' . $tableName . ' WHERE status = '. Import_Data_Action::$IMPORT_RECORD_NONE .' AND '. $moduleSubject . ' = "'. $subject .'"';
			$subjectResult = $adb->query($sql);
			$count = $adb->num_rows($subjectResult);
			$subjectRowIDs = $fieldArray = $productList = array();
			for ($j = 0; $j < $count; ++$j) {
				$subjectRow = $adb->raw_query_result_rowdata($subjectResult, $j);
				array_push($subjectRowIDs, $subjectRow['id']);
				$productList[$j]['relatedto'] = $subjectRow['relatedto'];
				$productList[$j]['listprice'] = $subjectRow['listprice'];
			}
			foreach ($fieldMapping as $fieldName => $index) {
				$fieldData[$fieldName] = $row[strtolower($fieldName)];
			}

            $entityInfo = $this->importRecord($obj, $fieldData, $productList);
            unset($productList);
			if ($entityInfo == null) {
                $entityInfo = array('id' => null, 'status' => Import_Data_Action::$IMPORT_RECORD_FAILED);
            } else if(!$entityInfo['status']){
                $entityInfo['status'] = Import_Data_Action::$IMPORT_RECORD_CREATED;
            }

                $entityIdComponents = vtws_getIdComponents($entityInfo['id']);
                $recordId = $entityIdComponents[1];
                if(!empty($recordId)) {
                    $entityfields = getEntityFieldNames($moduleName);
                    $label = '';
                    if(is_array($entityfields['fieldname'])){
                        foreach($entityfields['fieldname'] as $field){
                            $label .= $fieldData[$field]." ";
                        }
                    }else {
                        $label = $fieldData[$entityfields['fieldname']];
                    }

                    $adb->pquery('UPDATE nectarcrm_crmentity SET label=? WHERE crmid=?', array(trim($label), $recordId));
                    //updating solr while import records
                    $recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
                    $focus = $recordModel->getEntity();
                    $focus->id = $recordId;
                    $focus->column_fields = $fieldData;
                    $this->entityData[] = VTEntityData::fromCRMEntity($focus);
                }

                $label = trim($label);
                $adb->pquery('UPDATE nectarcrm_crmentity SET label=? WHERE crmid=?', array($label, $recordId));
                //Creating entity data of updated records for post save events
                if ($entityInfo['status'] !== Import_Data_Action::$IMPORT_RECORD_CREATED) {
                    $recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
                    $focus = $recordModel->getEntity();
                    $focus->id = $recordId;
                    $focus->column_fields = $entityInfo;
                    $this->entitydata[] = VTEntityData::fromCRMEntity($focus);
                }
				
			foreach($subjectRowIDs as $id){
				$obj->importedRecordInfo[$id] = $entityInfo;
				$obj->updateImportStatus($id, $entityInfo);
			}
		}

        $obj->entitydata = null;
		$result = null;
		return true;
	}

	function importRecord($obj, $fieldData, $productList) {
		$moduleName = 'PriceBooks';
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, $obj->user);
		$moduleMeta = $moduleHandler->getMeta();
		unset($fieldData['listprice']); unset($fieldData['relatedto']);
		$fieldData = $obj->transformForImport($fieldData, $moduleMeta);
		try{
			$entityInfo = vtws_create($moduleName, $fieldData, $obj->user);
			if($entityInfo && $productList){
				$this->relatePriceBookWithProduct($entityInfo, $productList);
			}
		} catch (Exception $e){
		}
		$entityInfo['status'] = $obj->getImportRecordStatus('created');
		return $entityInfo;
	}

	function relatePriceBookWithProduct($entityinfo, $productList) {
		if(count($productList) > 0){
			foreach($productList as $product){
				if(!$product['relatedto'])
					continue;
				$productName = $product['relatedto'];
				$productName = explode('::::', $productName);
				$productId = getEntityId($productName[0], $productName[1]);
                $presence = isRecordExists($productId);
                if($presence){
                    $productInstance = nectarcrm_Record_Model::getInstanceById($productId);
                    $pricebookId = vtws_getIdComponents($entityinfo['id']);
                    if($productInstance){
                        $recordModel = nectarcrm_Record_Model::getInstanceById($pricebookId[1]);
                        $recordModel->updateListPrice($productId, $product['listprice']);
                    }
                }
			}
		}
	}

	function getGroupQuery($tableName){
		return 'SELECT status FROM '.$tableName.' GROUP BY bookname';
	}
}
?>
