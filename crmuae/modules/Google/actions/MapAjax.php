<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_MapAjax_Action extends nectarcrm_BasicAjax_Action {

    public function process(nectarcrm_Request $request) {
        switch ($request->get("mode")) {
            case 'getLocation'	:	$result = $this->getLocation($request);
									break;
        }
        echo json_encode($result);
    }

    /**
     * get address for the record, based on the module type.
     * @param nectarcrm_Request $request
     * @return type 
     */
    function getLocation(nectarcrm_Request $request) {
        $result = Google_Map_Helper::getLocation($request);
        return $result;
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateReadAccess();
    }
}

?>
