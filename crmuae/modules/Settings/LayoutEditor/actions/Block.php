<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_LayoutEditor_Block_Action extends Settings_nectarcrm_Index_Action {
    
    public function __construct() {
        $this->exposeMethod('save');
        $this->exposeMethod('updateSequenceNumber');
        $this->exposeMethod('delete');
    }
    
    public function save(nectarcrm_Request $request) {
        $blockId = $request->get('blockid');
        $sourceModule = $request->get('sourceModule');
        $modueInstance = nectarcrm_Module_Model::getInstance($sourceModule);

        if(!empty($blockId)) {
            $blockInstance = Settings_LayoutEditor_Block_Model::getInstance($blockId);
            $blockInstance->set('display_status',$request->get('display_status'));
			$isDuplicate = false;
        } else {
            $blockInstance = new Settings_LayoutEditor_Block_Model();
            $blockInstance->set('label', $request->get('label'));
			$blockInstance->set('iscustom', '1');
             //Indicates block id after which you need to add the new block
            $beforeBlockId = $request->get('beforeBlockId');
            if(!empty($beforeBlockId)) {
                $beforeBlockInstance = nectarcrm_Block_Model::getInstance($beforeBlockId);
				$beforeBlockSequence = $beforeBlockInstance->get('sequence');
				$newBlockSequence = ($beforeBlockSequence+1);
				//To give sequence one more than prev block 
                $blockInstance->set('sequence', $newBlockSequence);
				//push all other block down so that we can keep new block there
				nectarcrm_Block_Model::pushDown($beforeBlockSequence, $modueInstance->getId());
            }
			$isDuplicate = nectarcrm_Block_Model::checkDuplicate(trim($request->get('label')), $modueInstance->getId());
        }

		$response = new nectarcrm_Response();
		if (!$isDuplicate) {
			try{
				$id = $blockInstance->save($modueInstance);
				$responseInfo = array('id'=>$id,'label'=>$blockInstance->get('label'),'isCustom'=>$blockInstance->isCustomized(), 'beforeBlockId'=>$beforeBlockId, 'isAddCustomFieldEnabled'=>$blockInstance->isAddCustomFieldEnabled());
				if(empty($blockId)) {
					//if mode is create add all blocks sequence so that client will place the new block correctly
					$responseInfo['sequenceList'] = nectarcrm_Block_Model::getAllBlockSequenceList($modueInstance->getId());
				}
				$response->setResult($responseInfo);
			} catch(Exception $e) {
				$response->setError($e->getCode(),$e->getMessage());
			}
		} else {
			$response->setError('502', vtranslate('LBL_DUPLICATES_EXIST', $request->getModule(false)));
		}
        $response->emit();
    }
    
    public function updateSequenceNumber(nectarcrm_Request $request) {
        $response = new nectarcrm_Response();
        try{
            $sequenceList = $request->get('sequence');
            nectarcrm_Block_Model::updateSequenceNumber($sequenceList,$request->get('selectedModule'));
            $response->setResult(array('success'=>true));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
    
    
    public function delete(nectarcrm_Request $request) {
        $response = new nectarcrm_Response();
        $blockId = $request->get('blockid');
        $checkIfFieldsExists = nectarcrm_Block_Model::checkFieldsExists($blockId);
        if($checkIfFieldsExists) {
            $response->setError('502','Fields exists for the block');
            $response->emit();
            return;
        }
        $blockInstance = nectarcrm_Block_Model::getInstance($blockId);
        if(!$blockInstance->isCustomized()) {
            $response->setError('502','Cannot delete non custom blocks');
            $response->emit();
            return;
        }
        try{
            $sourceModule = $blockInstance->get('module')->name;
            $blockLabel = $blockInstance->get('label');
            $blockInstance->delete(false);
            Settings_LayoutEditor_Module_Model::removeLabelFromLangFile($sourceModule, $blockLabel);
            $response->setResult(array('success'=>true));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }

}