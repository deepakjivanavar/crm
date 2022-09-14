<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

require_once ('include/events/VTEntityData.inc');
require_once ('include/QueryGenerator/QueryGenerator.php');
require_once ('include/Zend/Json.php');
require_once ('include/utils/utils.php');
require_once ('modules/com_nectarcrm_workflow/VTWorkflowUtils.php');
require_once ('modules/com_nectarcrm_workflow/VTWorkflowManager.inc');
require_once ('modules/com_nectarcrm_workflow/VTTaskManager.inc');
require_once ('modules/com_nectarcrm_workflow/VTTaskQueue.inc');
require_once ('modules/com_nectarcrm_workflow/VTEntityCache.inc');
require_once ('include/Webservices/WebserviceField.php');
require_once ('include/Webservices/EntityMeta.php');
require_once ('include/Webservices/nectarcrmWebserviceObject.php');
require_once ('include/Webservices/nectarcrmCRMObject.php');
require_once ('include/Webservices/nectarcrmCRMObjectMeta.php');
require_once ('include/Webservices/DataTransform.php');
require_once ('include/Webservices/Retrieve.php');