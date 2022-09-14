<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class nectarcrm_ProcessDuplicates_Action extends nectarcrm_Action_Controller {

	function checkPermission(nectarcrm_Request $request) {
		$module = $request->getModule();
		$records = $request->get('records');
		if($records) {
			foreach($records as $record) {
				$recordPermission = Users_Privileges_Model::isPermitted($module, 'EditView', $record);
				if(!$recordPermission) {
					throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
				}
			}
		}
	}

	function process (nectarcrm_Request $request) {
		global $skipDuplicateCheck;
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
		$records = $request->get('records');
		$primaryRecord = $request->get('primaryRecord');
		$primaryRecordModel = nectarcrm_Record_Model::getInstanceById($primaryRecord, $moduleName);

		$response = new nectarcrm_Response();
		try {
			$skipDuplicateCheckOldValue = $skipDuplicateCheck;
			$skipDuplicateCheck = true;

			$fields = $moduleModel->getFields();
			foreach($fields as $field) {
				$fieldValue = $request->get($field->getName());
				if($field->isEditable()) {
					if($field->uitype == 71) {
						$fieldValue = CurrencyField::convertToUserFormat($fieldValue);
					}
					$primaryRecordModel->set($field->getName(), $fieldValue);
				}
			}
			$primaryRecordModel->set('mode', 'edit');
			$primaryRecordModel->save();

			$deleteRecords = array_diff($records, array($primaryRecord));
			foreach($deleteRecords as $deleteRecord) {
				$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'Delete', $deleteRecord);
				if($recordPermission) {
					$primaryRecordModel->transferRelationInfoOfRecords(array($deleteRecord));
					$record = nectarcrm_Record_Model::getInstanceById($deleteRecord);
					$record->delete();
				}
			}
			$skipDuplicateCheck = $skipDuplicateCheckOldValue;

			$response->setResult(true);
		} catch (DuplicateException $e) {
			$response->setError($e->getMessage(), $e->getDuplicationMessage(), $e->getMessage());
		} catch (Exception $e) {
			$response->setError($e->getMessage());
		}
		$response->emit();
	}

	public function validateRequest(nectarcrm_Request $request) {
		$request->validateWriteAccess();
	}
}