<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_TagCloudSearchAjax_View extends nectarcrm_IndexAjax_View {

	function process(nectarcrm_Request $request) {
		
		$tagId = $request->get('tag_id');
		$taggedRecords = nectarcrm_Tag_Model::getTaggedRecords($tagId);
		
		$viewer = $this->getViewer($request);
		
		$viewer->assign('TAGGED_RECORDS',$taggedRecords);
		$viewer->assign('TAG_NAME',$request->get('tag_name'));
		
		echo $viewer->view('TagCloudResults.tpl', $module, true);
	}
	
	
	
}