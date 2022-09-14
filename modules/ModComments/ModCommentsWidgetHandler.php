<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;

$widgetName = vtlib_purify($_REQUEST['widget']);
$criteria = vtlib_purify($_REQUEST['criteria']);

$widgetController = CRMEntity::getInstance($currentModule);
$widgetInstance = $widgetController->getWidget($widgetName);
$widgetInstance->setCriteria($criteria);

echo $widgetInstance->process( array('ID' => vtlib_purify($_REQUEST['parentid'])) );
