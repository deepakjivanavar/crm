<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_relatedtypes($elementType, $user) {
    global $adb, $log;

    $allowedTypes = vtws_listtypes(null, $user);

    $webserviceObject = nectarcrmWebserviceObject::fromName($adb, $elementType);
    $handlerPath  = $webserviceObject->getHandlerPath();
    $handlerClass = $webserviceObject->getHandlerClass();

    require_once $handlerPath;
    $handler = new $handlerClass($webserviceObject, $user, $adb, $log);
    $meta = $handler->getMeta();
    $tabid = $meta->getTabId();

    $sql = "SELECT nectarcrm_relatedlists.label, nectarcrm_tab.name, nectarcrm_tab.isentitytype FROM nectarcrm_relatedlists 
            INNER JOIN nectarcrm_tab ON nectarcrm_tab.tabid=nectarcrm_relatedlists.related_tabid 
            WHERE nectarcrm_relatedlists.tabid=? AND nectarcrm_tab.presence = 0";

    $params = array($tabid);
    $rs = $adb->pquery($sql, $params);

    $return = array('types' => array(), 'information' => array());

    while ($row = $adb->fetch_array($rs)) {
        if (in_array($row['name'], $allowedTypes['types'])) {
            $return['types'][] = $row['name'];
            // There can be same module related under different label - so label is our key.
            $return['information'][$row['label']] = array(
                'name' => $row['name'],
                'label'=> $row['label'],
                'isEntity' => $row['isentitytype']
            );
        }
    }

	return $return;
}

