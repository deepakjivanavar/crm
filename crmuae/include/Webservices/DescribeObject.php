<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
	
	function vtws_describe($elementType,$user){
		
		global $log,$adb,$app_strings;

		//setting $app_strings 
		if (!$app_strings) {
			$currentLanguage = nectarcrm_Language_Handler::getLanguage();
			$moduleLanguageStrings = nectarcrm_Language_Handler::getModuleStringsFromFile($currentLanguage);
			$app_strings = $moduleLanguageStrings['languageStrings'];
		}

		$webserviceObject = nectarcrmWebserviceObject::fromName($adb,$elementType);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		
		require_once $handlerPath;
		
		$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
		$meta = $handler->getMeta();
		
		$types = vtws_listtypes(null, $user);
		if(!in_array($elementType,$types['types'])){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
		}
		
		$entity = $handler->describe($elementType);
		VTWS_PreserveGlobal::flush();
		return $entity;
	}
	
?>