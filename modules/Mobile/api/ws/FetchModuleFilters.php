<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
class Mobile_WS_FetchModuleFilters extends Mobile_WS_Controller {
	
	function process(Mobile_API_Request $request) {
		$module = $request->get('module');
		$current_user = $this->getActiveUser();

		$allFilters = CustomView_Record_Model::getAllByGroup($module);
		unset($allFilters['Public']);
		$result = array();
		if($allFilters) {
			foreach($allFilters as $group => $filters) {
				$result[$group] = array();
				foreach($filters as $filter) {
					$result[$group][] = array('id'=>$filter->get('cvid'), 'name'=>$filter->get('viewname'), 'default'=>$filter->isDefault()); 
				}
			}
		}
		$response = new Mobile_API_Response();
		$response->setResult(array('filters'=>$result, 'moduleLabel' => vtranslate($module, $module)));
		return $response;
	}

	protected function getModuleFilters($moduleName, $user) {
		
		$filters = array();
		
		global $adb;
		$sql = "SELECT nectarcrm_customview.*, nectarcrm_users.user_name FROM nectarcrm_customview 
			INNER JOIN nectarcrm_users ON nectarcrm_customview.userid = nectarcrm_users.id WHERE nectarcrm_customview.entitytype=?";
		$parameters = array($moduleName);

		if(!is_admin($user)) {
			require('user_privileges/user_privileges_'.$user->id.'.php');
			
			$sql .= " AND (nectarcrm_customview.status=0 or nectarcrm_customview.userid = ? or nectarcrm_customview.status = 3 or nectarcrm_customview.userid IN
			(SELECT nectarcrm_user2role.userid FROM nectarcrm_user2role INNER JOIN nectarcrm_users on nectarcrm_users.id=nectarcrm_user2role.userid 
			INNER JOIN nectarcrm_role on nectarcrm_role.roleid=nectarcrm_user2role.roleid WHERE nectarcrm_role.parentrole LIKE '".$current_user_parent_role_seq."::%'))";
			
			array_push($parameters, $current_user->id);
		}
		
		$result = $adb->pquery($sql, $parameters);
		if($result && $adb->num_rows($result)) {
			while($resultrow = $adb->fetch_array($result)) {
				$filters[] = $this->prepareFilterDetailUsingResultRow($resultrow);
			}
		}
		
		return $filters;
	}
	
	protected function prepareFilterDetailUsingResultRow($resultrow) {
		$filter = array();
		$filter['cvid'] = $resultrow['cvid'];
		$filter['viewname'] = decode_html($resultrow['viewname']);
		$filter['setdefault'] = $resultrow['setdefault'];
		$filter['setmetrics'] = $resultrow['setmetrics'];
		$filter['moduleName'] = decode_html($resultrow['entitytype']);
		$filter['status']     = decode_html($resultrow['status']);
		$filter['userName']   = decode_html($resultrow['user_name']);
		return $filter;
	}
}