<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Assets_Module_Model extends nectarcrm_Module_Model {

	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if ($sourceModule == 'HelpDesk') {
			$condition = " nectarcrm_assets.assetsid NOT IN (SELECT relcrmid FROM nectarcrm_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM nectarcrm_crmentityrel WHERE relcrmid = '$record') ";

			$pos = stripos($listQuery, 'where');
			if ($pos) {
				$split = preg_split('/where/i', $listQuery);
				$overRideQuery = $split[0].' WHERE '.$split[1].' AND '.$condition;
			} else {
				$overRideQuery = $listQuery.' WHERE '.$condition;
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

	/*
	 * Function to get supported utility actions for a module
	 */
	public function getUtilityActionsNames() {
		return array('Import', 'Export', 'DuplicatesHandling');
	}

}
