<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/


/**
 * Functions that need-rewrite / to be eliminated.
 */

class nectarcrm_Deprecated {

	static function getFullNameFromQResult($result, $row_count, $module) {
		global $adb;
		$rowdata = $adb->query_result_rowdata($result, $row_count);
		$entity_field_info = getEntityFieldNames($module);
		$fieldsName = $entity_field_info['fieldname'];
		$name = '';
		if ($rowdata != '' && count($rowdata) > 0) {
			$name = self::getCurrentUserEntityFieldNameDisplay($module, $fieldsName, $rowdata );
		}
		$name = textlength_check($name);
		return $name;
	}

	static function getFullNameFromArray($module, $fieldValues) {
		$entityInfo = getEntityFieldNames($module);
		$fieldsName = $entityInfo['fieldname'];
		$displayName = self::getCurrentUserEntityFieldNameDisplay($module, $fieldsName, $fieldValues);
		return $displayName;
	}

	static function getCurrentUserEntityFieldNameDisplay($module, $fieldsName, $fieldValues) {
		global $current_user;
		if(!is_array($fieldsName)) {
			return $fieldValues[$fieldsName];
		} else {
			$accessibleFieldNames = array();
			foreach($fieldsName as $field) {
				if($module == 'Users' || getColumnVisibilityPermission($current_user->id, $field, $module) == '0') {
					$accessibleFieldNames[] = $fieldValues[$field];
				}
			}
			if(count($accessibleFieldNames) > 0) {
				return implode(' ', $accessibleFieldNames);
			}
		}
		return '';
	}

	static function getBlockId($tabid, $label) {
		global $adb;
		$query = "select blockid from nectarcrm_blocks where tabid=? and blocklabel = ?";
		$result = $adb->pquery($query, array($tabid, $label));
		$noofrows = $adb->num_rows($result);

		$blockid = '';
		if ($noofrows == 1) {
			$blockid = $adb->query_result($result, 0, "blockid");
		}
		return $blockid;
	}

	static function getParentTab() {
		return '';

		/*global $log, $default_charset;
		$log->debug("Entering getParentTab() method ...");
		if (!empty($_REQUEST['parenttab'])) {
			if (self::checkParentTabExists($_REQUEST['parenttab'])) {
				return vtlib_purify($_REQUEST['parenttab']);
			} else {
				return self::getParentTabFromModule($_REQUEST['module']);
			}
		} else {
			return self::getParentTabFromModule($_REQUEST['module']);
		}*/
	}

	static function getParentTabFromModule($module) {
		return '';

		/*
		global $adb;
		if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0) && file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
			include('tabdata.php');
			include('parent_tabdata.php');
			$tabid = $tab_info_array[$module];
			foreach ($parent_child_tab_rel_array as $parid => $childArr) {
				if (in_array($tabid, $childArr)) {
					$parent_tabname = $parent_tab_info_array[$parid];
					break;
				}
			}
			return $parent_tabname;
		} else {
			$sql = "select nectarcrm_parenttab.* from nectarcrm_parenttab inner join nectarcrm_parenttabrel on nectarcrm_parenttabrel.parenttabid=nectarcrm_parenttab.parenttabid inner join nectarcrm_tab on nectarcrm_tab.tabid=nectarcrm_parenttabrel.tabid where nectarcrm_tab.name=?";
			$result = $adb->pquery($sql, array($module));
			$tab = $adb->query_result($result, 0, "parenttab_label");
			return $tab;
		}*/
	}

	/*static function checkParentTabExists($parenttab) {
		global $adb;

		if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
			include('parent_tabdata.php');
			if (in_array($parenttab, $parent_tab_info_array))
				return true;
			else
				return false;
		} else {

			$result = "select 1 from nectarcrm_parenttab where parenttab_label = ?";
			$noofrows = $adb->num_rows($result);
			if ($noofrows > 0)
				return true;
			else
				return false;
		}
	}*/

