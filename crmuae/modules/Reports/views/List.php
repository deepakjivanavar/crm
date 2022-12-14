<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Reports_List_View extends nectarcrm_Index_View {

	protected $listViewHeaders = false;
	protected $listViewEntries = false;
	protected $listViewCount   = false;

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Reports_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}



	function preProcess(nectarcrm_Request $request, $display=true) {
		parent::preProcess($request, false);

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$folders = $moduleModel->getFolders();
		$listViewModel = new Reports_ListView_Model();
		$listViewModel->set('module', $moduleModel);
		$linkModels = $listViewModel->getListViewLinks();
		$listViewMassActionModels = $listViewModel->getListViewMassActions();
		$viewer->assign('LISTVIEW_LINKS', $linkModels);
		$viewer->assign('LISTVIEW_MASSACTIONS', $listViewMassActionModels);
		$viewer->assign('FOLDERS', $folders);
		$reportModel = Reports_Record_Model::getCleanInstance();
		$this->initializeListViewContents($request);

		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(nectarcrm_Request $request) {
		return 'ListViewPreProcess.tpl';
	}
	function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$this->initializeListViewContents($request);
		$viewer->view('ListViewContents.tpl', $moduleName);
	}

	function postProcess(nectarcrm_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$viewer->view('ListViewPostProcess.tpl', $moduleName);
		parent::postProcess($request);
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
			"modules.nectarcrm.resources.Detail",
			"modules.$moduleName.resources.Detail",
			"modules.nectarcrm.resources.dashboards.Widget",
			'modules.nectarcrm.resources.List',
			"modules.$moduleName.resources.List",
			"modules.$moduleName.resources.ChartDetail",
			"modules.nectarcrm.resources.ListSidebar",
			'~/libraries/jquery/vtchart.js',
			"~layouts/v7/lib/jquery/sadropdown.js",
			"~layouts/" .nectarcrm_Viewer::getDefaultLayoutName(). "/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/" .nectarcrm_Viewer::getDefaultLayoutName(). "/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function initializeListViewContents(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$folderId = $request->get('viewname');
		if(empty($folderId) || $folderId == 'undefined'){
			$folderId = nectarcrm_ListView_Model::getSortParamsSession($moduleName.'_folderId');
			if(empty($folderId)) {
				$folderId = 'All';
			}
		} else {
			nectarcrm_ListView_Model::setSortParamsSession($moduleName.'_folderId', $folderId);
		}
		$pageNumber = $request->get('page');
		$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		$searchParams = $request->get('search_params');
		$searchParams = $searchParams[0];

		$orderParams = nectarcrm_ListView_Model::getSortParamsSession($moduleName.'_'.$folderId);
		if($request->get('mode') == 'removeSorting') {
			nectarcrm_ListView_Model::deleteParamsSession($moduleName.'_'.$folderId, array('orderby', 'sortorder'));
			$orderBy = '';
			$sortOrder = '';
		}
		if(empty($orderBy) && empty($pageNumber)) {
			$orderParams = nectarcrm_ListView_Model::getSortParamsSession($moduleName.'_'.$folderId);
			if($orderParams) {
				$pageNumber = $orderParams['page'];
				$orderBy = $orderParams['orderby'];
				$sortOrder = $orderParams['sortorder'];
			}
		} else if($request->get('nolistcache') != 1) {
			$params = array('page' => $pageNumber, 'orderby' => $orderBy, 'sortorder' => $sortOrder, 'search_params' =>$searchParams);
			nectarcrm_ListView_Model::setSortParamsSession($moduleName.'_'.$folderId, $params);
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

		$listViewModel = new Reports_ListView_Model();
		$listViewModel->set('module', $moduleModel);
		$listViewModel->set('folderid', $folderId);

		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder', $sortOrder);
		}
		$listViewMassActionModels = $listViewModel->getListViewMassActions();
		if(empty ($pageNumber)){
			$pageNumber = '1';
		}

		if(empty($searchParams)) {
			$searchParams = array();
		}
		$listViewModel->set('search_params', $searchParams);

		$viewer->assign('MODULE', $moduleName);
		  // preProcess is already loading this, we can reuse
		if(!$this->pagingModel){
			$pagingModel = new nectarcrm_Paging_Model();
			$pagingModel->set('page', $pageNumber);
		} else{
			$pagingModel = $this->pagingModel;
		}

		$viewer->assign('LISTVIEW_MASSACTIONS', $listViewMassActionModels);

		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders($folderId);
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$noOfEntries = count($this->listViewEntries);
		$viewer->assign('PAGE_NUMBER',$pageNumber);
		$viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('VIEWNAME',$folderId);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('FASORT_IMAGE',$faSortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('SEARCH_DETAILS', array());
		$viewer->assign('LISTVIEW_MODEL',$listViewModel);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		if(!$this->pagingModel){
			$this->pagingModel = $pagingModel;
		}

		if(!empty($searchParams)) {
			$listSearchParams = array();
			foreach($searchParams as $conditions) {
				$fieldname = $conditions[0];
				$searchValue = $conditions[2];
				$comparator = $conditions[1];
				$listSearchParams[$fieldname] = array('searchValue' => $searchValue, 'comparator' => $comparator);
			}
			$viewer->assign('SEARCH_DETAILS', $listSearchParams);
		}
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
		$dashBoardModel = new nectarcrm_DashBoard_Model();
		$activeTabs = $dashBoardModel->getActiveTabs();
		foreach($activeTabs as $index => $tabInfo) {
			if(!empty($tabInfo['appname'])) {
				unset($activeTabs[$index]);
			}
		}
		$viewer->assign('DASHBOARD_TABS', $activeTabs);
	}

	/**
	 * Function returns the number of records for the current filter
	 * @param nectarcrm_Request $request
	 */
	function getRecordsCount(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		$count = $this->getListViewCount($request);

		$result = array();
		$result['module'] = $moduleName;
		$result['viewname'] = $cvId;
		$result['count'] = $count;

		$response = new nectarcrm_Response();
		$response->setEmitType(nectarcrm_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function to get listView count
	 * @param nectarcrm_Request $request
	 */
	function getListViewCount(nectarcrm_Request $request){
		$folderId = $request->get('viewname');
		if(empty($folderId)){
			$folderId = 'All';
		}
		$listViewModel = new Reports_ListView_Model();
		$listViewModel->set('folderid', $folderId);
		$searchParams = $request->get('search_params');
		if(!empty($searchParams[0])) {
			$listViewModel->set('search_params', $searchParams[0]);
		}
		$count = $listViewModel->getListViewCount();

		return $count;
	}



	/**
	 * Function to get the page count for list
	 * @return total number of pages
	 */
	function getPageCount(nectarcrm_Request $request){
		$listViewCount = $this->getListViewCount($request);
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