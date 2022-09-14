<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class nectarcrm_MentionedUsers_Action extends nectarcrm_Action_Controller {

	function checkPermission(nectarcrm_Request $request) {
		return true;
	}
    
    public function process(nectarcrm_Request $request) {
        $mentionRule = Settings_Notifications_Task_Model::getInstance('Mention');
        $message = $request->get('message');
        $mentionedUsers = $mentionRule->getMentionedNames($message);
        $commentId = $request->get('crmid');
        $commentRecord = nectarcrm_Record_Model::getInstanceById($commentId, nectarcrm_Module_Model::getInstance('ModComments'));
        $commentOwnerId = $commentRecord->get('creator');
        $commentOwnerName = Users_Record_Model::getInstanceById($commentOwnerId, Users_Module_Model::getInstance('Users'))->getName();
        $commentOwnerName = str_replace(' ', '',$commentOwnerName);
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $currentUser->getId();
        
        if($commentOwnerId !== $currentUserId) {
            $mentionedUsers[] = decode_html($commentOwnerName);
        }
        $currentUserName = decode_html(str_replace(' ', '',$currentUser->getName()));
        //Unset current user from the mentioned users
        if(($key = array_search(strtolower($currentUserName), $mentionedUsers)) !== false) {
            unset($mentionedUsers[$key]);
        }
        
        $usersString = '@'.implode(' @', $mentionedUsers);
        $mentionedUsersData['usersString'] = $usersString.' ';
        
        $response = new nectarcrm_Response();
        $response->setResult($mentionedUsersData);
        $response->emit();
    }
}
