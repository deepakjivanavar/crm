<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Dashboard_View extends nectarcrm_Index_View {

	protected static $selectable_dashboards;

	function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		if(!Users_Privileges_Model::isPermitted($moduleName, $actionName)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	function preProcess(nectarcrm_Request $request, $display=true) {
		parent::preProcess($request, false);
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$dashBoardModel = nectarcrm_DashBoard_Model::getInstance($moduleName);
		//check profile permissions for Dashboards
		$moduleModel = nectarcrm_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			// TODO : Need to optimize the widget which are retrieving twice
			$dashboardTabs = $dashBoardModel->getActiveTabs();
			if ($request->get("tabid")) {
				$tabid = $request->get("tabid");
			} else {
				// If no tab, then select first tab of the user
				$tabid = $dashboardTabs[0]["id"];
			}
			$dashBoardModel->set("tabid", $tabid);
			$widgets = $dashBoardModel->getSelectableDashboard();
			self::$selectable_dashboards = $widgets;
		} else {
			$widgets = array();
		}
		$viewer->assign('MODULE_PERMISSION', $permission);
		$viewer->assign('WIDGETS', $widgets);
		$viewer->assign('MODULE_NAME', $moduleName);
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(nectarcrm_Request $request) {
		return 'dashboards/DashBoardPreProcess.tpl';
	}

	function process(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$dashBoardModel = nectarcrm_DashBoard_Model::getInstance($moduleName);

		//check profile permissions for Dashboards
		$moduleModel = nectarcrm_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			// TODO : Need to optimize the widget which are retrieving twice
		   $dashboardTabs = $dashBoardModel->getActiveTabs();
		   if($request->get("tabid")){
			   $tabid = $request->get("tabid");
		   } else {
			   // If no tab, then select first tab of the user
			   $tabid = $dashboardTabs[0]["id"];
		   }
		   $dashBoardModel->set("tabid",$tabid);
			$widgets = $dashBoardModel->getDashboards($moduleName);
		} else {
			return;
		}

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('WIDGETS', $widgets);
		$viewer->assign('DASHBOARD_TABS', $dashboardTabs);
		$viewer->assign('DASHBOARD_TABS_LIMIT', $dashBoardModel->dashboardTabLimit);
		$viewer->assign('SELECTED_TAB',$tabid);
        if (self::$selectable_dashboards) {
			$viewer->assign('SELECTABLE_WIDGETS', self::$selectable_dashboards);
		}
		$viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('TABID',$tabid);
		$viewer->view('dashboards/DashBoardContents.tpl', $moduleName);
	}

	public function postProcess(nectarcrm_Request $request) {
		parent::postProcess($request);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	public function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'~/libraries/jquery/gridster/jquery.gridster.min.js',
			'~/libraries/jquery/jqplot/jquery.jqplot.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.pieRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.barRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.pointLabels.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.funnelRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.barRenderer.min.js',
			'~/libraries/jquery/jqplot/plugins/jqplot.logAxisRenderer.min.js',
			'~/libraries/jquery/VtJqplotInterface.js',
			'~/libraries/jquery/vtchart.js',
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/gridster/jquery.gridster.min.js',
			'~/libraries/jquery/vtchart.js',
			'modules.nectarcrm.resources.DashBoard',
			'modules.'.$moduleName.'.resources.DashBoard',
			'modules.nectarcrm.resources.dashboards.Widget',
			'~/layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/modules/nectarcrm/resources/Detail.js',
			'~/layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/modules/Reports/resources/Detail.js',
			'~/layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/modules/Reports/resources/ChartDetail.js',
			"modules.Emails.resources.MassEdit",
			"modules.nectarcrm.resources.CkEditor",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/bootstrap-daterangepicker/moment.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/bootstrap-daterangepicker/daterangepicker.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	/**
	 * Function to get the list of Css models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_CssScript_Model instances
	 */
	public function getHeaderCss(nectarcrm_Request $request) {
		$parentHeaderCssScriptInstances = parent::getHeaderCss($request);

		$headerCss = array(
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/jquery/gridster/jquery.gridster.min.css',
			'~layouts/'.nectarcrm_Viewer::getDefaultLayoutName().'/lib/bootstrap-daterangepicker/daterangepicker.css',
			'~libraries/jquery/jqplot/jquery.jqplot.min.css'
		);
		$cssScripts = $this->checkAndConvertCssStyles($headerCss);
		$headerCssScriptInstances = array_merge($parentHeaderCssScriptInstances , $cssScripts);
		return $headerCssScriptInstances;
	}
}