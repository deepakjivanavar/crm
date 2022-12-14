<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Tags_Module_Model extends Settings_nectarcrm_Module_Model {

	var $baseTable = 'nectarcrm_freetags';
	var $baseIndex = 'id';
	var $listFields = array('tag' => 'Tags', 'visibility' => 'Private/Public');
	var $nameFields = array('tag');
	var $name = 'Tags';

	public function getCreateRecordUrl() {
		return "javascript:Settings_Tags_List_Js.triggerAdd(event)";
	}

}