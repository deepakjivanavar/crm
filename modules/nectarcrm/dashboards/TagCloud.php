<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_TagCloud_Dashboard extends nectarcrm_IndexAjax_View {
	
	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	public function getHeaderScripts(nectarcrm_Request $request) {

		$jsFileNames = array(
			'~/libraries/jquery/jquery.tagcloud.js'
		);

		$headerScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $headerScriptInstances;
	}
	
	public function process(nectarcrm_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		
		$linkId = $request->get('linkid');
		
		$widget = nectarcrm_Widget_Model::getInstance($linkId, $currentUser->getId());
		
		$tags = nectarcrm_Tag_Model::getAllUserTags($currentUser->getId());

		//Include special script and css needed for this widget
		$viewer->assign('SCRIPTS',$this->getHeaderScripts($request));

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('TAGS', $tags);
		$viewer->assign('MODULE_NAME', $moduleName);
		
		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/TagCloudContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/TagCloud.tpl', $moduleName);
		}
		
	}
	
}