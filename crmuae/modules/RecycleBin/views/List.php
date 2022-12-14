<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class RecycleBin_List_View extends nectarcrm_Index_View {

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	function preProcess(nectarcrm_Request $request, $display=true) {
		parent::preProcess($request, false);
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$moduleModel = RecycleBin_Module_Model::getInstance($moduleName);

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));

		$quickLinkModels = $moduleModel->getSideBarLinks($linkParams);

		$viewer->assign('QUICK_LINKS', $quickLinkModels);
		$this->initializeListViewContents($request, $viewer);

		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(nectarcrm_Request $request) {
		return 'ListViewPreProcess.tpl';
	}

	function process (nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$this->initializeListViewContents($request, $viewer);

		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('ListViewContents.tpl', $moduleName);
	}

	function postProcess(nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$viewer->view('ListViewPostProcess.tpl', $moduleName);
		parent::postProcess($request);
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
		$moduleName = $request->getModule();
		$sourceModule = $request->get('sourceModule');

		$pageNumber = $request->get('page');
		$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		$operator = $request->get('operator');
		$searchParams = $request->get('search_params');
		$listHeaders = $request->get('list_headers', array());

		$orderParams = nectarcrm_ListView_Model::getSortParamsSession($moduleName . '_' . $sourceModule);
		if ($request->get('mode') == 'removeSorting') {
			nectarcrm_ListView_Model::deleteParamsSession($moduleName . '_' . $sourceModule, array('orderby', 'sortorder'));
			$orderBy = '';
			$sortOrder = '';
		}
		if (empty($listHeaders)) {
			$listHeaders = $orderParams['list_headers'];
		}
		if (empty($orderBy) && empty($searchValue) && empty($pageNumber) && empty($searchParams)) {
			if ($orderParams) {
				$pageNumber = $orderParams['page'];
				$orderBy = $orderParams['orderby'];
				$sortOrder = $orderParams['sortorder'];
				$searchKey = $orderParams['search_key'];
				$searchValue = $orderParams['search_value'];
				$operator = $orderParams['operator'];
				$searchParams = $orderParams['search_params'];
				$starFilterMode = $orderParams['star_filter_mode'];
			}
		} else if ($request->get('nolistcache') != 1) {
			$params = array('page' => $pageNumber, 'orderby' => $orderBy, 'sortorder' => $sortOrder, 'search_key' => $searchKey,
				'search_value' => $searchValue, 'operator' => $operator, 'search_params' => $searchParams, 'star_filter_mode' => $starFilterMode);
			if (!empty($listHeaders)) {
				$params['list_headers'] = $listHeaders;
			}
			nectarcrm_ListView_Model::setSortParamsSession($moduleName . '_' . $sourceModule, $params);
		}

		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
			$faSortImage = "fa-sort-desc";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
			$faSortImage = "fa-sort-asc";
		}

		if(empty ($pageNumber)){
			$pageNumber = '1';
		}

		$moduleModel = RecycleBin_Module_Model::getInstance($moduleName);
		//If sourceModule is empty, pick the first module name from the list
		if(empty($sourceModule)) {
			foreach($moduleModel->getAllModuleList() as $model) {
				$sourceModule = $model->get('name');
				break;
			}
		}
		$listViewModel = RecycleBin_ListView_Model::getInstance($moduleName, $sourceModule);

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $moduleModel->getListViewMassActions($linkParams);

		 // preProcess is already loading this, we can reuse
		if (!$this->pagingModel) {
			$pagingModel = new nectarcrm_Paging_Model();
			$pagingModel->set('page', $pageNumber);
		} else {
			$pagingModel = $this->pagingModel;
		}

		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder',$sortOrder);
		}

		if (empty($searchParams)) {
			$searchParams = array();
		}
		$transformedSearchParams = nectarcrm_Util_Helper::transferListSearchParamsToFilterCondition($searchParams, $listViewModel->getModule());
		$listViewModel->set('search_params', $transformedSearchParams);

		//To make smarty to get the details easily accesible
		foreach ($searchParams as $fieldListGroup) {
			foreach ($fieldListGroup as $fieldSearchInfo) {
				$fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
				$fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
				$fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
				$searchParams[$fieldName] = $fieldSearchInfo;
			}
		}

		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}

		if(!$this->pagingModel){
			$this->pagingModel = $pagingModel;
		}

		$noOfEntries = count($this->listViewEntries);

		$viewer->assign('MODULE', $moduleName);

		$viewer->assign('LISTVIEW_LINKS', $moduleModel->getListViewLinks());
		$viewer->assign('LISTVIEW_MASSACTIONS', $linkModels);

		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);

		$viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
		$viewer->assign('MODULE_LIST', $moduleModel->getAllModuleList());
		$viewer->assign('SOURCE_MODULE',$sourceModule);
		$viewer->assign('IS_RECORDS_DELETED', $moduleModel->isRecordsDeleted());
		$viewer->assign('SEARCH_DETAILS', $searchParams);
		$viewer->assign('NO_SEARCH_PARAMS_CACHE', $request->get('nolistcache'));
		$viewer->assign('FASORT_IMAGE',$faSortImage);

		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
			if(!$this->listViewCount){
				$this->listViewCount = $listViewModel->getListViewCount();
			}
			$totalCount = $this->listViewCount;
			$pageLimit = $pagingModel->getPageLimit();
			$pageCount = ceil((int) $totalCount / (int) $pageLimit);

			if($pageCount == 0){
				$pageCount = 1;
			}
			$viewer->assign('PAGE_COUNT', $pageCount);
			$viewer->assign('LISTVIEW_COUNT', $totalCount);
		}
		$viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));

	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.nectarcrm.resources.List',
			"modules.$moduleName.resources.List",
			'modules.CustomView.resources.CustomView',
			"modules.$moduleName.resources.CustomView",
			"modules.Emails.resources.MassEdit",
			"modules.nectarcrm.resources.CkEditor",
			"modules.nectarcrm.resources.ListSidebar",
			"~layouts/v7/lib/jquery/sadropdown.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	/**
	 * Function to get the page count for list
	 * @return total number of pages
	 */
	function getPageCount(nectarcrm_Request $request){
		$moduleName = $request->getModule();
		$sourceModule = $request->get('sourceModule');
		$listViewModel = RecycleBin_ListView_Model::getInstance($moduleName, $sourceModule);
		$searchParams = $request->get('search_params');
		$listViewModel->set('search_params',nectarcrm_Util_Helper::transferListSearchParamsToFilterCondition($searchParams, $listViewModel->getModule()));

		$listViewCount = $listViewModel->getListViewCount($request);
		$pagingModel = new nectarcrm_Paging_Model();
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $listViewCount / (int) $pageLimit);

		if($pageCount == 0){
			$pageCount = 1;
		}
		$result = array();
		$result['page'] = $pageCount;
		$result['numberOfRecords'] = $listViewCount;
		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function returns the number of records for the current filter
	 * @param nectarcrm_Request $request
	 */
	function getRecordsCount(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$sourceModule = $request->get('sourceModule');
		$listViewModel = RecycleBin_ListView_Model::getInstance($moduleName, $sourceModule);
		$searchParams = $request->get('search_params');
		$listViewModel->set('search_params',nectarcrm_Util_Helper::transferListSearchParamsToFilterCondition($searchParams, $listViewModel->getModule()));

		$count = $listViewModel->getListViewCount();

		$result = array();
		$result['module'] = $moduleName;
		$result['count'] = $count;

		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	 /**
	 * Setting module related Information to $viewer (for nectarcrm7)
	 * @param type $request
	 * @param type $moduleModel
	 */
	public function setModuleInfo($request, $moduleModel) {
		$fieldsInfo = array();
		$basicLinks = array();
		$settingLinks = array();
		$sourceModule = $request->get('sourceModule');

		$moduleModel = RecycleBin_Module_Model::getInstance($request->getModule());
		//If sourceModule is empty, pick the first module name from the list
		if (empty($sourceModule)) {
			foreach ($moduleModel->getAllModuleList() as $model) {
				$sourceModule = $model->get('name');
				break;
			}
		}
		$moduleModel = nectarcrm_Module_Model::getInstance($sourceModule);

		$moduleFields = $moduleModel->getFields();
		foreach ($moduleFields as $fieldName => $fieldModel) {
			$fieldsInfo[$fieldName] = $fieldModel->getFieldInfo();
		}

		$moduleBasicLinks = $moduleModel->getModuleBasicLinks();
		foreach ($moduleBasicLinks as $basicLink) {
			$basicLinks[] = nectarcrm_Link_Model::getInstanceFromValues($basicLink);
		}

		$moduleSettingLinks = $moduleModel->getSettingLinks();
		foreach ($moduleSettingLinks as $settingsLink) {
			$settingLinks[] = nectarcrm_Link_Model::getInstanceFromValues($settingsLink);
		}
		$viewer = $this->getViewer($request);
		$viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
		$viewer->assign('MODULE_BASIC_ACTIONS', $basicLinks);
		$viewer->assign('MODULE_SETTING_ACTIONS', $settingLinks);
	}

	public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
}