<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PriceBooks_Module_Model extends nectarcrm_Module_Model {

	/**
	 * Function returns query for PriceBook-Product relation
	 * @param <nectarcrm_Record_Model> $recordModel
	 * @param <nectarcrm_Record_Model> $relatedModuleModel
	 * @return <String>
	 */
	function get_pricebook_products($recordModel, $relatedModuleModel) {
		$query = 'SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode, nectarcrm_products.commissionrate,
						nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
						nectarcrm_pricebookproductrel.listprice
				FROM nectarcrm_products
				INNER JOIN nectarcrm_pricebookproductrel ON nectarcrm_products.productid = nectarcrm_pricebookproductrel.productid
				INNER JOIN nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_products.productid
				INNER JOIN nectarcrm_pricebook on nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid
				INNER JOIN nectarcrm_productcf on nectarcrm_productcf.productid = nectarcrm_products.productid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid '
				. Users_Privileges_Model::getNonAdminAccessControlQuery($relatedModuleModel->getName()) .'
				WHERE nectarcrm_pricebook.pricebookid = '.$recordModel->getId().' and nectarcrm_crmentity.deleted = 0';
		return $query;
	}


	/**
	 * Function returns query for PriceBooks-Services Relationship
	 * @param <nectarcrm_Record_Model> $recordModel
	 * @param <nectarcrm_Record_Model> $relatedModuleModel
	 * @return <String>
	 */
	function get_pricebook_services($recordModel, $relatedModuleModel) {
		$query = 'SELECT nectarcrm_service.serviceid, nectarcrm_service.servicename, nectarcrm_service.service_no, nectarcrm_service.commissionrate,
					nectarcrm_service.qty_per_unit, nectarcrm_service.unit_price, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
					nectarcrm_pricebookproductrel.listprice
			FROM nectarcrm_service
			INNER JOIN nectarcrm_pricebookproductrel on nectarcrm_service.serviceid = nectarcrm_pricebookproductrel.productid
			INNER JOIN nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_service.serviceid
			INNER JOIN nectarcrm_pricebook on nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid
			INNER JOIN nectarcrm_servicecf on nectarcrm_servicecf.serviceid = nectarcrm_service.serviceid
			LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid '
			. Users_Privileges_Model::getNonAdminAccessControlQuery($relatedModuleModel->getName()) .'
			WHERE nectarcrm_pricebook.pricebookid = '.$recordModel->getId().' and nectarcrm_crmentity.deleted = 0';
		return $query;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery, $currencyId = false) {
		$relatedModulesList = array('Products', 'Services');
		if (in_array($sourceModule, $relatedModulesList)) {
			$pos = stripos($listQuery, ' where ');
			if ($currencyId && in_array($field, array('productid', 'serviceid'))) {
				$condition = " nectarcrm_pricebook.pricebookid IN (SELECT pricebookid FROM nectarcrm_pricebookproductrel WHERE productid = $record)
								AND nectarcrm_pricebook.currency_id = $currencyId AND nectarcrm_pricebook.active = 1";
			} else if($field == 'productsRelatedList') {
				$condition = "nectarcrm_pricebook.pricebookid NOT IN (SELECT pricebookid FROM nectarcrm_pricebookproductrel WHERE productid = $record)
								AND nectarcrm_pricebook.active = 1";
			}
			if ($pos) {
				$split = preg_split('/ where /i', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery . ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}
	
	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return false;
	}
	
	/**
	 * Funtion that returns fields that will be showed in the record selection popup
	 * @return <Array of fields>
	 */
	public function getPopupViewFieldsList() {
		$popupFileds = $this->getSummaryViewFieldsList();
		$reqPopUpFields = array('Currency' => 'currency_id'); 
		foreach ($reqPopUpFields as $fieldLabel => $fieldName) {
			$fieldModel = nectarcrm_Field_Model::getInstance($fieldName,$this); 
			if ($fieldModel->getPermissions('readwrite')) { 
				$popupFileds[$fieldName] = $fieldModel; 
			}
		}
		return array_keys($popupFileds);
	}
    
    /**
	* Function is used to give links in the All menu bar
	*/
	public function getQuickMenuModels() {
		if($this->isEntityModule()) {
			$moduleName = $this->getName();
			$listViewModel = nectarcrm_ListView_Model::getCleanInstance($moduleName);
			$basicListViewLinks = $listViewModel->getBasicLinks();
		}
        
		if($basicListViewLinks) {
			foreach($basicListViewLinks as $basicListViewLink) {
				if(is_array($basicListViewLink)) {
					$links[] = nectarcrm_Link_Model::getInstanceFromValues($basicListViewLink);
				} else if(is_a($basicListViewLink, 'nectarcrm_Link_Model')) {
					$links[] = $basicListViewLink;
				}
			}
		}
		return $links;
	}

	/*
     * Function to get supported utility actions for a module
	 */
	function getUtilityActionsNames() {
        return array('Import', 'Export');
    }

	/**
	 * Function returns export query - deprecated
	 * @param <String> $where
	 * @return <String> export query
	 */
	public function getExportQuery($focus, $query) {
		$baseTableName = $focus->table_name;
		$splitQuery = preg_split('/ FROM /i', $query, 2);
		$columnFields = explode(',', $splitQuery[0]);
		foreach ($columnFields as &$value) {
			if(trim($value) == "$baseTableName.currency_id") {
				$value = ' nectarcrm_currency_info.currency_name AS currency_id';
			}
		}
		array_push($columnFields, "nectarcrm_pricebookproductrel.productid as Relatedto", "nectarcrm_pricebookproductrel.listprice as ListPrice");
		$joinSplit = preg_split('/ WHERE /i',$splitQuery[1], 2);
		$joinSplit[0] .= " LEFT JOIN nectarcrm_currency_info ON nectarcrm_currency_info.id = $baseTableName.currency_id "
				."LEFT JOIN nectarcrm_pricebookproductrel on nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid ";
		$splitQuery[1] = $joinSplit[0] . ' WHERE ' .$joinSplit[1];
		$query = implode(', ', $columnFields).' FROM ' . $splitQuery[1];
		return $query;
	}

	public function getAdditionalImportFields() {
		if (!$this->importableFields) {
			$fieldHeaders = array(
								'relatedto'=> array('label'=>'Related To', 'uitype'=>10),//For relation field
								'listprice'=> array('label'=>'ListPrice', 'uitype'=>83)//For related field currency
				);

			$this->importableFields = array();
			foreach ($fieldHeaders as $fieldName => $fieldInfo) {
				$fieldModel = new nectarcrm_Field_Model();
				$fieldModel->name = $fieldName;
				$fieldModel->label = $fieldInfo['label'];
				$fieldModel->column = $fieldName;
				$fieldModel->uitype = $fieldInfo['uitype'];
				$webServiceField = $fieldModel->getWebserviceFieldObject();
				$webServiceField->setFieldDataType($fieldModel->getFieldDataType());
				$fieldModel->webserviceField = $webServiceField;
				$this->importableFields[$fieldName] = $fieldModel;
			}
		}
		return $this->importableFields;
	}

}
