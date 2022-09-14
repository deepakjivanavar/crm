<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_LayoutEditor_Relation_Action extends Settings_nectarcrm_Index_Action {
    
    public function process(nectarcrm_Request $request) {
        $relationInfo = $request->get('related_info');
        $updatedRelatedList = $relationInfo['updated'];
        $deletedRelatedList = $relationInfo['deleted'];
		if(empty($updatedRelatedList)) {
			$updatedRelatedList = array();
		}
		if(empty($deletedRelatedList)) {
			$deletedRelatedList = array();
		}
        $sourceModule = $request->get('sourceModule');
        $moduleModel = nectarcrm_Module_Model::getInstance($sourceModule, false);
        $relationModulesList = nectarcrm_Relation_Model::getAllRelations($moduleModel, false);
        $sequenceList = array();
        foreach($relationModulesList as $relationModuleModel) {
            $sequenceList[] = $relationModuleModel->get('sequence');
        }
        //To sort sequence in ascending order
        sort($sequenceList);
        $relationUpdateDetail = array();
        $index = 0;
        foreach($updatedRelatedList as $relatedId) {
            $relationUpdateDetail[] = array('relation_id' => $relatedId, 'sequence' => $sequenceList[$index++] , 'presence' => 0);
        }
        foreach($deletedRelatedList as $relatedId) {
            $relationUpdateDetail[] = array('relation_id'=> $relatedId, 'sequence' => $sequenceList[$index++], 'presence' => 1);
        }
        $response = new nectarcrm_Response();
        try{
            $response->setResult(array('success'=> true));
            nectarcrm_Relation_Model::updateRelationSequenceAndPresence($relationUpdateDetail, $moduleModel->getId());
        }
        catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) { 
        $request->validateWriteAccess(); 
    } 
}