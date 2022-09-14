<?php

/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Tags_Record_Model extends Settings_nectarcrm_Record_Model {
 
    public function getId() {
        return $this->get('id');
    }
    
    public function getName() {
        return $this->get('tag');
    }
    
    
    /**
	 * Function to get the list view actions for the record
	 * @return <Array> - Associate array of nectarcrm_Link_Model instances
	 */
	public function getRecordLinks() {

		$links = array();
		$recordLinks = array(
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_EDIT',
				'linkurl' => 'javascript:Settings_Tags_List_Js.triggerEdit(event)',
				'linkicon' => 'icon-pencil'
			),
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => "javascript:Settings_Tags_List_Js.triggerDelete('".$this->getDeleteActionUrl()."')",
				'linkicon' => 'icon-trash'
			)
		);
		foreach ($recordLinks as $recordLink) {
			$links[] = nectarcrm_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}
    
    public function getDeleteActionUrl() {
        return 'index.php?module=nectarcrm&action=TagCloud&mode=remove&tag_id='.$this->getId();
    }
    
    public function getRowInfo() {
        return $this->getData();
    }
}