<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_RelationListView_Model extends nectarcrm_RelationListView_Model {

	public function getCreateViewUrl() {
		$createViewUrl = parent::getCreateViewUrl();
		$relationModel = $this->getRelationModel();
		$relatedModuleModel = $relationModel->getRelationModuleModel();
		$relatedModuleName = $relatedModuleModel->getName();

		if (in_array($relatedModuleName, array('Quotes', 'SalesOrder'))) {
			$parentRecordModel = $this->getParentRecordModel();
			$createViewUrl .= '&account_id='.$parentRecordModel->get('related_to').'&contact_id='.$parentRecordModel->get('contact_id');
		}
		return $createViewUrl;
	}

}
?>
