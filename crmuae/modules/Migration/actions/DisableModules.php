<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Migration_DisableModules_Action extends nectarcrm_Action_Controller {

	public function process(nectarcrm_Request $request) {
		$modulesList = $request->get('modulesList');
		if ($modulesList) {
			$moduleManagerModel = new Settings_ModuleManager_Module_Model();
			foreach ($modulesList as $moduleName) {
				$moduleManagerModel->disableModule($moduleName);
			}
		}

		header('Location: migrate/index.php');
	}

}
