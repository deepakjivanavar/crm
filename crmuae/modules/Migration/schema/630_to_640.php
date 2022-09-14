<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *********************************************************************************/

if(defined('NECTARCRM_UPGRADE')) {

//Start add new currency - 'CFP Franc or Pacific Franc' 
global $adb;

$query = 'UPDATE nectarcrm_currencies_seq SET id = (SELECT currencyid FROM nectarcrm_currencies ORDER BY currencyid DESC LIMIT 1)';
$adb->pquery($query, array());

$uniqId = $adb->getUniqueID('nectarcrm_currencies'); 
$result = $adb->pquery('SELECT 1 FROM nectarcrm_currencies WHERE currency_name = ?',array('CFP Franc')); 
 
if($adb->num_rows($result)<=0){ 
Migration_Index_View::ExecuteQuery('INSERT INTO nectarcrm_currencies VALUES (?,?,?,?)', array($uniqId, 'CFP Franc', 'XPF', 'F')); 
} 

//Adding new timezone (GMT+11:00) New Caledonia 
    $sortOrderResult = $adb->pquery("SELECT sortorderid FROM nectarcrm_time_zone WHERE time_zone = ?", array('Asia/Yakutsk'));
    if ($adb->num_rows($sortOrderResult)) {
        $sortOrderId = $adb->query_result($sortOrderResult, 0, 'sortorderid');
        $adb->pquery("UPDATE nectarcrm_time_zone SET sortorderid = (sortorderid + 1) WHERE sortorderid > ?", array($sortOrderId));
        Migration_Index_View::ExecuteQuery('INSERT INTO nectarcrm_time_zone (time_zone, sortorderid, presence) VALUES (?, ?, ?)', array('Etc/GMT-11', ($sortOrderId + 1), 1));
        echo "New timezone (GMT+11:00) New Caledonia added.<br>";
    }

//updating the config file to support multiple layout
$filename = 'config.inc.php';
if (file_exists($filename)) {
    $contents = file_get_contents($filename);
    if (empty($contents)) {
        echo '<tr><td width="80%"><span>Your Configuration file couldnot able to edit, Please asdd it manually</span></td><td style="color:red">Unsuccess</td></tr>';
    } else {
        $config_content = explode('?>', $contents);
        if (strpos($config_content[0], '$default_layout') == false) {
            $config_code = "// Set the default layout 
\$default_layout = 'vlayout';

include_once 'config.security.php';
?>";
            $contents = $config_content[0] .  $config_code;
        }
        file_put_contents($filename, $contents);
        echo '<tr><td width="80%"><span>Configuration file Updated</span></td><td style="color:green">Success</td></tr>';
    }
}
}
?>
