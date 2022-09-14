<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

/**
 * Function to relate CRM records for relationships exists in nectarcrm_relatedlists table.
 * @param $sourceRecordId - Source record webservice id.
 * @param $relatedRecordId - Related record webservice id(s). One record id or array of ids for same module.
 * @param $relationIdLabel - Relation id or label as in nectarcrm_relatedlists table.
 * @param $user
 */
function vtws_add_related($sourceRecordId, $relatedRecordId, $relationIdLabel = false, $user = false) {
	$db = PearDatabase::getInstance();
	if (!is_array($relatedRecordId)) {
		$relatedRecordId = array($relatedRecordId);
	}

	$sourceRecordIdParts = vtws_getIdComponents($sourceRecordId);
	$relatedRecordIdParts = vtws_getIdComponents($relatedRecordId[0]);
	if (!isRecordExists($sourceRecordIdParts[1])) {
		throw new Exception("Source record $sourceRecordIdParts is deleted");
	}

	try {
		$sourceRecordWsObject = nectarcrmWebserviceObject::fromId($db, $sourceRecordIdParts[0]);
		$relatedRecordWsObject = nectarcrmWebserviceObject::fromId($db, $relatedRecordIdParts[0]);

		$sourceModuleModel = nectarcrm_Module_Model::getInstance($sourceRecordWsObject->getEntityName());
		$relatedModuleModel = nectarcrm_Module_Model::getInstance($relatedRecordWsObject->getEntityName());

		$relationLabel = false;
		$relationId = false;
		if (is_numeric($relationIdLabel)) {
			$relationId = $relationIdLabel;
		} else if (!empty($relationIdLabel)) {
			$relationLabel = $relationIdLabel;
		}

		if ($sourceModuleModel && $relatedModuleModel) {
			$relationModel = nectarcrm_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel, $relationLabel, $relationId);
			if ($relationModel) {
				foreach ($relatedRecordId as $id) {
					$idParts = vtws_getIdComponents($id);
					if ($idParts[0] == $relatedRecordIdParts[0]) {
						$relationModel->addRelation($sourceRecordIdParts[1], $idParts[1]);
					}
				}
			}
		}
		return array('message' => 'successfull');
	} catch (Exception $ex) {
		throw new Exception($ex->getMessage());
	}
}
