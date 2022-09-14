<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Google_SaveSyncSettings_Action extends nectarcrm_BasicAjax_Action {

	public function process(nectarcrm_Request $request) {
		$contactsSettings = $request->get('Contacts');
		$calendarSettings = $request->get('Calendar');
		$sourceModule = $request->get('sourceModule');

		$contactRequest = new nectarcrm_Request($contactsSettings);
		$contactRequest->set('sourcemodule', 'Contacts');
		Google_Utils_Helper::saveSyncSettings($contactRequest);

		$calendarRequest = new nectarcrm_Request($calendarSettings);
		$calendarRequest->set('sourcemodule', 'Calendar');
		Google_Utils_Helper::saveSyncSettings($calendarRequest);
		$googleModuleModel = nectarcrm_Module_Model::getInstance('Google');

		$returnUrl = $googleModuleModel->getBaseExtensionUrl($sourceModule);

		if($request->has('parent') && $request->get('parent') === 'Settings') {
			$returnUrl = 'index.php?module=' . $sourceModule . '&parent=Settings&view=Extension&extensionModule=Google&extensionView=Index&mode=settings';
		}

		header('Location: '.$returnUrl);
	}

}

?>