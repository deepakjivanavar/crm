<?php

require_once 'vtlib/nectarcrm/Module.php';
require_once 'vtlib/nectarcrm/Package.php';

$Vtiger_Utils_Log = true;

$package = new Vtiger_Package();
$package->import('/path/to/HelloWorld-v1.zip');