<?php

/* +***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * *********************************************************************************** */

class Settings_Workflows_SaveAjax_Action extends Settings_nectarcrm_IndexAjax_View {

   public function process(nectarcrm_Request $request) {
      $record = $request->get('record');
      $status = $request->get('status');
      
      if($record){
         if($status == 'off')
            $status = 0;
         else if($status == 'on')
            $status = 1;
         Settings_Workflows_Record_Model::updateWorkflowStatus($record, $status);
      }
      
      $response = new nectarcrm_Response();
      $response->setResult(array('success'));
      $response->emit();
   }

   public function validateRequest(nectarcrm_Request $request) {
      $request->validateWriteAccess();
   }

}
