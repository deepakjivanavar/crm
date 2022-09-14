<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_ShowWidget_View extends nectarcrm_IndexAjax_View {

	function checkPermission(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$moduleName = $request->getModule();
		$componentName = $request->get('name');
		$linkId = $request->get('linkid');
		if(!empty($componentName)) {
			$className = nectarcrm_Loader::getComponentClassName('Dashboard', $componentName, $moduleName);
			if(!empty($className)) {
				$widget = NULL;
				if(!empty($linkId)) {
					$widget = new nectarcrm_Widget_Model();
					$widget->set('linkid', $linkId);
					$widget->set('userid', $currentUser->getId());
					$widget->set('filterid', $request->get('filterid', NULL));
                    
                    // In nectarcrm7, we need to pin this report widget to first tab of that user
                    $dasbBoardModel = nectarcrm_DashBoard_Model::getInstance($moduleName);
                    $defaultTab = $dasbBoardModel->getUserDefaultTab($currentUser->getId());
                    $widget->set('tabid', $request->get('tab', $defaultTab['id']));
                    
					if ($request->has('data')) {
						$widget->set('data', $request->get('data'));
					}
					$widget->add();
				}
				
				//Date conversion from user format to database format
				$createdTime = $request->get('createdtime');
				//user format dates should be used in getSearchParams() api
				$request->set('dateFilter', $createdTime);
				if(!empty($createdTime)) {
					$startDate = nectarcrm_Date_UIType::getDBInsertedValue($createdTime['start']);
					$dates['start'] = getValidDBInsertDateTimeValue($startDate . ' 00:00:00');
					$endDate = nectarcrm_Date_UIType::getDBInsertedValue($createdTime['end']);
					$dates['end'] = getValidDBInsertDateTimeValue($endDate . ' 23:59:59');
				}
				$request->set('createdtime', $dates);
				
				$classInstance = new $className();
				$classInstance->process($request, $widget);
				return;
			}
	}

		$response = new nectarcrm_Response();
		$response->setResult(array('success'=>false,'message'=>  vtranslate('NO_DATA')));
		$response->emit();
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}