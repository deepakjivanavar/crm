<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Users_ListAjax_View extends Users_List_View{
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getListViewCount');
        $this->exposeMethod('getRecordsCount');
        $this->exposeMethod('getPageCount');
    }

    function preProcess(nectarcrm_Request $request) {
        return true;
    }

    function postProcess(nectarcrm_Request $request) {
        return true;
    }

    function process(nectarcrm_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

}