<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Products_MassActionAjax_View extends nectarcrm_MassActionAjax_View {

	public function initMassEditViewContents(nectarcrm_Request $request) {
		parent::initMassEditViewContents($request);

		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$fieldInfo = array();
		$fieldList = $moduleModel->getFields();
		foreach ($fieldList as $fieldName => $fieldModel) {
			$fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
		}

		$additionalFieldsList = $moduleModel->getAdditionalImportFields();
		foreach ($additionalFieldsList as $fieldName => $fieldModel) {
			$fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
		}

		$recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
		$taxDetails = $recordModel->getTaxClassDetails();
		foreach ($taxDetails as $taxkey => $taxInfo) {
			$taxInfo['percentage'] = 0;
			foreach ($taxInfo['regions'] as $regionKey => $regionInfo) {
				$taxInfo['regions'][$regionKey]['value'] = 0;
			}
			$taxDetails[$taxkey] = $taxInfo;
		}

		$viewer->assign('TAXCLASS_DETAILS', $taxDetails);
		$viewer->assign('MASS_EDIT_FIELD_DETAILS', $fieldInfo);
	}
}
