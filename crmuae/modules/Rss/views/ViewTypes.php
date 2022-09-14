<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Rss_ViewTypes_View extends nectarcrm_IndexAjax_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('getRssWidget');
        $this->exposeMethod('getRssAddForm');
    }
        
	/**
     * Function to display rss sidebar widget
     * @param <nectarcrm_Request> $request 
     */
    public function getRssWidget(nectarcrm_Request $request) {
        $module = $request->get('module');
        $moduleModel = nectarcrm_Module_Model::getInstance($module);
        $rssSources = $moduleModel->getRssSources();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $module);
        $viewer->assign('RSS_SOURCES', $rssSources);
        echo $viewer->view('RssWidgetContents.tpl', $module, true);
    }
    
    /**
     * Function to get the rss add form 
     * @param <nectarcrm_Request> $request
     */
    public function getRssAddForm(nectarcrm_Request $request) { 
        $module = $request->getModule();
		$moduleModel = nectarcrm_Module_Model::getInstance($module);
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE',$module);
        $viewer->view('RssAddForm.tpl', $module);
    }
   
}
