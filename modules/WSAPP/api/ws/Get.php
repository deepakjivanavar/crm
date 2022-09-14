<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'include/Webservices/GetUpdates.php';
require_once 'modules/WSAPP/Utils.php';

function wsapp_get ($key, $module, $token, $user) {
    $name = wsapp_getApplicationName($key);
    if ($name) {
        $handlerDetails = wsapp_getHandler($name);
        require_once $handlerDetails['handlerpath'];
        $handler = new $handlerDetails['handlerclass']($key);
        return $handler->get($module,$token,$user);
    }
    return array();
}