	static function copyValuesFromRequest($focus) {
		if (isset($_REQUEST['record'])) {
			$focus->id = $_REQUEST['record'];
		}
		if (isset($_REQUEST['mode'])) {
			$focus->mode = $_REQUEST['mode'];
		}
		foreach ($focus->column_fields as $fieldname => $val) {
			if (isset($_REQUEST[$fieldname])) {
				if (is_array($_REQUEST[$fieldname]))
					$value = $_REQUEST[$fieldname];
				else
					$value = trim($_REQUEST[$fieldname]);
				$focus->column_fields[$fieldname] = $value;
			}
		}
	}

	static function createModuleMetaFile() {
		global $adb;

		$sql = "select * from nectarcrm_tab";
		$result = $adb->pquery($sql, array());
		$num_rows = $adb->num_rows($result);
		$result_array = Array();
		$seq_array = Array();
		$ownedby_array = Array();

		for ($i = 0; $i < $num_rows; $i++) {
			$tabid = $adb->query_result($result, $i, 'tabid');
			$tabname = $adb->query_result($result, $i, 'name');
			$presence = $adb->query_result($result, $i, 'presence');
			$ownedby = $adb->query_result($result, $i, 'ownedby');
			$result_array[$tabname] = $tabid;
			$seq_array[$tabid] = $presence;
			$ownedby_array[$tabid] = $ownedby;
		}

		//Constructing the actionname=>actionid array
		$actionid_array = Array();
		$sql1 = "select * from nectarcrm_actionmapping";
		$result1 = $adb->pquery($sql1, array());
		$num_seq1 = $adb->num_rows($result1);
		for ($i = 0; $i < $num_seq1; $i++) {
			$actionname = $adb->query_result($result1, $i, 'actionname');
			$actionid = $adb->query_result($result1, $i, 'actionid');
			$actionid_array[$actionname] = $actionid;
		}

		//Constructing the actionid=>actionname array with securitycheck=0
		$actionname_array = Array();
		$sql2 = "select * from nectarcrm_actionmapping where securitycheck=0";
		$result2 = $adb->pquery($sql2, array());
		$num_seq2 = $adb->num_rows($result2);
		for ($i = 0; $i < $num_seq2; $i++) {
			$actionname = $adb->query_result($result2, $i, 'actionname');
			$actionid = $adb->query_result($result2, $i, 'actionid');
			$actionname_array[$actionid] = $actionname;
		}

		$filename = 'tabdata.php';

		if (file_exists($filename)) {
			if (is_writable($filename)) {
				if (!$handle = fopen($filename, 'w+')) {
					echo "Cannot open file ($filename)";
					exit;
				}
				require_once('modules/Users/CreateUserPrivilegeFile.php');
				$newbuf = '';
				$newbuf .="<?php\n\n";
				$newbuf .="\n";
				$newbuf .= "//This file contains the commonly used variables \n";
				$newbuf .= "\n";
				$newbuf .= "\$tab_info_array=" . constructArray($result_array) . ";\n";
				$newbuf .= "\n";
				$newbuf .= "\$tab_seq_array=" . constructArray($seq_array) . ";\n";
				$newbuf .= "\n";
				$newbuf .= "\$tab_ownedby_array=" . constructArray($ownedby_array) . ";\n";
				$newbuf .= "\n";
				$newbuf .= "\$action_id_array=" . constructSingleStringKeyAndValueArray($actionid_array) . ";\n";
				$newbuf .= "\n";
				$newbuf .= "\$action_name_array=" . constructSingleStringValueArray($actionname_array) . ";\n";
				$newbuf .= "?>";
				fputs($handle, $newbuf);
				fclose($handle);
			} else {
				echo "The file $filename is not writable";
			}
		} else {
			echo "The file $filename does not exist";
		}
	}

