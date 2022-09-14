<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_Module_Model extends nectarcrm_Module_Model{

	/**
	 * Function to check whether the module is an entity type module or not
	 * @return <Boolean> true/false
	 */
	public function isQuickCreateSupported() {
		//emails module is not enabled for quick create
		return false;
	}

	public function isWorkflowSupported() {
		return false;
	}

	/**
	 * Function to check whether the module is summary view supported
	 * @return <Boolean> - true/false
	 */
	public function isSummaryViewSupported() {
		return false;
	}
	
	/**
	 * Function to get emails related modules
	 * @return <Array> - list of modules 
	 */	
	public function getEmailRelatedModules() {
		$userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		
		$relatedModules = vtws_listtypes(array('email'), Users_Record_Model::getCurrentUserModel());
		$relatedModules = $relatedModules['types'];

		foreach ($relatedModules as $key => $moduleName) {
			if ($moduleName === 'Users') {
				unset($relatedModules[$key]);
			}
		}
		foreach ($relatedModules as $moduleName) {
			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			if(($userPrivModel->isAdminUser() || $userPrivModel->hasGlobalReadPermission() || $userPrivModel->hasModulePermission($moduleModel->getId())) && !$moduleModel->restrictToListInComposeEmailPopup()) {
				$emailRelatedModules[] = $moduleName;
			}
		}
		$emailRelatedModules[] = 'Users';
		return $emailRelatedModules;
	}

	/**
	 * Function to search emails for send email
	 * @param <String> $searchValue
	 * @return <Array> Result of searched emails
	 */
	public function searchEmails($searchValue, $moduleName = false) {
		global $current_user;
		$emailsResult = array();
		$db = PearDatabase::getInstance();

		$EmailsModuleModel = nectarcrm_Module_Model::getInstance('Emails');
		$emailSupportedModulesList = $EmailsModuleModel->getEmailRelatedModules();
		foreach ($emailSupportedModulesList as $module) {
			if ($module != 'Users' && $module != 'ModComments') {
                    $activeModules[] = "'".$module."'";
                    $activeModuleModel = nectarcrm_Module_Model::getInstance($module);
                    $moduleEmailFields = $activeModuleModel->getFieldsByType('email');
					foreach ($moduleEmailFields as $fieldName => $fieldModel) {
						if ($fieldModel->isViewable()) {
								$fieldIds[] = $fieldModel->get('id');
							}
						}
					}
				}

			if ($moduleName) {
                $activeModules = array("'".$moduleName."'");
            }
            
            $query = "SELECT nectarcrm_emailslookup.crmid, nectarcrm_emailslookup.setype, nectarcrm_emailslookup.value, 
                          nectarcrm_crmentity.label FROM nectarcrm_emailslookup INNER JOIN nectarcrm_crmentity on 
                          nectarcrm_crmentity.crmid = nectarcrm_emailslookup.crmid AND nectarcrm_crmentity.deleted=0 WHERE 
						  nectarcrm_emailslookup.fieldid in (".implode(',', $fieldIds).") and 
						  nectarcrm_emailslookup.setype in (".implode(',', $activeModules).") 
                          and (nectarcrm_emailslookup.value LIKE ? OR nectarcrm_crmentity.label LIKE ?)";

			$emailOptOutIds = $this->getEmailOptOutRecordIds();
			if (!empty($emailOptOutIds)) {
				$query .= " AND nectarcrm_emailslookup.crmid NOT IN (".implode(',', $emailOptOutIds).")";
			}

			$result = $db->pquery($query, array('%'.$searchValue.'%', '%'.$searchValue.'%'));
            $isAdmin = is_admin($current_user);
			while ($row = $db->fetchByAssoc($result)) {
				if (!$isAdmin) {
					$recordPermission = Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid']);
					if (!$recordPermission) {
						continue;
					}
				}
			$emailsResult[vtranslate($row['setype'], $row['setype'])][$row['crmid']][] = array('value' => $row['value'],
																								'label' => decode_html($row['label']).' <b>('.$row['value'].')</b>',
																								'name' => decode_html($row['label']),);
            }
            
            // For Users we should only search in users table
            $additionalModule = array('Users');
            if(!$moduleName || in_array($moduleName, $additionalModule)){
                foreach($additionalModule as $moduleName){
                    $moduleInstance = CRMEntity::getInstance($moduleName);
                    $searchFields = array();
                    $moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
                    $emailFieldModels = $moduleModel->getFieldsByType('email');

                    foreach ($emailFieldModels as $fieldName => $fieldModel) {
                        if ($fieldModel->isViewable()) {
                                $searchFields[] = $fieldName;
                        }
                    }
                    $emailFields = $searchFields;

                    $nameFields = $moduleModel->getNameFields();
                    foreach ($nameFields as $fieldName) {
                        $fieldModel = nectarcrm_Field_Model::getInstance($fieldName, $moduleModel);
                        if ($fieldModel->isViewable()) {
                                $searchFields[] = $fieldName;
                        }
                    }

				if ($emailFields) {
					$userQuery = 'SELECT '.$moduleInstance->table_index.', '.implode(',',$searchFields).' FROM nectarcrm_users WHERE deleted=0';
                        $result = $db->pquery($userQuery, array());
                        $numOfRows = $db->num_rows($result);
                        for($i=0; $i<$numOfRows; $i++) {
                            $row = $db->query_result_rowdata($result, $i);
                            foreach ($emailFields as $emailField) {
                                    $emailFieldValue = $row[$emailField];
                                    if ($emailFieldValue) {
                                            $recordLabel = getEntityFieldNameDisplay($moduleName, $nameFields, $row);
                                            if (strpos($emailFieldValue, $searchValue) !== false || strpos($recordLabel, $searchValue) !== false) {
                                                    $emailsResult[vtranslate($moduleName, $moduleName)][$row[$moduleInstance->table_index]][]
                                                                            = array('value'	=> $emailFieldValue,
																					'name'	=> $recordLabel,
																					'label'	=> $recordLabel . ' <b>('.$emailFieldValue.')</b>');

                                            }
                                    }
                            }
                        }
                    }
                }
            }
            return $emailsResult;
	}
    
    /*
     * Function to get supported utility actions for a module
     */
    function getUtilityActionsNames() {
        return array();
    }
	
	function getEmailOptOutRecordIds() {
		$emailOptOutIds = array();
		$db = PearDatabase::getInstance();
		$contactResult = $db->pquery("SELECT crmid FROM nectarcrm_crmentity INNER JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_crmentity.crmid WHERE nectarcrm_crmentity.deleted = ? AND nectarcrm_contactdetails.emailoptout = ?", array('0', '1'));
		$contactCount = $db->num_rows($contactResult);
		for($i = 0; $i < $contactCount; $i++) {
			$emailOptOutIds[] = $db->query_result($contactResult, $i, 'crmid');
		}
		$accountResult = $db->pquery("SELECT crmid FROM nectarcrm_crmentity INNER JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_crmentity.crmid WHERE nectarcrm_crmentity.deleted = ? AND nectarcrm_account.emailoptout = ?", array('0', '1'));
		$accountCount = $db->num_rows($accountResult);
		for($i = 0; $i < $accountCount; $i++) {
			$emailOptOutIds[] = $db->query_result($accountResult, $i, 'crmid');
		}
		$leadResult = $db->pquery("SELECT crmid FROM nectarcrm_crmentity INNER JOIN nectarcrm_leaddetails ON nectarcrm_leaddetails.leadid = nectarcrm_crmentity.crmid WHERE nectarcrm_crmentity.deleted = ? AND nectarcrm_leaddetails.emailoptout = ?", array('0', '1'));
		$leadCount = $db->num_rows($leadResult);
		for($i = 0; $i < $leadCount; $i++) {
			$emailOptOutIds[] = $db->query_result($leadResult, $i, 'crmid');
		}
		
		return $emailOptOutIds;
	}

	/**
	 * Function to save a given record model of the current module
	 * @param nectarcrm_Record_Model $recordModel
	 */
	public function saveRecord(nectarcrm_Record_Model $recordModel) {
		$moduleName = $this->get('name');
		$focus = $recordModel->getEntity();
		$fields = $focus->column_fields;
		foreach ($fields as $fieldName => $fieldValue) {
			$fieldValue = $recordModel->get($fieldName);
			if (is_array($fieldValue)) {
				$focus->column_fields[$fieldName] = $fieldValue;
			} else if ($fieldValue !== null) {
				$value = is_string($fieldValue) ? decode_emptyspace_html($fieldValue) : $fieldValue;
				$focus->column_fields[$fieldName] = $value;
			}
		}
		$focus->mode = $recordModel->get('mode');
		$focus->id = $recordModel->getId();
		$focus->save($moduleName);

		return $recordModel->setId($focus->id);
	}
}
?>
