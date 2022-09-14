<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

abstract class nectarcrm_Mass_Action extends nectarcrm_Action_Controller {

	protected function getRecordsListFromRequest(nectarcrm_Request $request) {
		$cvId = $request->get('viewname');
		$module = $request->get('module');
		if(!empty($cvId) && $cvId=="undefined"){
			$sourceModule = $request->get('sourceModule');
			$cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
		}
		$selectedIds = $request->get('selected_ids');
		$excludedIds = $request->get('excluded_ids');

		if(!empty($selectedIds) && $selectedIds != 'all') {
			if(!empty($selectedIds) && count($selectedIds) > 0) {
				return $selectedIds;
			}
		}

		$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
		if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }

            /**
			 *  Mass action on Documents if we select particular folder is applying on all records irrespective of
			 *  seleted folder
			 */
			if ($module == 'Documents') {
				$customViewModel->set('folder_id', $request->get('folder_id'));
				$customViewModel->set('folder_value', $request->get('folder_value'));
			}

			$customViewModel->set('search_params',$request->get('search_params'));
			return $customViewModel->getRecordIds($excludedIds,$module);
		}
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}
