<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_MoveDocuments_Action extends nectarcrm_Mass_Action {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		if(!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$documentIdsList = $this->getRecordsListFromRequest($request);
		$folderId = $request->get('folderid');

		if (!empty ($documentIdsList)) {
			foreach ($documentIdsList as $documentId) {
				$documentModel = nectarcrm_Record_Model::getInstanceById($documentId, $moduleName);
				if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $documentId)) {
					$documentModel->set('folderid', $folderId);
					$documentModel->set('mode', 'edit');
					$documentModel->save();
				} else {
					$documentsMoveDenied[] = $documentModel->getName();
				}
			}
		}
		if (empty ($documentsMoveDenied)) {
			$result = array('success'=>true, 'message'=>vtranslate('LBL_DOCUMENTS_MOVED_SUCCESSFULLY', $moduleName));
		} else {
			$result = array('success'=>false, 'message'=>vtranslate('LBL_DENIED_DOCUMENTS', $moduleName), 'LBL_RECORDS_LIST'=>$documentsMoveDenied);
		}

		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}
}