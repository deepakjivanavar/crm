<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_GetData_Action extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
		$record = $request->get('record');
		$sourceModule = $request->get('source_module');
		$response = new nectarcrm_Response();

		$permitted = Users_Privileges_Model::isPermitted($sourceModule, 'DetailView', $record);
		if($permitted) {
			$recordModel = nectarcrm_Record_Model::getInstanceById($record, $sourceModule);
			$data = $recordModel->getData();
			$response->setResult(array('success'=>true, 'data'=>array_map('decode_html',$data)));
		} else {
			$response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
		}
		$response->emit();
	}
}
