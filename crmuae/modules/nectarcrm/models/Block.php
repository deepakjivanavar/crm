<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'vtlib/nectarcrm/Block.php';

class nectarcrm_Block_Model extends nectarcrm_Block {

	public $fields = false;

	public function getFields() {
		if(empty($this->fields)) {
			$moduleFields = nectarcrm_Field_Model::getAllForModule($this->module);
            $this->fields = array();
            
            // if block does not contains any fields 
            if(!is_array($moduleFields[$this->id])){
                $moduleFields[$this->id] = array();
            }
            
			foreach($moduleFields[$this->id] as $field){
                    $this->fields[$field->get('name')] = $field;
			}
		}
		return $this->fields;
	}
    
    public function setFields($fieldModelList) {
        $this->fields = $fieldModelList;
        return $this;
    }

	/**
	 * Function to get the value of a given property
	 * @param <String> $propertyName
	 * @return <Object>
	 */
	public function get($propertyName) {
		if(property_exists($this,$propertyName)){
			return $this->$propertyName;
		}
	}
    
    public function set($propertyName, $value) {
        if(property_exists($this,$propertyName)){
            $this->$propertyName = $value;
        }
        return $this;
    }
    
    public function isCustomized() {
        return ($this->iscustom != 0) ? true : false;
    }
    
    public function __update() {
        $db = PearDatabase::getInstance();
        
        $query = 'UPDATE nectarcrm_blocks SET blocklabel=?,display_status=? WHERE blockid=?';
        $params = array($this->label, $this->display_status, $this->id);
        $db->pquery($query, $params);
    }

	/**
     * Function which indicates whether the block is shown or hidden
     * @return : <boolean>
     */
    public function isHidden(){
        if($this->get('display_status') == '0') {
            return true;
        }
        return false;
    }
    
    /**
     * Function to get the in active fields for the block
     * @param type $raw - true to send field in model format or false to send in array format
     * @return type - arrays
     */
    public function getInActiveFields($raw=true) {
        $inActiveFields = array();
        $fields = $this->getFields();
        foreach($fields as $fieldName => $fieldModel) {
            if(!$fieldModel->isActiveField()) {
                if($raw){
                    $inActiveFields[$fieldName] = $fieldModel;
                }else{
                    $fieldDetails = $fieldModel->getFieldInfo();
                    $fieldDetails['fieldid'] = $fieldModel->getId();
                    $inActiveFields[$fieldName] = $fieldDetails;
                }
            }
        }
        return $inActiveFields;
    }

	/**
	 * Function to retrieve block instances for a module
	 * @param <type> $moduleModel - module instance
	 * @return <array> - list of nectarcrm_Block_Model
	 */
	public static function getAllForModule($moduleModel) {
		$blockObjects = nectarcrm_Cache::get('ModuleBlocks',$moduleModel->getId());
        
        if(!$blockObjects){
            $blockObjects = parent::getAllForModule($moduleModel);
            if($blockObjects)
                nectarcrm_Cache::set('ModuleBlocks',$moduleModel->getId(),$blockObjects);
        }
        $blockModelList = array();

		if($blockObjects) {
			foreach($blockObjects as $blockObject) {
				$blockModelList[] = self::getInstanceFromBlockObject($blockObject);
			}
		}
		return $blockModelList;
	}
	
	public static function getInstance($value, $moduleInstance = false) {
		$blockInstance = parent::getInstance($value, $moduleInstance);
		$blockModel = self::getInstanceFromBlockObject($blockInstance);
		return $blockModel;
	}

	/**
	 * Function to retrieve block instance from nectarcrm_Block object
	 * @param nectarcrm_Block $blockObject - vtlib block object
	 * @return nectarcrm_Block_Model
	 */
	public static function getInstanceFromBlockObject(nectarcrm_Block $blockObject) {
		$objectProperties = get_object_vars($blockObject);
		$blockClassName = nectarcrm_Loader::getComponentClassName('Model', 'Block', $blockObject->module->name);
		$blockModel = new $blockClassName();
		foreach($objectProperties as $properName=>$propertyValue) {
			$blockModel->$properName = $propertyValue;
		}
		return $blockModel;
	}
    
    public static function updateSequenceNumber($sequenceList, $moduleName = false) {
        $db = PearDatabase::getInstance();
        $query = 'UPDATE nectarcrm_blocks SET sequence = CASE blockid ';
        foreach ($sequenceList as $blockId => $sequence){
            $query .=' WHEN '.$blockId.' THEN '.$sequence;
        }
        $query .=' END ';
        $query .= ' WHERE blockid IN ('.generateQuestionMarks($sequenceList).')';
        $db->pquery($query, array_keys($sequenceList));
        
        // To clear cache
        if($moduleName){
            $moduleInstance = nectarcrm_Module_Model::getInstance($moduleName);
            nectarcrm_Cache::flushModuleBlocksCache($moduleInstance);
        }
        // End
    }
    
    public static function checkFieldsExists($blockId) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT 1 FROM nectarcrm_field WHERE block=?';
        $result = $db->pquery($query, array($blockId));
        return ($db->num_rows($result) > 0) ? true : false;
    }
	
	/**
	 * Function to push all blocks down after sequence number
	 * @param type $fromSequence 
	 */
	public static function pushDown($fromSequence, $sourceModuleTabId) {
		$db = PearDatabase::getInstance();
		$query = 'UPDATE nectarcrm_blocks SET sequence=sequence+1 WHERE sequence > ? and tabid=?';
		$result = $db->pquery($query, array($fromSequence,$sourceModuleTabId));
        
        // To clear Cache
        $moduleModel = nectarcrm_Module_Model::getInstance($sourceModuleTabId);
        nectarcrm_Cache::flushModuleBlocksCache($moduleModel);
        // End
	}
    
    public static function getAllBlockSequenceList($moduleTabId) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT blockid,sequence FROM nectarcrm_blocks where tabid=?';
        $result = $db->pquery($query, array($moduleTabId));
        $response = array();
        $num_rows = $db->num_rows($result);
        for($i=0; $i<$num_rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $response[$row['blockid']] = $row['sequence'];
        }
        return $response;
    }

	/**
	 * Function to check whether duplicate exist or not
	 * @param <String> $blockLabel
	 * @param <Number> ModuleId
	 * @return <Boolean> true/false
	 */
	public static function checkDuplicate($blockLabel, $tabId) {
		$db = PearDatabase::getInstance();

		$result = $db->pquery('SELECT 1 FROM nectarcrm_blocks WHERE blocklabel = ? AND tabid = ?', array($blockLabel, $tabId));
		if ($db->num_rows($result)) {
			return true;
		}
		return false;
	}
	
}
