<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_MoveReports_Action extends nectarcrm_Mass_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Reports_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$parentModule = 'Reports';
		$reportIdsList = Reports_Record_Model::getRecordsListFromRequest($request);
		$folderId = $request->get('folderid');
                $viewname=$request->get('viewname');
                if($folderId==$viewname){
                    $sameTargetFolder=1;
                }
		if (!empty ($reportIdsList)) {
			foreach ($reportIdsList as $reportId) {
				$reportModel = Reports_Record_Model::getInstanceById($reportId);
				if (!$reportModel->isDefault() && $reportModel->isEditable() && $reportModel->isEditableBySharing()) {
					$reportModel->move($folderId);
				} else {
					$reportsMoveDenied[] = vtranslate($reportModel->getName(), $parentModule);
				}
			}
		}
		$response = new nectarcrm_Response();
		if($sameTargetFolder){
                    $result=array('success'=>false, 'message'=>vtranslate('LBL_SAME_SOURCE_AND_TARGET_FOLDER', $parentModule));
                } 
                else if(empty ($reportsMoveDenied)) {
                    $result=array('success'=>true, 'message'=>vtranslate('LBL_REPORTS_MOVED_SUCCESSFULLY', $parentModule));
                }else {
                    $result = array('success'=>false, 'message'=>vtranslate('LBL_DENIED_REPORTS', $parentModule),'denied'=>$reportsMoveDenied);
		}
                $response->setResult($result);
		$response->emit();
	}
}