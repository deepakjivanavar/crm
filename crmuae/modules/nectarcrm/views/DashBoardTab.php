<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_DashboardTab_View extends nectarcrm_Index_View {
    
    function __construct() {
		parent::__construct();
        $this->exposeMethod('showDashBoardAddTabForm');
        $this->exposeMethod('getTabContents');
        $this->exposeMethod('showDashBoardTabList');
	}
    
    function process(nectarcrm_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    
    function showDashBoardAddTabForm($request){
        $moduleName = $request->getModule();

        $viewer = $this->getViewer($request);
        $viewer->assign("MODULE",$moduleName);
        echo $viewer->view('AddDashBoardTabForm.tpl', $moduleName, true);
    }
    
    function getTabContents($request){
        $moduleName = $request->getModule();
        $tabId = $request->get("tabid");
        
        $dashBoardModel = nectarcrm_DashBoard_Model::getInstance($moduleName);
        $dashBoardModel->set("tabid",$tabId);
        
        $widgets = $dashBoardModel->getDashboards($moduleName);
        $selectableWidgets = $dashBoardModel->getSelectableDashboard();
        $dashBoardTabInfo = $dashBoardModel->getTabInfo($tabId);
         
        $viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('WIDGETS', $widgets);        
		$viewer->assign('SELECTABLE_WIDGETS', $selectableWidgets);
		$viewer->assign('TABID',$tabId);

		$viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
		echo $viewer->view('dashboards/DashBoardTabContents.tpl', $moduleName,true);
    }
    
    public function showDashBoardTabList(nectarcrm_Request $request) {
        
        $viewer = $this->getViwer($request);
        $moduleName = $this->getModule();
        
        $dashBoardModel = new nectarcrm_DashBoard_Model();
        $dashBoardTabs = $dashBoardModel->getActiveTabs();
        
        $viewer->assign('DASHBOARD_TABS',$dashBoardTabs);
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('DashBoardTabList.tpl',$moduleName);
    }
}