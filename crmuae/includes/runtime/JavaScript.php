<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_JavaScript extends nectarcrm_Viewer {

	/**
	 * Function to get the path of a given style sheet or default style sheet
	 * @param <String> $fileName
	 * @return <string / Boolean> - file path , false if not exists
	 */
	public static function getFilePath($fileName=''){
		if(empty($fileName)) {
			return false;
		}
		$filePath =  self::getBaseJavaScriptPath() . '/' . $fileName;
		$completeFilePath = nectarcrm_Loader::resolveNameToPath('~'.$filePath);

		if(file_exists($completeFilePath)){
			return $filePath;
		}
		return false;
	}

	/**
	 * Function to get the Base Theme Path, until theme folder not selected theme folder
	 * @return <string> - theme folder
	 */
	public static function getBaseJavaScriptPath(){
		return 'layouts'. '/' . self::getLayoutName();
	}
}