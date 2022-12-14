<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class CustomView_SaveAjax_Action extends CustomView_Save_Action {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('updateColumns');
	}

	public function process(nectarcrm_Request $request) {
		$response = new nectarcrm_Response();
		$cvId = $request->get('record');
		if (!$cvId) {
			$response->setError('Filter Not specified');
			$response->emit();
			return;
		}

		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

		$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
		$customViewModel->set('setdefault',$request->get('setdefault'));
		$customViewModel->save(true);
		$response->setResult(array('id'=>$cvId,'isdefault'=>$customViewModel->get('setdefault')));
		$response->emit();
	}


	/**
	 * Function to updated selected Custom view columns
	 * @param nectarcrm_Request $request
	 */
	 public function updateColumns(nectarcrm_Request $request) {
		$cvid = $request->get('record');
		$customViewModel = CustomView_Record_Model::getInstanceById($cvid);
		$response = new nectarcrm_Response();
		if ($customViewModel) {
			$selectedColumns = $request->get('columnslist');
			$customViewModel->deleteSelectedFields();
			$customViewModel->saveSelectedFields($selectedColumns);
			/**
			 * We are setting list_headers in session when we manage columns.
			 * we should clear this from session in order to apply view
			 */
			$listViewSessionKey = $customViewModel->getModule()->getName().'_'.$cvid;
			nectarcrm_ListView_Model::deleteParamsSession($listViewSessionKey,'list_headers');
			$response->setResult(array('message'=>vtranslate('List columns saved successfully',$request->getModule()), 'listviewurl'=>$customViewModel->getModule()->getListViewUrl().'&viewname='.$cvid));
		} else {
			$response->setError(vtranslate('Filter does not exist',$request->getModule()));
		}
		$response->emit();
	}
}