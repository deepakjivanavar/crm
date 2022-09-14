<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Currency_TransformEditAjax_View extends Settings_nectarcrm_IndexAjax_View {
    
    public function process(nectarcrm_Request $request) {
        $record = $request->get('record');
        
        $currencyList = Settings_Currency_Record_Model::getAll($record);
        
        $qualifiedName = $request->getModule(false);
        $viewer = $this->getViewer($request);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('CURRENCY_LIST',$currencyList);
        $viewer->assign('RECORD_MODEL',  Settings_Currency_Record_Model::getInstance($record));
        echo $viewer->view('TransformEdit.tpl', $qualifiedName, true);
    }
}