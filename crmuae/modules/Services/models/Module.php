<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Services_Module_Model extends Products_Module_Model {
	
	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		$supportedModulesList = array('Leads', 'Accounts', 'HelpDesk', 'Potentials');
		if (($sourceModule == 'PriceBooks' && $field == 'priceBookRelatedList')
				|| in_array($sourceModule, $supportedModulesList)
				|| in_array($sourceModule, getInventoryModules())) {

			$condition = " nectarcrm_service.discontinued = 1 ";

			if ($sourceModule == 'PriceBooks' && $field == 'priceBookRelatedList') {
				$condition .= " AND nectarcrm_service.serviceid NOT IN (SELECT productid FROM nectarcrm_pricebookproductrel WHERE pricebookid = '$record') ";
			} elseif (in_array($sourceModule, $supportedModulesList)) {
				$condition .= " AND nectarcrm_service.serviceid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";
			}

			$pos = stripos($listQuery, 'where');
			if ($pos) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery . ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}
	
	/**
	 * Function returns query for Services-PriceBooks Relationship
	 * @param <nectarcrm_Record_Model> $recordModel
	 * @param <nectarcrm_Record_Model> $relatedModuleModel
	 * @return <String>
	 */
	function get_service_pricebooks($recordModel, $relatedModuleModel) {
		$query = 'SELECT nectarcrm_pricebook.pricebookid, nectarcrm_pricebook.bookname, nectarcrm_pricebook.active, nectarcrm_crmentity.crmid, 
						nectarcrm_crmentity.smownerid, nectarcrm_pricebookproductrel.listprice, nectarcrm_service.unit_price
					FROM nectarcrm_pricebook
					INNER JOIN nectarcrm_pricebookproductrel ON nectarcrm_pricebook.pricebookid = nectarcrm_pricebookproductrel.pricebookid
					INNER JOIN nectarcrm_crmentity on nectarcrm_crmentity.crmid = nectarcrm_pricebook.pricebookid
					INNER JOIN nectarcrm_service on nectarcrm_service.serviceid = nectarcrm_pricebookproductrel.productid
					INNER JOIN nectarcrm_pricebookcf on nectarcrm_pricebookcf.pricebookid = nectarcrm_pricebook.pricebookid
					LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
					LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid '
					. Users_Privileges_Model::getNonAdminAccessControlQuery($relatedModuleModel->getName()) .'
					WHERE nectarcrm_service.serviceid = '.$recordModel->getId().' and nectarcrm_crmentity.deleted = 0';
		
		return $query;
	}
    
    /*
     * Function to get supported utility actions for a module
     */
    function getUtilityActionsNames() {
        return array('Import', 'Export', 'DuplicatesHandling');
    }
}