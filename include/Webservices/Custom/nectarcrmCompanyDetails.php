<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *
 *********************************************************************************/
require_once 'include/Webservices/nectarcrmActorOperation.php';
/**
 * Description of nectarcrmCompanyDetails
 *
 * @author MAK
 */
class nectarcrmCompanyDetails extends nectarcrmActorOperation {
	public function create($elementType, $element) {
		$db = PearDatabase::getInstance();
		$sql = 'select * from nectarcrm_organizationdetails';
		$result = $db->pquery($sql,$params);
		$rowCount = $db->num_rows($result);
		if($rowCount > 0) {
			$id = $db->query_result($result,0,'organization_id');
			$meta = $this->getMeta();
			$element['id'] = vtws_getId($meta->getEntityId(), $id);
			return $this->revise($element);
		}else{
			$element = $this->handleFileUpload($element);
			return parent::create($elementType, $element);
		}
	}

	function handleFileUpload($element) {
		$fileFieldList = $this->meta->getFieldListByType('file');
		foreach ($fileFieldList as $field) {
			$fieldname = $field->getFieldName();
			if(is_array($_FILES[$fieldname])) {
				$element[$fieldname] = vtws_CreateCompanyLogoFile($fieldname);
			}
		}
		return $element;
	}

	public function update($element) {
		$element = $this->handleFileUpload($element);
		return parent::update($element);
	}

	public function revise($element) {
		$element = $this->handleFileUpload($element);
		return parent::revise($element);
	}

	public function retrieve($id) {
		$element = parent::retrieve($id);
		if (empty($element['logo'])) {
			$element['logo'] = vtws_getCompanyEncodedImage($element['logoname']);
		}
		return $element;
	}
}
?>