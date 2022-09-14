<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_SaveSettings_Action extends nectarcrm_BasicAjax_Action {

    public function process(nectarcrm_Request $request) {
        $sourceModule = $request->get('sourcemodule');
        $fieldMapping = $request->get('fieldmapping');
        Google_Utils_Helper::saveSettings($request);
        if($fieldMapping) {
            Google_Utils_Helper::saveFieldMappings($sourceModule, $fieldMapping);
        }
        $response = new nectarcrm_Response;
        $result = array('settingssaved' => true);
        $response->setResult($result);
        $response->emit();
    }
    
}

?>