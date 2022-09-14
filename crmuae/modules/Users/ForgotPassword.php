<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Users_ForgotPassword_Handler {

	public function changePassword($data){
		global $site_URL;
        $request = new nectarcrm_Request($data);
        $viewer = nectarcrm_Viewer::getInstance();
		$companyModel = nectarcrm_CompanyDetails_Model::getInstanceById();
        $companyName = $companyModel->get('organizationname');
        $organisationDetails=$companyModel->getLogo();
        $logoTitle = $organisationDetails->get('title');
		$logoName = $organisationDetails->get('imagepath');
        $moduleName = 'Users';
		$viewer->assign('LOGOURL',$site_URL.$logoName);
		$viewer->assign('TITLE',$logoTitle);
		$viewer->assign('COMPANYNAME',$companyName);
		$viewer->assign('USERNAME',$request->get('username'));
		$changePasswordTrackUrl = $site_URL."modules/Users/actions/ForgotPassword.php";
		$viewer->assign('TRACKURL',$changePasswordTrackUrl);
		$viewer->view('ForgotPassword.tpl',$moduleName);
	}

}