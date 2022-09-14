<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

Class CustomView_EditAjax_View extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->get('source_module');
		$module = $request->getModule();
		$record = $request->get('record');
		$sourceRecord = $request->get('source_viewname');

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceForModule($moduleModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);

		if(!empty($record)) {
			$customViewModel = CustomView_Record_Model::getInstanceById($record);
			$viewer->assign('MODE', 'edit');
		} else if(!empty($sourceRecord)) {
			$customViewModel = CustomView_Record_Model::getInstanceById($sourceRecord);
			$viewer->assign('MODE', '');
		} else {
			$customViewModel = new CustomView_Record_Model();
			$customViewModel->setModule($moduleName);
			$viewer->assign('MODE', '');
		}

		$viewer->assign('ADVANCE_CRITERIA', $customViewModel->transformToNewAdvancedFilter());
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('DATE_FILTERS', nectarcrm_Field_Model::getDateFilterTypes());

		if($moduleName == 'Calendar'){
			$advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
		} else{
			$advanceFilterOpsByFieldType = nectarcrm_Field_Model::getAdvancedFilterOpsByFieldType();
		}
		$viewer->assign('ADVANCED_FILTER_OPTIONS', nectarcrm_Field_Model::getAdvancedFilterOptions());
		$viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);
		$dateFilters = nectarcrm_Field_Model::getDateFilterTypes();
		foreach($dateFilters as $comparatorKey => $comparatorInfo) {
			$comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
			$comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
			$comparatorInfo['label'] = vtranslate($comparatorInfo['label'],$module);
			$dateFilters[$comparatorKey] = $comparatorInfo;
		}
		$viewer->assign('DATE_FILTERS', $dateFilters);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$recordStructure = $recordStructureInstance->getStructure();
		// for Inventory module we should now allow item details block
		if(in_array($moduleName, getInventoryModules())){
			$itemsBlock = "LBL_ITEM_DETAILS";
			unset($recordStructure[$itemsBlock]);
		}
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Added to show event module custom fields
		if($moduleName == 'Calendar'){
			$relatedModuleName = 'Events';
			$relatedModuleModel = nectarcrm_Module_Model::getInstance($relatedModuleName);
			$relatedRecordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceForModule($relatedModuleModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
			$eventBlocksFields = $relatedRecordStructureInstance->getStructure();
			$viewer->assign('EVENT_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
			$viewer->assign('EVENT_RECORD_STRUCTURE', $eventBlocksFields);
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		$viewer->assign('CUSTOMVIEW_MODEL', $customViewModel);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $module);
		$viewer->assign('SOURCE_MODULE',$moduleName);
		$viewer->assign('USER_MODEL', $currentUserModel);
		$viewer->assign('CV_PRIVATE_VALUE', CustomView_Record_Model::CV_STATUS_PRIVATE);
		$viewer->assign('CV_PENDING_VALUE', CustomView_Record_Model::CV_STATUS_PENDING);
		$viewer->assign('CV_PUBLIC_VALUE', CustomView_Record_Model::CV_STATUS_PUBLIC);
		$viewer->assign('MODULE_MODEL',$moduleModel);

		$allCustomViews = CustomView_Record_Model::getAllByGroup($moduleName);
		$allViewNames = array();
		foreach ($allCustomViews as $views) {
			foreach ($views as $view) {
				if ($currentUserModel->getId() == $view->get('userid')) {
					$allViewNames[$view->getId()] = strtolower(vtranslate($view->get('viewname'), $moduleName));
				}
			}
		}
		$viewer->assign('CUSTOM_VIEWS_LIST', $allViewNames);

		$customViewSharedMembers = $customViewModel->getMembers();
		$listShared = ($customViewModel->get('status') == CustomView_Record_Model::CV_STATUS_PUBLIC) ? true : false;
		foreach ($customViewSharedMembers as $memberGroupLabel => $membersList) {
			if(count($membersList) > 0){
				$listShared = true;
				break;
			}
		}
		$viewer->assign('LIST_SHARED',$listShared);
		$viewer->assign('SELECTED_MEMBERS_GROUP', $customViewSharedMembers);
		$viewer->assign('MEMBER_GROUPS', Settings_Groups_Member_Model::getAll());

		echo $viewer->view('EditView.tpl', $module, true);
	}
}
