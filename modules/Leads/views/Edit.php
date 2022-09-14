<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Edit_View extends nectarcrm_Edit_View {

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        if(!$recordModel){
            if (!empty($recordId)) {
                $recordModel = nectarcrm_Record_Model::getInstanceById($recordId, $moduleName);
            } else {
                $recordModel = nectarcrm_Record_Model::getCleanInstance($moduleName);
            }
        }

		$viewer = $this->getViewer($request);

	$salutationFieldModel = nectarcrm_Field_Model::getInstance('salutationtype', $recordModel->getModule());
	$salutationValue = $request->get('salutationtype');
        if(!empty($salutationValue)){ 
        	$salutationFieldModel->set('fieldvalue', $salutationValue); 
        } else{ 
        	$salutationFieldModel->set('fieldvalue', $recordModel->get('salutationtype')); 
        } 
		$viewer->assign('SALUTATION_FIELD_MODEL', $salutationFieldModel);

		parent::process($request);
	}

}
