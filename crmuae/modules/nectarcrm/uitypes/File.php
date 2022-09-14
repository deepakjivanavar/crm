<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_File_UIType extends nectarcrm_Base_UIType {
    
    /**
	 * Function to get the Template name for the current UI Type Object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/File.tpl';
	}
    
    /**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param <String> $value
	 * @param <Integer> $recordId
	 * @param <nectarcrm_Record_Model>
	 * @return <String>
	 */
	public function getDisplayValue($value, $recordId=false, $recordModel=false) {
        $db = PearDatabase::getInstance();
        $attachmentId = (int)$value;
        $displayValue = '--';
        if($attachmentId) {
            $query = 'SELECT name FROM nectarcrm_attachments WHERE attachmentsid = ?';
            $result = $db->pquery($query,array($attachmentId));
            $displayValue = $attachmentName = $db->query_result($result,0,'name');
            if($recordModel) {
                $url = 'index.php?module='.$recordModel->getModuleName().
                       '&action=DownloadAttachment&record='.$recordModel->getId().'&attachmentid='.$attachmentId;
                $displayValue = '<a href="'.$url.'" title="'.vtranslate('LBL_DOWNLOAD_FILE',$recordModel->getModuleName()).'">'.
                                    textlength_check($attachmentName).
                                '</a>';
            }
        }
        return $displayValue;
    }
    
}