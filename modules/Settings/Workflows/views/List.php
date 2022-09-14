<?php

/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Workflows_List_View extends Settings_nectarcrm_List_View {

   public function preProcess(nectarcrm_Request $request, $display = true) {
      $viewer = $this->getViewer($request);
      $viewer->assign('SUPPORTED_MODULE_MODELS', Settings_Workflows_Module_Model::getSupportedModules());
      $viewer->assign('MODULES_COUNT', Settings_Workflows_Module_Model::getActiveWorkflowCount(true));
      $viewer->assign('CRON_RECORD_MODEL', Settings_CronTasks_Record_Model::getInstanceByName('Workflow'));
      parent::preProcess($request, $display);
   }

   public function getHeaderScripts(nectarcrm_Request $request) {
      $headerScriptInstances = parent::getHeaderScripts($request);
      $moduleName = $request->getModule();

      $jsFileNames = array(
            '~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js',
            "~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js",
      );

      $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
      $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
      return $headerScriptInstances;
   }
   
   public function getHeaderCss(nectarcrm_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/libraries/jquery/bootstrapswitch/css/bootstrap3/bootstrap-switch.min.css',
			"~layouts/".nectarcrm_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
    
    /*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(nectarcrm_Request $request, nectarcrm_Viewer $viewer) {
        $search_value = $request->get('search_value');
        $sourceModule = $request->get('sourceModule');
        $viewer->assign('SUPPORTED_MODULE_MODELS', Settings_Workflows_Module_Model::getSupportedModules());
        $viewer->assign('MODULES_COUNT', Settings_Workflows_Module_Model::getActiveWorkflowCount(true));
        $viewer->assign('CRON_RECORD_MODEL', Settings_CronTasks_Record_Model::getInstanceByName('Workflow'));
        $viewer->assign('SEARCH_VALUE', $search_value);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        
        parent::initializeListViewContents($request, $viewer);
    }

}