	static function createModuleGroupMetaFile() {
		global $adb;
		$sql = "select parenttabid,parenttab_label from nectarcrm_parenttab where visible=0 order by sequence";
		$result = $adb->pquery($sql, array());
		$num_rows = $adb->num_rows($result);
		$result_array = Array();
		for ($i = 0; $i < $num_rows; $i++) {
			$parenttabid = $adb->query_result($result, $i, 'parenttabid');
			$parenttab_label = $adb->query_result($result, $i, 'parenttab_label');
			$result_array[$parenttabid] = $parenttab_label;
		}

		$filename = 'parent_tabdata.php';

		if (file_exists($filename)) {
			if (is_writable($filename)) {
				if (!$handle = fopen($filename, 'w+')) {
					echo "Cannot open file ($filename)";
					exit;
				}
				require_once('modules/Users/CreateUserPrivilegeFile.php');
				$newbuf = '';
				$newbuf .="<?php\n\n";
				$newbuf .="\n";
				$newbuf .= "//This file contains the commonly used variables \n";
				$newbuf .= "\n";
				$newbuf .= "\$parent_tab_info_array=" . constructSingleStringValueArray($result_array) . ";\n";
				$newbuf .="\n";

				$parChildTabRelArray = Array();

				foreach ($result_array as $parid => $parvalue) {
					$childArray = Array();
					//$sql = "select * from nectarcrm_parenttabrel where parenttabid=? order by sequence";
					// vtlib customization: Disabling the tab item based on presence
					$sql = "select * from nectarcrm_parenttabrel where parenttabid=?
						and tabid in (select tabid from nectarcrm_tab where presence in (0,2)) order by sequence";
					// END
					$result = $adb->pquery($sql, array($parid));
					$num_rows = $adb->num_rows($result);
					$result_array = Array();
					for ($i = 0; $i < $num_rows; $i++) {
						$tabid = $adb->query_result($result, $i, 'tabid');
						$childArray[] = $tabid;
					}
					$parChildTabRelArray[$parid] = $childArray;
				}
				$newbuf .= "\n";
				$newbuf .= "\$parent_child_tab_rel_array=" . constructTwoDimensionalValueArray($parChildTabRelArray) . ";\n";
				$newbuf .="\n";
				$newbuf .="\n";
				$newbuf .="\n";
				$newbuf .= "?>";
				fputs($handle, $newbuf);
				fclose($handle);
			} else {
				echo "The file $filename is not writable";
			}
		} else {
			echo "The file $filename does not exist";
		}
	}

	static function getTemplateDetails($templateid) {
		global $adb;
		$returndata = Array();
		$result = $adb->pquery("select body, subject from nectarcrm_emailtemplates where templateid=?", array($templateid));
		$returndata[] = $templateid;
		$returndata[] = $adb->query_result($result, 0, 'body');
		$returndata[] = $adb->query_result($result, 0, 'subject');
		return $returndata;
	}

	static function getAnnouncements() {
		global $adb;
		$sql = " select * from nectarcrm_announcement inner join nectarcrm_users on nectarcrm_announcement.creatorid=nectarcrm_users.id";
		$sql.=" AND nectarcrm_users.is_admin='on' AND nectarcrm_users.status='Active' AND nectarcrm_users.deleted = 0";
		$result = $adb->pquery($sql, array());
		for ($i = 0; $i < $adb->num_rows($result); $i++) {
			$announce = getUserFullName($adb->query_result($result, $i, 'creatorid')) . ' :  ' . $adb->query_result($result, $i, 'announcement') . '   ';
			if ($adb->query_result($result, $i, 'announcement') != '')
				$announcement.=$announce;
		}

	   return $announcement;
	}

	static function getModuleTranslationStrings($language, $module) {
		static $cachedModuleStrings = array();

		if(!empty($cachedModuleStrings[$module])) {
			return $cachedModuleStrings[$module];
		}
		$newStrings = nectarcrm_Language_Handler::getModuleStringsFromFile($language, $module);
		$cachedModuleStrings[$module] = $newStrings['languageStrings'];

		return $cachedModuleStrings[$module];
	}

	static function getTranslatedCurrencyString($str) {
		global $app_currency_strings;
		if (isset($app_currency_strings) && isset($app_currency_strings[$str])) {
			return $app_currency_strings[$str];
		}
		return $str;
	}

