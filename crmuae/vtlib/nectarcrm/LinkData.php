<?php

/* +*******************************************************************************
 *  The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *
 * ******************************************************************************* */

/**
 * @author MAK
 */

/**
 * Description of LinkData
 *
 * @author MAK
 */
class nectarcrm_LinkData {
	protected $input;
	protected $link;
	protected $user;
	protected $module;

	public function __construct($link, $user, $input = null) {
		global $currentModule;
		$this->link = $link;
		$this->user = $user;
		$this->module = $currentModule;
		if(empty($input)) {
			$this->input = $_REQUEST;
		} else {
			$this->input = $input;
		}
	}

	public function getInputParameter($name) {
		return $this->input[$name];
	}

	/**
	 *
	 * @return nectarcrm_Link
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 *
	 * @return Users 
	 */
	public function getUser() {
		return $this->user;
	}

	public function getModule() {
		return $this->module;
	}

}

?>