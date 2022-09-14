<?php
/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'include/events/VTEventHandler.inc';

class nectarcrm_RecordLabelUpdater_Handler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb;

		if ($eventName == 'nectarcrm.entity.aftersave') {
			$labelInfo = getEntityName($data->getModuleName(), $data->getId(), true);

			if ($labelInfo) {
				$label = decode_html($labelInfo[$data->getId()]);
				$adb->pquery('UPDATE nectarcrm_crmentity SET label=? WHERE crmid=?', array($label, $data->getId()));
			}
		}
	}
}