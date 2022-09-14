<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * nectarcrm JS Script Model Class
 */
class nectarcrm_JsScript_Model extends nectarcrm_Base_Model {

	const DEFAULT_TYPE = 'text/javascript';

	/**
	 * Function to get the type attribute value
	 * @return <String>
	 */
	public function getType() {
		$type = $this->get('type');
		if(empty($type)){
			$type = self::DEFAULT_TYPE;
		}
		return $type;
	}

	/**
	 * Function to get the src attribute value
	 * @return <String>
	 */
	public function getSrc() {
		$src = $this->get('src');
		if(empty($src)) {
            $src = $this->get('linkurl');
		}
		return $src;
	}

	/**
	 * Static Function to get an instance of nectarcrm JsScript Model from a given nectarcrm_Link object
	 * @param nectarcrm_Link $linkObj
	 * @return nectarcrm_JsScript_Model instance
	 */
	public static function getInstanceFromLinkObject (nectarcrm_Link $linkObj){
		$objectProperties = get_object_vars($linkObj);
		$linkModel = new self();
		foreach($objectProperties as $properName=>$propertyValue){
			$linkModel->$properName = $propertyValue;
		}
		return $linkModel->setData($objectProperties);
	}
}