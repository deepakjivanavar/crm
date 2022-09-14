<?php
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/utils/CommonUtils.php');
require_once('include/database/PearDatabase.php');
/** Function to  returns the combo field values in array format
  * @param $combofieldNames -- combofieldNames:: Type string array
  * @returns $comboFieldArray -- comboFieldArray:: Type string array
 */
function getComboArray($combofieldNames)
{
	global $log,$mod_strings;
        $log->debug("Entering getComboArray(".$combofieldNames.") method ...");
	global $adb,$current_user;
        $roleid=$current_user->roleid;
	$comboFieldArray = Array();
	foreach ($combofieldNames as $tableName => $arrayName)
	{
		$fldArrName= $arrayName;
		$arrayName = Array();
		
		$sql = "select $tableName from nectarcrm_$tableName";
		$params = array();
		if(!is_admin($current_user))
		{
			$subrole = getRoleSubordinates($roleid);
			if(count($subrole)> 0)
			{
				$roleids = $subrole;
				array_push($roleids, $roleid);
			}
			else
			{
				$roleids = $roleid;
			}
			$sql = "select distinct $tableName from nectarcrm_$tableName  inner join nectarcrm_role2picklist on nectarcrm_role2picklist.picklistvalueid = nectarcrm_$tableName.picklist_valueid where roleid in(". generateQuestionMarks($roleids) .") order by sortid";
			$params = array($roleids);
		}
		$result = $adb->pquery($sql, $params);	
		while($row = $adb->fetch_array($result))
		{
			$val = $row[$tableName];
			$arrayName[$val] = getTranslatedString($val);
		}
		$comboFieldArray[$fldArrName] = $arrayName;
	}
	$log->debug("Exiting getComboArray method ...");
	return $comboFieldArray;	
}
function getUniquePicklistID()
{
	global $adb;
	/*$sql="select id from nectarcrm_picklistvalues_seq";
	$picklistvalue_id = $adb->query_result($adb->pquery($sql, array()),0,'id');

	$qry = "update nectarcrm_picklistvalues_seq set id =?";
	$adb->pquery($qry, array(++$picklistvalue_id));*/
	return $adb->getUniqueID('nectarcrm_picklistvalues');
}

?>
