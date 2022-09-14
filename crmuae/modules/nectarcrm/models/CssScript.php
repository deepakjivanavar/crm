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
 * CSS Script Model Class
 */
class nectarcrm_CssScript_Model extends nectarcrm_Base_Model {

	const DEFAULT_REL = 'stylesheet';
	const DEFAULT_MEDIA = 'screen';
	const DEFAULT_TYPE = 'text/css';

	const LESS_REL = 'stylesheet/less';

	/**
	 * Function to get the rel attribute value
	 * @return <String>
	 */
	public function getRel(){
		$rel = $this->get('rel');
		if(empty($rel)){
			$rel = self::DEFAULT_REL;
		}
		return $rel;
	}

	/**
	 * Function to get the media attribute value
	 * @return <String>
	 */
	public function getMedia(){
		$media = $this->get('media');
		if(empty($media)){
			$media = self::DEFAULT_MEDIA;
		}
		return $media;
	}

	/**
	 * Function to get the type attribute value
	 * @return <String>
	 */
	public function getType(){
		$type = $this->get('type');
		if(empty($type)){
			$type = self::DEFAULT_TYPE;
		}
		return $type;
	}

	/**
	 * Function to get the href attribute value
	 * @return <String>
	 */
	public function getHref() {
		$href = $this->get('href');
		if(empty($href)) {
			$href = $this->get('linkurl');
		}
		return $href;
	}

	/**
	 * Function to get the instance of CSS Script model from a given nectarcrm_Link object
	 * @param nectarcrm_Link $linkObj
	 * @return nectarcrm_CssScript_Model instance
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
