<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_Notebook_Model extends nectarcrm_Widget_Model {
	
	public function getContent() {
		$data = Zend_Json::decode(decode_html($this->get('data')));
		return $data['contents'];
		
	}
	
	public function getLastSavedDate() {
		$data = Zend_Json::decode(decode_html($this->get('data')));
		return $data['lastSavedOn'];
		
	}
	
	public function save($request) {
		$db = PearDatabase::getInstance();
		$content = $request->get('contents');
		$noteBookId = $request->get('widgetid');
		$date_var = date("Y-m-d H:i:s");
		$date = $db->formatDate($date_var, true);
		
		$dataValue = array();
		$dataValue['contents'] = $content;
		$dataValue['lastSavedOn'] = $date;
		
		$data = Zend_Json::encode((object) $dataValue);
		$this->set('data', nectarcrm_Util_Helper::toSafeHTML($data));
		
		
		$db->pquery('UPDATE nectarcrm_module_dashboard_widgets SET data=? WHERE id=?', array($data, $noteBookId));
	}

	public static function getUserInstance($widgetId) {
			$currentUser = Users_Record_Model::getCurrentUserModel();

			$db = PearDatabase::getInstance();
			
            // linkurl is needed for dashboard widget to load in nectarcrm7
			$result = $db->pquery('SELECT nectarcrm_module_dashboard_widgets.*,nectarcrm_links.linkurl FROM nectarcrm_module_dashboard_widgets 
			INNER JOIN nectarcrm_links ON nectarcrm_links.linkid = nectarcrm_module_dashboard_widgets.linkid 
			WHERE linktype = ? AND nectarcrm_module_dashboard_widgets.id = ? AND nectarcrm_module_dashboard_widgets.userid = ?', array('DASHBOARDWIDGET', $widgetId, $currentUser->getId()));
			
			$self = new self();
			if($db->num_rows($result)) {
				$row = $db->query_result_rowdata($result, 0);
				$self->setData($row);
			}
		return $self;
		
	}
	
}