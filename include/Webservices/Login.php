<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
	
	function vtws_login($username,$pwd){
		
		$user = new Users();
		$userId = $user->retrieve_user_id($username);
		
		$token = vtws_getActiveToken($userId);
		if($token == null){
			throw new WebServiceException(WebServiceErrorCode::$INVALIDTOKEN,"Specified token is invalid or expired");
		}
		
		$accessKey = vtws_getUserAccessKey($userId);
		if($accessKey == null){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSKEYUNDEFINED,"Access key for the user is undefined");
		}
		
		$accessCrypt = md5($token.$accessKey);
		if(strcmp($accessCrypt,$pwd)!==0){
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD,"Invalid username or password");
		}
		$user = $user->retrieveCurrentUserInfoFromFile($userId);
		if($user->status != 'Inactive'){
			return $user;
		}
		throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED,'Given user is inactive');
	}
	
	function vtws_getActiveToken($userId){
		global $adb;
		
		$sql = "select token from nectarcrm_ws_userauthtoken where userid=? and expiretime >= ?";
		$result = $adb->pquery($sql,array($userId,time()));
		if($result != null && isset($result)){
			if($adb->num_rows($result)>0){
				return $adb->query_result($result,0,"token");
			}
		}
		return null;
	}
	
	function vtws_getUserAccessKey($userId){
		global $adb;
		
		$sql = "select accesskey from nectarcrm_users where id=?";
		$result = $adb->pquery($sql,array($userId));
		if($result != null && isset($result)){
			if($adb->num_rows($result)>0){
				return $adb->query_result($result,0,"accesskey");
			}
		}
		return null;
	}
	
?>