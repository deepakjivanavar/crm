<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class EmailTemplates_Field_Model extends nectarcrm_Field_Model {

	public static $allFields = false;

	public function isViewable(){
		return true;
	}

	public static function getAllForModule($moduleModel){
		if(empty(self::$allFields)) {
			$fieldsList = array();
			$firstBlockFields = array('templatename'=>'LBL_TEMPLATE_NAME','description'=>'LBL_DESCRIPTION');
			$secondBlockFields = array('subject'=>'LBL_SUBJECT');
			$blocks = $moduleModel->getBlocks();

			foreach ($firstBlockFields as $fieldName=>$fieldLabel) {
				$fieldModel = new EmailTemplates_Field_Model();
				$blockModel = $blocks['SINGLE_EmailTemplates'];
				$fieldModel->set('name',$fieldName)->set('label',$fieldLabel)->set('block',$blockModel);
				$fieldsList[$blockModel->get('id')][] = $fieldModel;

			}

			foreach($secondBlockFields as $fieldName=>$fieldLabel){
				$fieldModel = new EmailTemplates_Field_Model();
				$blockModel = $blocks['LBL_EMAIL_TEMPLATE'];
				$fieldModel->set('name',$fieldName)->set('label',$fieldLabel)->set('block',$blockModel);
				$fieldsList[$blockModel->get('id')][] = $fieldModel;
			}
			self::$allFields = $fieldsList;
		}
		return self::$allFields;
	}

	/**
	 * Function to check if the field is named field of the module
	 * @return <Boolean> - True/False
	 */
	public function isNameField() {
		return false;
	}

}