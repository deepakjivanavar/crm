<?php
/*********************************************************************************

** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
global $app_strings,$mod_strings, $list_max_entries_per_page, $currentModule, $theme, $current_language, $current_user;

$smarty = new nectarcrmCRM_Smarty();
$category = getParentTab();

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/nectarcrm/layout_utils.php');

$smarty->assign("SESSION_WHERE",$_SESSION['export_where']);

$smarty->assign('APP',$app_strings);
$smarty->assign('MOD',$mod_strings);
$smarty->assign("THEME", $theme_path);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("CATEGORY",$category);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("MODULELABEL",getTranslatedString($currentModule));
$smarty->assign("IDSTRING",vtlib_purify($_REQUEST['idstring']));
$smarty->assign("EXCLUDED_RECORDS",vtlib_purify($_REQUEST['excludedRecords']));
$smarty->assign("PERPAGE",$list_max_entries_per_page);

if(!is_admin($current_user) && (isPermitted($currentModule, 'Export') != 'yes')) {	
	$smarty->display(vtlib_getModuleTemplate('nectarcrm','OperationNotPermitted.tpl'));	
} else {
	$smarty->display('ExportRecords.tpl');
}


?>
