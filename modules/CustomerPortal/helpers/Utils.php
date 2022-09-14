<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

class CustomerPortal_Utils {

	static function getImageDetails($recordId, $module) {
		global $adb;
		$sql = "SELECT nectarcrm_attachments.*, nectarcrm_crmentity.setype FROM nectarcrm_attachments
					INNER JOIN nectarcrm_seattachmentsrel ON nectarcrm_seattachmentsrel.attachmentsid = nectarcrm_attachments.attachmentsid
					INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_attachments.attachmentsid
						WHERE nectarcrm_crmentity.setype = ? and nectarcrm_seattachmentsrel.crmid = ?";

		$result = $adb->pquery($sql, array($module.' Image', $recordId));

		$imageId = $adb->query_result($result, 0, 'attachmentsid');
		$imagePath = $adb->query_result($result, 0, 'path');
		$imageName = $adb->query_result($result, 0, 'name');
		$imageType = $adb->query_result($result, 0, 'type');
		$imageOriginalName = urlencode(decode_html($imageName));

		if (!empty($imageName)) {
			$imageDetails[] = array(
				'id' => $imageId,
				'orgname' => $imageOriginalName,
				'path' => $imagePath.$imageId,
				'name' => $imageName,
				'type' => $imageType
			);
		}

		if (!isset($imageDetails))
			return;
		else
			return self::getEncodedImage($imageDetails[0]);
	}

	static function getEncodedImage($imageDetails) {
		global $root_directory;
		$image = $root_directory.'/'.$imageDetails['path'].'_'.$imageDetails['name'];
		$image_data = file_get_contents($image);
		$encoded_image = base64_encode($image_data);
		$encodedImageData = array();
		$encodedImageData['imagetype'] = $imageDetails['type'];
		$encodedImageData['imagedata'] = $encoded_image;
		return $encodedImageData;
	}

	public static function getActiveModules() {
		$activeModules = nectarcrm_Cache::get('CustomerPortal', 'activeModules'); // need to flush cache when modules updated at CRM settings

		if (empty($activeModules)) {
			global $adb;
			$sql = "SELECT nectarcrm_tab.name FROM nectarcrm_customerportal_tabs INNER JOIN nectarcrm_tab 
						ON nectarcrm_customerportal_tabs.tabid= nectarcrm_tab.tabid AND nectarcrm_tab.presence= ? WHERE nectarcrm_customerportal_tabs.visible = ? ";
			$sqlResult = $adb->pquery($sql, array(0, 1));

			for ($i = 0; $i < $adb->num_rows($sqlResult); $i++) {
				$activeModules[] = $adb->query_result($sqlResult, $i, 'name');
			}
			//Checking if module is active at Module Manager 
			foreach ($activeModules as $index => $moduleName) {
				$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
				if (!$moduleModel || !$moduleModel->isActive() || nectarcrm_Runtime::isRestricted('modules', $moduleName)) {
					unset($activeModules[$index]);
				}
			}
			nectarcrm_Cache::set('CustomerPortal', 'activeModules', $activeModules);
		}
		return $activeModules;
	}

	public static function isModuleActive($module) {
		$activeModules = self::getActiveModules();
		$defaultAllowedModules = array("ModComments", "History", "Contacts", "Accounts");

		if (in_array($module, $defaultAllowedModules)) {
			return true;
		} else if (in_array($module, $activeModules) && !nectarcrm_Runtime::isRestricted('modules', $module)) {
			return true;
		}
		return false;
	}

	static function resolveRecordValues(&$record, $user = null, $ignoreUnsetFields = false) {
		$userTypeFields = array('assigned_user_id', 'creator', 'userid', 'created_user_id', 'modifiedby', 'folderid');

		if (empty($record))
			return $record;

		$module = nectarcrm_Util_Helper::detectModulenameFromRecordId($record['id']);
		$fieldnamesToResolve = nectarcrm_Util_Helper::detectFieldnamesToResolve($module);
		$activeFields = self::getActiveFields($module);

		if (is_array($activeFields) && $module !== 'ModComments') {
			foreach ($fieldnamesToResolve as $key => $field) {
				if (!in_array($field, $activeFields)) {
					unset($fieldnamesToResolve[$key]);
				}
			}
		}

		if (!empty($fieldnamesToResolve)) {
			foreach ($fieldnamesToResolve as $resolveFieldname) {

				if (isset($record[$resolveFieldname]) && !empty($record[$resolveFieldname])) {
					$fieldvalueid = $record[$resolveFieldname];

					if (in_array($resolveFieldname, $userTypeFields)) {
						$fieldvalue = decode_html(trim(vtws_getName($fieldvalueid, $user)));
					} else {
						$fieldvalue = nectarcrm_Util_Helper::fetchRecordLabelForId($fieldvalueid);
					}
					$record[$resolveFieldname] = array('value' => $fieldvalueid, 'label' => $fieldvalue);
				}
			}
		}
		return $record;
	}

