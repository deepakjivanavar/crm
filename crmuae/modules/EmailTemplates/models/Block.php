<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class EmailTemplates_Block_Model extends nectarcrm_Block_Model {

	public function getFields() {
		if (empty($this->fields)) {
			$moduleFields = EmailTemplates_Field_Model::getAllForModule($this->module);
			$this->fields = array();
			$fieldsList = $moduleFields[$this->id];
			// if block does not contains any fields 
			if (!is_array($moduleFields[$this->id])) {
				$moduleFields[$this->id] = array();
			}

			foreach ($moduleFields[$this->id] as $field) {
				$this->fields[$field->get('name')] = $field;
			}
		}
		return $this->fields;
	}

}
