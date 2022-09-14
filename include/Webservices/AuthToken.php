<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
	
	function vtws_getchallenge($username){
		
		global $adb;
		
		if(empty($username)){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"No username given");
		}

		$user = new Users();
		$userid = $user->retrieve_user_id($username);

        if(empty($userid)){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"username does not exists");
		}

		$authToken = uniqid();
		$servertime = time();
		$expireTime = time()+(60*5);
		
		$sql = "delete from nectarcrm_ws_userauthtoken where userid=?";
		$adb->pquery($sql,array($userid));
		
		$sql = "insert into nectarcrm_ws_userauthtoken(userid,token,expireTime) values (?,?,?)";
		$adb->pquery($sql,array($userid,$authToken,$expireTime));
		
		return array("token"=>$authToken,"serverTime"=>$servertime,"expireTime"=>$expireTime);
	}

?>