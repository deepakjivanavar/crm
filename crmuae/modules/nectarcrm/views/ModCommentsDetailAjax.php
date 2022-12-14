<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.2
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_ModCommentsDetailAjax_View extends nectarcrm_IndexAjax_View {

	function __construct() {
		$this->exposeMethod('saveRollupSettings');
		$this->exposeMethod('getNextGroupOfRollupComments');
	}

	public function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

		$moduleName = $request->getModule();
		$viewer = $this->getRollupComments($request);
		echo $viewer->view('ShowAllComments.tpl', $moduleName, true);
	}

	function getRollupComments($request) {
		$startindex = $request->get('startindex');

		$parentRecordId = $request->get('parentId');
		$parenModule = $request->get('parent');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$request->set('rollup_status', 1);
		$rollupsettings = ModComments_Module_Model::storeRollupSettingsForUser($currentUserModel, $request);
		$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentRecordId, $parenModule);
		$commentsRecordModel = $parentRecordModel->getRollupCommentsForModule($startindex);
		$modCommentsModel = nectarcrm_Module_Model::getInstance('ModComments');

		$fileNameFieldModel = nectarcrm_Field::getInstance("filename", $modCommentsModel);
		$fileFieldModel = nectarcrm_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);

		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
		$viewer->assign('PARENT_COMMENTS', $commentsRecordModel);
		$viewer->assign('MODULE_NAME', $parenModule);
		$viewer->assign('FIELD_MODEL', $fileFieldModel);
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', nectarcrm_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', nectarcrm_Util_Helper::getMaxUploadSizeInBytes());
		$viewer->assign('MODULE_RECORD', $parentRecordId);
		$viewer->assign('ROLLUP_STATUS', $request->get('rollup_status'));
		$viewer->assign('ROLLUPID', $rollupsettings['rollupid']);
		$viewer->assign('STARTINDEX', $startindex + 10);

		return $viewer;
	}

	function saveRollupSettings(nectarcrm_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		ModComments_Module_Model::storeRollupSettingsForUser($currentUserModel, $request);
	}

	function getNextGroupOfRollupComments(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$viewer = $this->getRollupComments($request);
		if (count($viewer->tpl_vars['PARENT_COMMENTS']->value))
			echo $viewer->view('CommentsList.tpl', $moduleName, true);
	}

}
