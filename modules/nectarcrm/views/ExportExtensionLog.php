<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/
require_once 'modules/WSAPP/WSAPPLogs.php';

class nectarcrm_ExportExtensionLog_View extends nectarcrm_View_Controller {

	function preProcess(nectarcrm_Request $request) {
		return false;
	}

	function postProcess(nectarcrm_Request $request) {
		return false;
	}

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if (!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * Function to convert log details to user format
	 * @param <array> $logDetails
	 * @return <array> $data
	 */
	function convertLogDetailsToUserFormat($logDetails, $moduleName) {
		$db = PearDatabase::getInstance();
		$data = array();
		$data[0]['module'] = vtranslate('LBL_SOURCE_MODULE');
		$data[0]['name'] = vtranslate('LBL_RECORD_NAME');
		$i = 1;
		foreach ($logDetails as $logId) {
			if (!is_numeric($logId)) {
				list ($moduleId, $recordId) = explode('x', $logId);
				if ($logId && $moduleId) {
					$wsObject = nectarcrmWebserviceObject::fromId($db, $moduleId);
					$moduleName = $wsObject->getEntityName();
				}
			} else {
				$recordId = $logId;
				if ($logId) {
					$moduleName = getSalesEntityType($recordId);
				}
			}
			$name = getEntityName($moduleName, $recordId);
			if (!empty($name)) {
				$data[$i]['module'] = $moduleName;
				$data[$i]['name'] = $name[$recordId];
				$i++;
			}
		}

		return $data;
	}

	function process(nectarcrm_request $request) {
		$logId = $request->get('logid');
		$type = $request->get('type');
		$this->getCSV($logId, $type);
	}

	/**
	 * Function exports log data into a csv file
	 */
	function getCSV($logId, $type) {
		$logData = WSAPP_Logs::getSyncCountDetails($logId);
		$sourceModule = WSAPP_Logs::getModuleFromLogId($logId);
		if ($type == 'app_skip' || $type == 'vt_skip') {
			$data = json_decode(decode_html($logData[$type.'_info'], true));
			$i = 1;
			$tmpData = array();
			$tmpData[0]['module'] = vtranslate('LBL_SOURCE_MODULE');
			$tmpData[0]['name'] = vtranslate('LBL_RECORD_NAME');
			$tmpData[0]['error'] = vtranslate('LBL_REASON');
			foreach ($data as $skipInfo) {
				$skipError = (array) $skipInfo;
				foreach ($skipError as $name => $errorMsg) {
					$tmpData[$i]['module'] = $sourceModule;
					$tmpData[$i]['name'] = $name;
					$tmpData[$i]['error'] = $errorMsg;
					$i++;
				}
			}
			$data = $tmpData;
		} else {
			$data = json_decode(decode_html($logData[$type.'_ids'], true));
			$data = $this->convertLogDetailsToUserFormat($data, $sourceModule);
		}

		$rootDirectory = vglobal('root_directory');
		$tmpDir = vglobal('tmp_dir');

		$tempFileName = tempnam($rootDirectory.$tmpDir, 'csv');
		$this->writeToCSVFile($tempFileName, $data);
		$fileName = 'ExtensionLog.csv';

		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			header('Pragma: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		}

		// we are adding UTF-8 Byte Order Mark - BOM at the bottom so the size should be + 8 of the file size
		$fileSize = @filesize($tempFileName) + 8;
		header('Content-Encoding: UTF-8');
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Length: '.$fileSize);
		header('Content-disposition: attachment; filename="'.$fileName.'"');

		$fp = fopen($tempFileName, 'rb');
		fpassthru($fp);
	}

	function writeToCSVFile($fileName, $data) {
		$arr_val = $data;

		$fp = fopen($fileName, 'w+');

		if (isset($arr_val)) {
			foreach ($arr_val as $key => $array_value) {
				$csv_values = array_map('decode_html', array_values($array_value));
				fputcsv($fp, $csv_values);
			}
		}

		fclose($fp);
	}

}