	static function getIdOfCustomViewByNameAll($module) {
		global $adb;

		static $cvidCache = array();
		if (!isset($cvidCache[$module])) {
			$qry_res = $adb->pquery("select cvid from nectarcrm_customview where viewname='All' and entitytype=?", array($module));
			$cvid = $adb->query_result($qry_res, 0, "cvid");
			$cvidCache[$module] = $cvid;
		}
		return isset($cvidCache[$module])? $cvidCache[$module] : '0';
	}

	static function SaveTagCloudView($id = "") {
		global $adb;
		$tag_cloud_status = $_REQUEST['tagcloudview'];
		if ($tag_cloud_status == "true") {
			$tag_cloud_view = 0;
		} else {
			$tag_cloud_view = 1;
		}
		if ($id == '') {
			$tag_cloud_view = 1;
		} else {
			$query = "update nectarcrm_homestuff set visible = ? where userid=? and stufftype='Tag Cloud'";
			$adb->pquery($query, array($tag_cloud_view, $id));
		}
	}

	static function clearSmartyCompiledFiles($path = null) {
		global $root_directory;
		if ($path == null) {
			$path = $root_directory . 'test/templates_c/';
		}
		if(file_exists($path) && is_dir($path)){
			$mydir = @opendir($path);
			while (false !== ($file = readdir($mydir))) {
				if ($file != "." && $file != ".." && $file != ".svn") {
					//chmod($path.$file, 0777);
					if (is_dir($path . $file)) {
						chdir('.');
						clear_smarty_cache($path . $file . '/');
						//rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
					} else {
						// Delete only files ending with .tpl.php
						if (strripos($file, '.tpl.php') == (strlen($file) - strlen('.tpl.php'))) {
							unlink($path . $file) or DIE("couldn't delete $path$file<br />");
						}
					}
				}
			}
			@closedir($mydir);
		}
		
	}

	static function getSmartyCompiledTemplateFile($template_file, $path = null) {
		global $root_directory;
		if ($path == null) {
			$path = $root_directory . 'test/templates_c/';
		}
		$mydir = @opendir($path);
		$compiled_file = null;
		while (false !== ($file = readdir($mydir)) && $compiled_file == null) {
			if ($file != "." && $file != ".." && $file != ".svn") {
				//chmod($path.$file, 0777);
				if (is_dir($path . $file)) {
					chdir('.');
					$compiled_file = get_smarty_compiled_file($template_file, $path . $file . '/');
					//rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
				} else {
					// Check if the file name matches the required template fiel name
					if (strripos($file, $template_file . '.php') == (strlen($file) - strlen($template_file . '.php'))) {
						$compiled_file = $path . $file;
					}
				}
			}
		}
		@closedir($mydir);
		return $compiled_file;
	}

	static function postApplicationMigrationTasks() {
		self::clearSmartyCompiledFiles();
		self::createModuleMetaFile();
		self::createModuleMetaFile();
	}

	static function checkFileAccessForInclusion($filepath) {
		global $root_directory;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
		}

		$unsafeDirectories = array('storage', 'cache', 'test');

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath = str_replace('\\', '/', $rootdirpath);

		$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
		$filePathParts = explode('/', $relativeFilePath);

