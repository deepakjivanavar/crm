<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_DownloadFile_Action extends nectarcrm_Action_Controller {

	public function checkPermission(nectarcrm_Request $request) {
		$moduleName = $request->getModule();

		if(!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}

	public function process(nectarcrm_Request $request) {
        $db = PearDatabase::getInstance();

        $attachmentId = $request->get('attachment_id');
        $query = "SELECT * FROM nectarcrm_attachments WHERE attachmentsid = ?" ;
        $result = $db->pquery($query, array($attachmentId));

        if($db->num_rows($result) == 1)
        {
            $row = $db->fetchByAssoc($result, 0);
            $fileType = $row["type"];
            $name = $row["name"];
            $filepath = $row["path"];
            $name = decode_html($name);
            $saved_filename = $attachmentId."_".$name;
            $disk_file_size = filesize($filepath.$saved_filename);
            $filesize = $disk_file_size + ($disk_file_size % 1024);
            $fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);

            header("Content-type: $fileType");
            header("Pragma: public");
            header("Cache-Control: private");
            header("Content-Disposition: attachment; filename=$name");
            header("Content-Description: PHP Generated Data");
            echo $fileContent;
        }
    }
}

?>
