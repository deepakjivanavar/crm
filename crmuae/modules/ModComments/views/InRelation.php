<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class ModComments_InRelation_View extends nectarcrm_RelatedList_View {

	function process(nectarcrm_Request $request) {
		$startindex = 0;
		$parentRecordId = $request->get('record');
		$commentRecordId = $request->get('commentid');
		$moduleName = $request->getModule();
		$rollupSettings = $request->get('rollup_settings');
		$rollupStatus = $rollupSettings['rollup_status'];
		$rollupid = $rollupSettings['rollupid'];
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$modCommentsModel = nectarcrm_Module_Model::getInstance('ModComments');

		if ($rollupStatus) {
			$parentRecordModel = nectarcrm_Record_Model::getInstanceById($parentRecordId, $moduleName);
			$parentCommentModels = $parentRecordModel->getRollupCommentsForModule($startindex);
			$startindex = $startindex + 10;
		} else {
			$parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);
		}

		if (!empty($commentRecordId)) {
			$currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
		}

		// To get field model of filename
		$fileNameFieldModel = nectarcrm_Field::getInstance("filename", $modCommentsModel);
		$fileFieldModel = nectarcrm_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
		$viewer->assign('PARENT_COMMENTS', $parentCommentModels);
		$viewer->assign('CURRENT_COMMENT', $currentCommentModel);
		$viewer->assign('FIELD_MODEL', $fileFieldModel);
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', nectarcrm_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', nectarcrm_Util_Helper::getMaxUploadSizeInBytes());

		$viewer->assign('MODULE_NAME', $request->getModule());
		$viewer->assign('MODULE_RECORD', $parentRecordId);
		$viewer->assign('ROLLUP_STATUS', $rollupStatus);
		$viewer->assign('ROLLUPID', $rollupid);
		$viewer->assign('STARTINDEX', $startindex);

		return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
	}

}
