<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Detail_View extends nectarcrm_Index_View {
	protected $record = false;
	protected $isAjaxEnabled = null;

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');
		$this->exposeMethod('showModuleDetailView');
		$this->exposeMethod('showModuleSummaryView');
		$this->exposeMethod('showModuleBasicView');
		$this->exposeMethod('showRecentActivities');
		$this->exposeMethod('showRecentComments');
		$this->exposeMethod('showRelatedList');
		$this->exposeMethod('showChildComments');
		$this->exposeMethod('getActivities');
		$this->exposeMethod('showRelatedRecords');
	}

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if ($recordId) {
			$recordEntityName = getSalesEntityType($recordId);
			if ($recordEntityName !== $moduleName) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
		}
		return true;
	}

	function preProcess(nectarcrm_Request $request, $display=true) {
		parent::preProcess($request, false);

		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if(!$this->record){
			$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$summaryInfo = array();
		// Take first block information as summary information
		$stucturedValues = $recordStrucure->getStructure();
		foreach($stucturedValues as $blockLabel=>$fieldList) {
			$summaryInfo[$blockLabel] = $fieldList;
			break;
		}

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
		$navigationInfo = ListViewSession::getListViewNavigation($recordId);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('NAVIGATION', $navigationInfo);

		//Intially make the prev and next records as null
		$prevRecordId = null;
		$nextRecordId = null;
		$found = false;
		if ($navigationInfo) {
			foreach($navigationInfo as $page=>$pageInfo) {
				foreach($pageInfo as $index=>$record) {
					//If record found then next record in the interation
					//will be next record
					if($found) {
						$nextRecordId = $record;
						break;
					}
					if($record == $recordId) {
						$found = true;
					}
					//If record not found then we are assiging previousRecordId
					//assuming next record will get matched
					if(!$found) {
						$prevRecordId = $record;
					}
				}
				//if record is found and next record is not calculated we need to perform iteration
				if($found && !empty($nextRecordId)) {
					break;
				}
			}
		}

		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if(!empty($prevRecordId)) {
			$viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
		}
		if(!empty($nextRecordId)) {
			$viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
		}

		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
		$viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $this->record->getSideBarLinks($linkParams);
		$viewer->assign('QUICK_LINKS', $linkModels);
		$viewer->assign('MODULE_NAME', $moduleName);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));

		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', nectarcrm_Functions::jsonEncode($picklistDependencyDatasource));

		$tagsList = nectarcrm_Tag_Model::getAllAccessible($currentUserModel->getId(), $moduleName, $recordId);
		$allUserTags = nectarcrm_Tag_Model::getAllUserTags($currentUserModel->getId());
		$viewer->assign('TAGS_LIST', $tagsList);
		$viewer->assign('ALL_USER_TAGS', $allUserTags);

		$appName = $request->get('app');
		if(!empty($appName)){
			$viewer->assign('SELECTED_MENU_CATEGORY',$appName);
		}

		$selectedTabLabel = $request->get('tab_label');
		$relationId = $request->get('relationId');

		if(empty($selectedTabLabel)) {
			if($currentUserModel->get('default_record_view') === 'Detail') {
				$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
			} else{
				if($moduleModel->isSummaryViewSupported()) {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
				} else {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
				}
			}
		}

		$viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
		$viewer->assign('SELECTED_RELATION_ID',$relationId);

		//nectarcrm7 - TO show custom view name in Module Header
		$viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));

		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(nectarcrm_Request $request) {
		return 'DetailViewPreProcess.tpl';
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if ($currentUserModel->get('default_record_view') === 'Summary') {
			echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}
	}

	public function postProcess(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if($moduleName=="Calendar"){
			$recordModel = nectarcrm_Record_Model::getInstanceById($recordId);
			$activityType = $recordModel->getType();
			if($activityType=="Events"){
				$moduleName="Events";
			}
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		if(!$this->record){
			$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$selectedTabLabel = $request->get('tab_label');
		$relationId = $request->get('relationId');

		if(empty($selectedTabLabel)) {
			if($currentUserModel->get('default_record_view') === 'Detail') {
				$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
			} else{
				if($moduleModel->isSummaryViewSupported()) {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
				} else {
					$selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
				}
			}
		}

		$viewer = $this->getViewer($request);

		$viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
		$viewer->assign('SELECTED_RELATION_ID',$relationId);
		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->view('DetailViewPostProcess.tpl', $moduleName);

		parent::postProcess($request);
	}


	public function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.nectarcrm.resources.Detail',
			"modules.$moduleName.resources.Detail",
			'modules.nectarcrm.resources.RelatedList',
			"modules.$moduleName.resources.RelatedList",
			'libraries.jquery.jquery_windowmsg',
			"libraries.jquery.ckeditor.ckeditor",
			"libraries.jquery.ckeditor.adapters.jquery",
			"modules.Emails.resources.MassEdit",
			"modules.nectarcrm.resources.CkEditor",
			"~/libraries/jquery/twitter-text-js/twitter-text.js",
			"libraries.jquery.multiplefileupload.jquery_MultiFile",
			'~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js',
			'~/libraries/jquery.bxslider/jquery.bxslider.min.js',
			"~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.js",
			'modules.nectarcrm.resources.Tag',
			'modules.Google.resources.Map'
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	function showDetailViewByMode($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		return $this->showModuleBasicView($request);
	}

	/**
	 * Function shows the entire detail for the record
	 * @param nectarcrm_Request $request
	 * @return <type>
	 */
	function showModuleDetailView(nectarcrm_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
		$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

		$moduleModel = $recordModel->getModule();

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE', $moduleName);

		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', nectarcrm_Functions::jsonEncode($picklistDependencyDatasource));

		if ($request->get('displayMode') == 'overlay') {
			$viewer->assign('MODULE_MODEL', $moduleModel);
			$this->setModuleInfo($request, $moduleModel);
			$viewer->assign('SCRIPTS',$this->getOverlayHeaderScripts($request));

			$detailViewLinkParams = array('MODULE'=>$moduleName, 'RECORD'=>$recordId);
			$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
			$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
			return $viewer->view('OverlayDetailView.tpl', $moduleName);
		} else {
			return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
		}
	}
	public function getOverlayHeaderScripts(nectarcrm_Request $request){
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.$moduleName.resources.Detail",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;	
	}

	function showModuleSummaryView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$pagingModel = new nectarcrm_Paging_Model();
		$viewer->assign('PAGING_MODEL', $pagingModel);

		$picklistDependencyDatasource = nectarcrm_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', nectarcrm_Functions::jsonEncode($picklistDependencyDatasource));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {

		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = nectarcrm_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = nectarcrm_RecordStructure_Model::getInstanceFromRecordModel($recordModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

		$moduleModel = $recordModel->getModule();
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}

	/**
	 * Added to support Engagements view in nectarcrm7
	 * @param nectarcrm_Request $request
	 */
	function _showRecentActivities(nectarcrm_Request $request){
		$parentRecordId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel,$moduleName);
		$pagingModel->calculatePageRange($recentActivities);

		if($pagingModel->getCurrentPage() == ModTracker_Record_Model::getTotalRecordCount($parentRecordId)/$pagingModel->getPageLimit()) {
			$pagingModel->set('nextPageExists', false);
		}
		$recordModel = nectarcrm_Record_Model::getInstanceById($parentRecordId);
		$viewer = $this->getViewer($request);
		$viewer->assign('SOURCE',$recordModel->get('source'));
		$viewer->assign('RECENT_ACTIVITIES', $recentActivities);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('RECORD_ID',$parentRecordId);
	}

	/**
	 * Function returns recent changes made on the record
	 * @param nectarcrm_Request $request
	 */
	function showRecentActivities (nectarcrm_Request $request){
		$moduleName = $request->getModule();
		$this->_showRecentActivities($request);

		$viewer = $this->getViewer($request);
		echo $viewer->view('RecentActivities.tpl', $moduleName, true);
	}

	/**
	 * Function returns latest comments
	 * @param nectarcrm_Request $request
	 * @return <type>
	 */
	function showRecentComments(nectarcrm_Request $request) {
		$parentId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		if($request->get('rollup-toggle')) {
			$rollupsettings = ModComments_Module_Model::storeRollupSettingsForUser($currentUserModel, $request);
		} else {
			$rollupsettings = ModComments_Module_Model::getRollupSettingsForUser($currentUserModel, $moduleName);
		}

		if($rollupsettings['rollup_status']) {
			$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentId, $moduleName);
			$recentComments = $parentRecordModel->getRollupCommentsForModule(0, 6);
		}else {
			$recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel);
		}

		$pagingModel->calculatePageRange($recentComments);
		if ($pagingModel->get('limit') < count($recentComments)) {
			array_pop($recentComments);
		}

		$modCommentsModel = nectarcrm_Module_Model::getInstance('ModComments');
		$fileNameFieldModel = nectarcrm_Field::getInstance("filename", $modCommentsModel);
		$fileFieldModel = nectarcrm_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);

		$viewer = $this->getViewer($request);
		$viewer->assign('COMMENTS', $recentComments);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('FIELD_MODEL', $fileFieldModel);
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', nectarcrm_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', nectarcrm_Util_Helper::getMaxUploadSizeInBytes());
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
		$viewer->assign('ROLLUP_STATUS', $rollupsettings['rollup_status']);
		$viewer->assign('ROLLUPID', $rollupsettings['rollupid']);
		$viewer->assign('PARENT_RECORD', $parentId);

		return $viewer->view('RecentComments.tpl', $moduleName, 'true');
	}

	/**
	 * Function returns related records
	 * @param nectarcrm_Request $request
	 * @return <type>
	 */
	function showRelatedList(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$relatedModuleName = $request->get('relatedModule');
		$targetControllerClass = null;

		if($relatedModuleName == 'ModComments') {
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$rollupSettings = ModComments_Module_Model::getRollupSettingsForUser($currentUserModel, $moduleName);
			$request->set('rollup_settings', $rollupSettings);
		}

		// Added to support related list view from the related module, rather than the base module.
		try {
			$targetControllerClass = nectarcrm_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
		}catch(AppException $e) {
			try {
				// If any module wants to have same view for all the relation, then invoke this.
				$targetControllerClass = nectarcrm_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
			}catch(AppException $e) {
				// Default related list
				$targetControllerClass = nectarcrm_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
			}
		}
		if($targetControllerClass) {
			$targetController = new $targetControllerClass();
			return $targetController->process($request);
		}
	}

	/**
	 * Function sends the child comments for a comment
	 * @param nectarcrm_Request $request
	 * @return <type>
	 */
	function showChildComments(nectarcrm_Request $request) {
		$parentCommentId = $request->get('commentid');
		$parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
		$childComments = $parentCommentModel->getChildComments();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$modCommentsModel = nectarcrm_Module_Model::getInstance('ModComments');

		$viewer = $this->getViewer($request);
		$viewer->assign('PARENT_COMMENTS', $childComments);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);

		return $viewer->view('CommentsList.tpl', $moduleName, 'true');
	}

	/**
	 * Function to get Ajax is enabled or not
	 * @param nectarcrm_Record_Model record model
	 * @return <boolean> true/false
	 */
	function isAjaxEnabled($recordModel) {
		if(is_null($this->isAjaxEnabled)){
			$this->isAjaxEnabled = $recordModel->isEditable();
		}
		return $this->isAjaxEnabled;
	}

	/**
	 * Function to get activities
	 * @param nectarcrm_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(nectarcrm_Request $request) {
		return '';
	}


	/**
	 * Function returns related records based on related moduleName
	 * @param nectarcrm_Request $request
	 * @return <type>
	 */
	function showRelatedRecords(nectarcrm_Request $request) {
		$parentId = $request->get('record');
		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$relatedModuleName = $request->get('relatedModule');
		$moduleName = $request->getModule();

		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$pagingModel = new nectarcrm_Paging_Model();
		$pagingModel->set('page', $pageNumber);
		if(!empty($limit)) {
			$pagingModel->set('limit', $limit);
		}

		$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentId, $moduleName);
		$relationListView = nectarcrm_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
		$models = $relationListView->getEntries($pagingModel);
		$header = $relationListView->getHeaders();

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE' , $moduleName);
		$viewer->assign('RELATED_RECORDS' , $models);
		$viewer->assign('RELATED_HEADERS', $header);
		$viewer->assign('RELATED_MODULE' , $relatedModuleName);
		$viewer->assign('PAGING_MODEL', $pagingModel);

		return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
	}

	public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/libraries/jquery/bootstrapswitch/css/bootstrap2/bootstrap-switch.min.css',
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}

}
