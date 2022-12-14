<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MailConverter_BodyAjax_View extends Settings_nectarcrm_IndexAjax_View {
	
	public function process(nectarcrm_Request $request) {
        $action = $request->get('action1');
        $delimiter = $request->get('delimiter');
        $bodyText = $request->get('body');
        $qualifiedModuleName = $request->getModule(false);
        $record = $request->get('record');
        $scannerId = $request->get('scannerId');
        
        $moduleFields = Settings_MailConverter_BodyRule_Model::getModuleFields($action);
        $bodyFields = Settings_MailConverter_BodyRule_Model::parseBody($bodyText, $delimiter);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE_FIELDS', $moduleFields);
        $viewer->assign('BODY_FIELDS', $bodyFields);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        
        if(!empty($record)) {
            $bodyRule = new Settings_MailConverter_BodyRule_Model();
            $bodyRule->set('scannerid', $scannerId);
            $bodyRule->set('ruleid', $record);
            $mappingData = $bodyRule->getMapping();
            $viewer->assign('MAPPING', $mappingData);
        }
        
        $viewer->view('MapFields.tpl', $qualifiedModuleName);
    }
}