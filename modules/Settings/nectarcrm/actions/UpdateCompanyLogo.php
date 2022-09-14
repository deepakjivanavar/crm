<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_UpdateCompanyLogo_Action extends Settings_nectarcrm_Basic_Action {

	public function process(nectarcrm_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		$moduleModel = Settings_nectarcrm_CompanyDetails_Model::getInstance();

		$saveLogo = $securityError = false;
		$logoDetails = $_FILES['logo'];
		$fileType = explode('/', $logoDetails['type']);
		$fileType = $fileType[1];

		$logoContent = file_get_contents($logoDetails['tmp_name']);
		if (preg_match('(<\?php?(.*?))', $imageContent) != 0) {
			$securityError = true;
		}

		if (!$securityError) {
			if ($logoDetails['size'] && in_array($fileType, Settings_nectarcrm_CompanyDetails_Model::$logoSupportedFormats)) {
				$saveLogo = true;
			}

			if ($saveLogo) {
				$logoName = ltrim(basename(' '.nectarcrm_Util_Helper::sanitizeUploadFileName($logoDetails['name'], vglobal('upload_badext'))));
				$moduleModel->saveLogo();
				$moduleModel->set('logoname', $logoName);
				$moduleModel->save();
			}
		}

		$reloadUrl = $moduleModel->getIndexViewUrl();
		if ($securityError) {
			$reloadUrl .= '&error=LBL_IMAGE_CORRUPTED';
		} else if (!$saveLogo) {
			$reloadUrl .= '&error=LBL_INVALID_IMAGE';
		}
		header('Location: ' . $reloadUrl);
	}
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}
