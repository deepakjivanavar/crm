<?php
require_once('include/utils/utils.php');
require_once 'vtlib/nectarcrm/Module.php';
require_once dirname(__FILE__) .'/ModTracker.php';
class ModTrackerUtils
{
	static function modTrac_changeModuleVisibility($tabid,$status) {
		if($status == 'module_disable'){
			ModTracker::disableTrackingForModule($tabid);
		} else {
			ModTracker::enableTrackingForModule($tabid);
		}
	}
	function modTrac_getModuleinfo(){
		global $adb;
		$query = $adb->pquery("SELECT nectarcrm_modtracker_tabs.visible,nectarcrm_tab.name,nectarcrm_tab.tabid
								FROM nectarcrm_tab
								LEFT JOIN nectarcrm_modtracker_tabs ON nectarcrm_modtracker_tabs.tabid = nectarcrm_tab.tabid
								WHERE nectarcrm_tab.isentitytype = 1 AND nectarcrm_tab.name NOT IN('Emails', 'Webmails')",array());
		$rows = $adb->num_rows($query);

        for($i = 0;$i < $rows; $i++){
			$infomodules[$i]['tabid']  = $adb->query_result($query,$i,'tabid');
			$infomodules[$i]['visible']  = $adb->query_result($query,$i,'visible');
			$infomodules[$i]['name'] = $adb->query_result($query,$i,'name');
		}

		return $infomodules;
	}
}
?>
