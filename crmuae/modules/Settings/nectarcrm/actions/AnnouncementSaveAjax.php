<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_nectarcrm_AnnouncementSaveAjax_Action extends Settings_nectarcrm_Basic_Action {
    
    public function process(nectarcrm_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $annoucementModel = Settings_nectarcrm_Announcement_Model::getInstanceByCreator($currentUser);
        $annoucementModel->set('announcement',$request->get('announcement'));
        $annoucementModel->save();
        $responce = new nectarcrm_Response();
        $responce->setResult(array('success'=>true));
        $responce->emit();
    }
    
    public function validateRequest(nectarcrm_Request $request) {
        $request->validateWriteAccess();
    }
}