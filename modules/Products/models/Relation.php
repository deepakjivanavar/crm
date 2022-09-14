<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Products_Relation_Model extends nectarcrm_Relation_Model {
	
	/**
	 * Function returns the Query for the relationhips
	 * @param <nectarcrm_Record_Model> $recordModel
	 * @param type $actions
	 * @return <String>
	 */
	public function getQuery($recordModel, $actions=false){
		$parentModuleModel = $this->getParentModuleModel();
		$relatedModuleModel = $this->getRelationModuleModel();
		$relatedModuleName = $relatedModuleModel->get('name');
		$parentModuleName = $parentModuleModel->get('name');
		$functionName = $this->get('name');
		$focus = CRMEntity::getInstance($parentModuleName);
		$focus->id = $recordModel->getId();
		if(method_exists($parentModuleModel, $functionName)) {
			$query = $parentModuleModel->$functionName($recordModel, $relatedModuleModel);
		} else {
            //For get_dependent_list fourth parameter should be relation id. So we are replacing actions by relation id if it is not given
            if(!$actions && $functionName == "get_dependents_list") {
                $actions = $this->getId();
            }
			$result = $focus->$functionName($recordModel->getId(), $parentModuleModel->getId(),
											$relatedModuleModel->getId(), $actions);
			$query = $result['query'];
		}

		//modify query if any module has summary fields, those fields we are displayed in related list of that module
		$relatedListFields = $relatedModuleModel->getConfigureRelatedListFields();
		if(count($relatedListFields) > 0 ) {
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$queryGenerator = new QueryGenerator($relatedModuleName, $currentUser);
			$queryGenerator->setFields($relatedListFields);
			$selectColumnSql = $queryGenerator->getSelectClauseColumnSQL();
			$newQuery = preg_split('/FROM/i', $query);
			$selectColumnSql = 'SELECT DISTINCT nectarcrm_crmentity.crmid, '.$selectColumnSql;
			$query = $selectColumnSql.' FROM '.$newQuery[1];
		}
		if($functionName == 'get_product_pricebooks'){
			$newQuery = preg_split('/FROM/i', $query);
			$selectColumnSql = $newQuery[0].' ,nectarcrm_pricebookproductrel.listprice, nectarcrm_pricebook.currency_id, nectarcrm_products.unit_price';
			$query = $selectColumnSql.' FROM '.$newQuery[1];
		}
		if($functionName == 'get_service_pricebooks'){
			$newQuery = preg_split('/FROM/i', $query);
			$selectColumnSql = $newQuery[0].' ,nectarcrm_pricebookproductrel.listprice, nectarcrm_pricebook.currency_id, nectarcrm_service.unit_price';
			$query = $selectColumnSql.' FROM '.$newQuery[1];
		}
		return $query;
	}
	
	/**
	 * Function that deletes PriceBooks related records information
	 * @param <Integer> $sourceRecordId - Product/Service Id
	 * @param <Integer> $relatedRecordId - Related Record Id
	 */
	public function deleteRelation($sourceRecordId, $relatedRecordId) {
		$sourceModuleName = $this->getParentModuleModel()->get('name');
		$relatedModuleName = $this->getRelationModuleModel()->get('name');
		if(($sourceModuleName == 'Products' || $sourceModuleName == 'Services') && $relatedModuleName == 'PriceBooks') {
			//Description: deleteListPrice function is deleting the relation between Pricebook and Product/Service 
			$priceBookModel = nectarcrm_Record_Model::getInstanceById($relatedRecordId, $relatedModuleName);
			$priceBookModel->deleteListPrice($sourceRecordId);
			$relatedModuleFocus = CRMEntity::getInstance($relatedModuleName);
			$relatedModuleFocus->trackUnLinkedInfo($sourceModuleName, $sourceRecordId, $relatedModuleName, $relatedRecordId);
		} else if($sourceModuleName == $relatedModuleName && $this->get('source')!='custom'){
			$this->deleteProductToProductRelation($sourceRecordId, $relatedRecordId);
		} else {
			parent::deleteRelation($sourceRecordId, $relatedRecordId);
		}
	}
	
	/**
	 * Function to delete the product to product relation(product bundles)
	 * @param type $sourceRecordId
	 * @param type $relatedRecordId true / false
	 * @return <boolean>
	 */
	public function deleteProductToProductRelation($sourceRecordId, $relatedRecordId) {
		$db = PearDatabase::getInstance();
		if(!empty($sourceRecordId) && !empty($relatedRecordId)){
			$db->pquery('DELETE FROM nectarcrm_seproductsrel WHERE crmid = ? AND productid = ?', array($relatedRecordId, $sourceRecordId));
			return true;
		}
	}
    
    /**
     * Function which will specify whether the relation is deletable
     * @return <Boolean>
     */
    public function isDeletable() {
        $relatedModuleModel = $this->getRelationModuleModel();
        $relatedModuleName = $relatedModuleModel->get('name');
        $inventoryModulesList = array('Invoice','Quotes','PurchaseOrder','SalesOrder');
        
        //Inventoty relationship cannot be deleted from the related list
        if(in_array($relatedModuleName, $inventoryModulesList)){
            return false;
        }
        return parent::isDeletable();
    }
	
	
	public function isSubProduct($subProductId){
		if(!empty($subProductId)){
			$db = PearDatabase::getInstance();
			$result = $db->pquery('SELECT crmid FROM nectarcrm_seproductsrel WHERE crmid = ?', array($subProductId));
			if($db->num_rows($result) > 0){
				return true;
			}
		}
	}
	
	/**
	 * Function to add Products/Services-PriceBooks Relation
	 * @param <Integer> $sourceRecordId
	 * @param <Integer> $destinationRecordId
	 * @param <Integer> $listPrice
	 */
	public function addListPrice($sourceRecordId, $destinationRecordId, $listPrice) {
		$sourceModuleName = $this->getParentModuleModel()->get('name');
		$relatedModuleName = $this->getRelationModuleModel()->get('name');
		$relationModuleModel = nectarcrm_Record_Model::getInstanceById($destinationRecordId, $relatedModuleName);
		
		$productModel = nectarcrm_Record_Model::getInstanceById($sourceRecordId, $sourceModuleName);
		$productModel->updateListPrice($destinationRecordId, $listPrice, $relationModuleModel->get('currency_id'));
	}

	public function updateShowBundlesOption($recordId, $value) {
		$sourceModuleName = $this->getParentModuleModel()->get('name');

		$productRecordModel = nectarcrm_Record_Model::getInstanceById($recordId, $sourceModuleName);
		$productRecordModel->updateShowBundlesOption($value);
	}

	/**
	 * Function to update Product bundles relation
	 * @param <Integer> $sourceRecordId
	 * @param <Integer> $destinationRecordId
	 * @param <Integer> $quantity
	 */
	public function updateQuantity($sourceRecordId, $destinationRecordId, $quantity) {
		$sourceModuleName = $this->getParentModuleModel()->get('name');
		$relatedModuleName = $this->getRelationModuleModel()->get('name');
		$relationModuleModel = nectarcrm_Record_Model::getInstanceById($destinationRecordId, $relatedModuleName);

		$productModel = nectarcrm_Record_Model::getInstanceById($sourceRecordId, $sourceModuleName);
		$productModel->updateSubProductQuantity($destinationRecordId, $quantity);
	}
}
