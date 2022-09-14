<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class PBXManagerHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {
        $moduleName = $entityData->getModuleName();

        $acceptedModule = array('Contacts','Accounts','Leads');
        if(!in_array($moduleName, $acceptedModule))
            return;
        
        if ($eventName == 'nectarcrm.entity.aftersave') {
            PBXManagerHandler::handlePhoneLookUpSaveEvent($entityData, $moduleName);
        }
 
		if ($eventName == 'nectarcrm.lead.convertlead' && $moduleName == 'Leads') {
			PBXManagerHandler::handlePhoneLookupDeleteEvent($entityData);
		}

		if($eventName == 'nectarcrm.entity.afterdelete'){
            PBXManagerHandler::handlePhoneLookupDeleteEvent($entityData);
        }
        
        if($eventName == 'nectarcrm.entity.afterrestore'){
            $this->handlePhoneLookUpRestoreEvent($entityData, $moduleName);
        }
    }

    static function handlePhoneLookUpSaveEvent($entityData, $moduleName) {
        $recordid = $entityData->getId();
        $data = $entityData->getData();
        
        $values['crmid'] = $recordid;
        $values['setype'] = $moduleName;
        $recordModel = new PBXManager_Record_Model;

        $moduleInstance = nectarcrm_Module_Model::getInstance($moduleName);
        $fieldsModel = $moduleInstance->getFieldsByType('phone');
        
        foreach ($fieldsModel as $field => $fieldName) {
                $fieldName = $fieldName->get('name');      
                $values[$fieldName] = $data[$fieldName];
                
                if($values[$fieldName])
                    $recordModel->receivePhoneLookUpRecord($fieldName, $values, true);
        }
    }
    
    static function handlePhoneLookupDeleteEvent($entityData){
        $recordid = $entityData->getId();
        $recordModel = new PBXManager_Record_Model;
        $recordModel->deletePhoneLookUpRecord($recordid);
    }
    
    protected function handlePhoneLookUpRestoreEvent($entityData, $moduleName) {
        $recordid = $entityData->getId();

        //To get the record model of the restored record
        $recordmodel = nectarcrm_Record_Model::getInstanceById($recordid, $moduleName);

        $values['crmid'] = $recordid;
        $values['setype'] = $moduleName;
        $recordModel = new PBXManager_Record_Model;

        $moduleInstance = nectarcrm_Module_Model::getInstance($moduleName);
        $fieldsModel = $moduleInstance->getFieldsByType('phone');
        
        foreach ($fieldsModel as $field => $fieldName) {
            $fieldName = $fieldName->get('name');  
            $values[$fieldName] = $recordmodel->get($fieldName);
            
            if($values[$fieldName])
                 $recordModel->receivePhoneLookUpRecord($fieldName, $values, true);
        }
    }

}

class PBXManagerBatchHandler extends VTEventHandler {
    
    function handleEvent($eventName, $entityDatas) {
        foreach ($entityDatas as $entityData) {
            $moduleName = $entityData->getModuleName();

            $acceptedModule = array('Contacts','Accounts','Leads');
            if(!in_array($moduleName, $acceptedModule))
                return;

            if ($eventName == 'nectarcrm.batchevent.save') {
                PBXManagerHandler::handlePhoneLookUpSaveEvent($entityData, $moduleName);
            }
            
            if ($eventName == 'nectarcrm.batchevent.delete') {
                PBXManagerHandler::handlePhoneLookupDeleteEvent($entityData);
            }
        }
    }
}

?>