		if (stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
			die('Sorry! Attempt to access restricted file. - '.$filepath);
		}
	}

	/** Function to check the file deletion within the deletable (safe) directories*/
	static function checkFileAccessForDeletion($filepath) {
		global $root_directory;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
		}

		$safeDirectories = array('storage', 'cache', 'test');

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath = str_replace('\\', '/', $rootdirpath);

		$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
		$filePathParts = explode('/', $relativeFilePath);

		if (stripos($realfilepath, $rootdirpath) !== 0 || !in_array($filePathParts[0], $safeDirectories)) {
			die('Sorry! Attempt to access restricted file. - '.$filepath);
		}

	}

	/** Function to check the file access is made within web root directory. */
	static function checkFileAccess($filepath) {
		if (!self::isFileAccessible($filepath)) {
			die('Sorry! Attempt to access restricted file. - '.$filepath);
		}
	}

	/**
	 * function to return whether the file access is made within nectarcrm root directory
	 * and it exists.
	 * @global String $root_directory nectarcrm root directory as given in config.inc.php file.
	 * @param String $filepath relative path to the file which need to be verified
	 * @return Boolean true if file is a valid file within nectarcrm root directory, false otherwise.
	 */
	static function isFileAccessible($filepath) {
		global $root_directory;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
		}

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath = str_replace('\\', '/', $rootdirpath);

		if (stripos($realfilepath, $rootdirpath) !== 0) {
			return false;
		}
		return true;
	}

	static function getSettingsBlockId($label) {
		global $adb;
		$blockid = '';
		$query = "select blockid from nectarcrm_settings_blocks where label = ?";
		$result = $adb->pquery($query, array($label));
		$noofrows = $adb->num_rows($result);
		if ($noofrows == 1) {
			$blockid = $adb->query_result($result, 0, "blockid");
		}
		return $blockid;
	}

	static function getSqlForNameInDisplayFormat($input, $module, $glue = ' ') {
		$entity_field_info = nectarcrm_Functions::getEntityModuleInfoFieldsFormatted($module);
		$fieldsName = $entity_field_info['fieldname'];
		if(is_array($fieldsName)) {
			foreach($fieldsName as $key => $value) {
				$formattedNameList[] = $input[$value];
			}
			$formattedNameListString = implode(",'" . $glue . "',", $formattedNameList);
		} else {
			$formattedNameListString = $input[$fieldsName];
		}
		$sqlString = "CONCAT(" . $formattedNameListString . ")";
		return $sqlString;
	}

	static function getModuleSequenceNumber($module, $recordId) {
		global $adb;
		switch ($module) {
			case "Invoice":
				$res = $adb->query("SELECT invoice_no FROM nectarcrm_invoice WHERE invoiceid = $recordId");
				$moduleSeqNo = $adb->query_result($res, 0, 'invoice_no');
				break;
			case "PurchaseOrder":
				$res = $adb->query("SELECT purchaseorder_no FROM nectarcrm_purchaseorder WHERE purchaseorderid = $recordId");
				$moduleSeqNo = $adb->query_result($res, 0, 'purchaseorder_no');
				break;
			case "Quotes":
				$res = $adb->query("SELECT quote_no FROM nectarcrm_quotes WHERE quoteid = $recordId");
				$moduleSeqNo = $adb->query_result($res, 0, 'quote_no');
				break;
			case "SalesOrder":
				$res = $adb->query("SELECT salesorder_no FROM nectarcrm_salesorder WHERE salesorderid = $recordId");
				$moduleSeqNo = $adb->query_result($res, 0, 'salesorder_no');
				break;
		}
		return $moduleSeqNo;
	}

	static function getModuleFieldTypeOfDataInfos($tables, $tabid='') {
		$result = array();
		if (!empty($tabid)) {
			$module = nectarcrm_Functions::getModuleName($tabid);
			$fieldInfos = nectarcrm_Functions::getModuleFieldInfos($tabid);
			foreach ($fieldInfos as $name => $field) {
				if (($field['displaytype'] == '1' || $field['displaytype'] == '3') &&
						($field['presence'] == '0' || $field['presence'] == '2')) {

					$label = nectarcrm_Functions::getTranslatedString($field['fieldlabel'], $module);
					$result[$name] = array($label => $field['typeofdata']);
				}
			}
		} else {
			throw new Exception('Field lookup by table no longer supported');
		}

		return $result;
	}
    
	static function return_app_list_strings_language($language, $module='nectarcrm') {
		require_once 'includes/runtime/LanguageHandler.php';
		$strings = nectarcrm_Language_Handler::getModuleStringsFromFile($language, $module);
		return $strings['languageStrings'];
	}
}