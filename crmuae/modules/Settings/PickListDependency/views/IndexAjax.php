<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_PickListDependency_IndexAjax_View extends Settings_PickListDependency_Edit_View {

    public function __construct() {
        parent::__construct();
        $this->exposeMethod('getDependencyGraph');
    }
    
    public function preProcess(nectarcrm_Request $request) {
        return true;
    }
    
    public function postProcess(nectarcrm_Request $request) {
        return true;
    }
    
    public function process(nectarcrm_Request $request) {
        $mode = $request->getMode();

		if($mode){
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
    }
    
}