	static function getRelatedModuleLabel($relatedModule, $parentModule = "Contacts") {
		$relatedModuleLabel = nectarcrm_Cache::get('CustomerPortal', $relatedModule.':label');

		if (empty($relatedModuleLabel)) {
			global $adb;

			if (in_array($relatedModule, array('ProjectTask', 'ProjectMilestone')))
				$parentModule = 'Project';

			$sql = "SELECT nectarcrm_relatedlists.label FROM nectarcrm_relatedlists
						INNER JOIN nectarcrm_customerportal_tabs ON nectarcrm_relatedlists.related_tabid =nectarcrm_customerportal_tabs.tabid
						INNER JOIN nectarcrm_tab ON nectarcrm_customerportal_tabs.tabid =nectarcrm_tab.tabid WHERE nectarcrm_tab.name=? AND nectarcrm_relatedlists.tabid=?";
			$sqlResult = $adb->pquery($sql, array($relatedModule, getTabid($parentModule)));

			if ($adb->num_rows($sqlResult) > 0) {
				$relatedModuleLabel = $adb->query_result($sqlResult, 0, 'label');
				nectarcrm_Cache::set('CustomerPortal', $relatedModule.':label', $relatedModuleLabel);
			}
		}
		return $relatedModuleLabel;
	}

	static function getActiveFields($module, $withPermissions = false) {
		$activeFields = nectarcrm_Cache::get('CustomerPortal', 'activeFields'); // need to flush cache when fields updated at CRM settings

		if (empty($activeFields)) {
			global $adb;
			$sql = "SELECT name, fieldinfo FROM nectarcrm_customerportal_fields INNER JOIN nectarcrm_tab ON nectarcrm_customerportal_fields.tabid=nectarcrm_tab.tabid";
			$sqlResult = $adb->pquery($sql, array());
			$num_rows = $adb->num_rows($sqlResult);

			for ($i = 0; $i < $num_rows; $i++) {
				$retrievedModule = $adb->query_result($sqlResult, $i, 'name');
				$fieldInfo = $adb->query_result($sqlResult, $i, 'fieldinfo');
				$activeFields[$retrievedModule] = $fieldInfo;
			}
			nectarcrm_Cache::set('CustomerPortal', 'activeFields', $activeFields);
		}

		$fieldsJSON = $activeFields[$module];
		$data = Zend_Json::decode(decode_html($fieldsJSON));
		$fields = array();

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				if (self::isViewable($key, $module)) {
					if ($withPermissions) {
						$fields[$key] = $value;
					} else {
						$fields[] = $key;
					}
				}
			}
		}
		return $fields;
	}

	static function str_replace_last($search, $replace, $str) {
		if (( $pos = strrpos($str, $search) ) !== false) {
			$search_length = strlen($search);
			$str = substr_replace($str, $replace, $pos, $search_length);
		}
		return $str;
	}

	static function isViewable($fieldName, $module) {
		global $db;
		$db = PearDatabase::getInstance();
		$tabidSql = "SELECT tabid from nectarcrm_tab WHERE name = ?";
		$tabidResult = $db->pquery($tabidSql, array($module));
		if ($db->num_rows($tabidResult)) {
			$tabId = $db->query_result($tabidResult, 0, 'tabid');
		}
		$presenceSql = "SELECT presence,displaytype FROM nectarcrm_field WHERE fieldname=? AND tabid = ?";
		$presenceResult = $db->pquery($presenceSql, array($fieldName, $tabId));
		$num_rows = $db->num_rows($presenceResult);
		if ($num_rows) {
			$fieldPresence = $db->query_result($presenceResult, 0, 'presence');
			$displayType = $db->query_result($presenceResult, 0, 'displaytype');
			if ($fieldPresence == 0 || $fieldPresence == 2 && $displayType !== 4) {
				return true;
			} else {
				return false;
			}
		}
	}

	static function isReferenceType($fieldName, $describe) {
		$type = self::getFieldType($fieldName, $describe);

		if ($type === 'reference') {
			return true;
		}
		return false;
	}

	static function isOwnerType($fieldName, $describe) {
		$type = self::getFieldType($fieldName, $describe);

		if ($type === 'owner') {
			return true;
		}
		return false;
	}

	static function getFieldType($fieldName, $describe) {
		$fields = $describe['fields'];

		foreach ($fields as $field) {
			if ($fieldName === $field['name']) {
				return $field['type']['name'];
			}
		}
		return null;
	}

	static function getMandatoryFields($describe) {

		$fields = $describe["fields"];
		$mandatoryFields = array();
		foreach ($fields as $field) {
			if ($field['mandatory'] == 1) {
				$mandatoryFields[$field['name']] = $field['type'];
			}
		}
		return $mandatoryFields;
	}

	static function isModuleRecordCreatable($module) {
		global $db;
		$db = PearDatabase::getInstance();
		$recordPermissionQuery = "SELECT createrecord from nectarcrm_customerportal_tabs WHERE tabid=?";
		$createPermissionResult = $db->pquery($recordPermissionQuery, array(getTabid($module)));
		$createPermission = $db->query_result($createPermissionResult, 0, 'createrecord');
		return $createPermission;
	}

	static function isModuleRecordEditable($module) {
		global $db;
		$db = PearDatabase::getInstance();
		$recordPermissionQuery = "SELECT editrecord from nectarcrm_customerportal_tabs WHERE tabid=?";
		$editPermissionResult = $db->pquery($recordPermissionQuery, array(getTabid($module)));
		$editPermission = $db->query_result($editPermissionResult, 0, 'editrecord');
		return $editPermission;
	}

	//Function to get all Ids when mode is set to published.

	static function getAllRecordIds($module, $current_user) {
		$relatedIds = array();
		$sql = sprintf('SELECT id FROM %s;', $module);
		$result = vtws_query($sql, $current_user);
		foreach ($result as $resultArray) {
			$relatedIds[] = $resultArray['id'];
		}
		return $relatedIds;
	}

}
