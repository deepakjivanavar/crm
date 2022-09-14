<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

abstract class CustomerPortal_API_Abstract {

	private $activeUser = false;
	private $activeCustomer = false;
	protected $resolvedValueCache = array();

	protected function initActiveUser($user) {
		$this->activeUser = $user;
	}

	protected function hasActiveUser() {
		$user = $this->getActiveUser();
		return ($user !== false);
	}

	protected function setActiveUser($user) {
		$this->initActiveUser($user);
	}

	public function getActiveUser() {
		return $this->activeUser;
	}

	protected function initActiveCustomer($customer) {
		$this->activeCustomer = $customer;
	}

	protected function hasActiveCustomer() {
		$customer = $this->getActiveCustomer();
		return ($customer !== false);
	}

	protected function setActiveCustomer($customer) {
		$this->initActiveCustomer($customer);
	}

	protected function getActiveCustomer() {
		return $this->activeCustomer;
	}

	function authenticatePortalUser($username, $password) {
		global $adb;
		$current_date = date("Y-m-d");
		$sql = "SELECT id, user_name, user_password,last_login_time, isactive, support_start_date, support_end_date, cryptmode FROM nectarcrm_portalinfo
					INNER JOIN nectarcrm_customerdetails ON nectarcrm_portalinfo.id=nectarcrm_customerdetails.customerid
					INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid=nectarcrm_portalinfo.id
						WHERE nectarcrm_crmentity.deleted=0 AND user_name=? AND isactive=1 AND nectarcrm_customerdetails.portal=1
						AND (nectarcrm_customerdetails.support_start_date <= ? OR nectarcrm_customerdetails.support_start_date IS NULL)
						AND (nectarcrm_customerdetails.support_end_date >= ? OR nectarcrm_customerdetails.support_end_date IS NULL)";

		$result = $adb->pquery($sql, array($username, $current_date, $current_date));
		$num_rows = $adb->num_rows($result);

		$isAuthenticated = false;
		if ($num_rows >= 0) {
			for ($i = 0; $i < $num_rows; ++$i) {
				$customerId = $adb->query_result($result, $i, 'id');
				if (nectarcrm_Functions::compareEncryptedPassword($password, $adb->query_result($result, $i, 'user_password'), $adb->query_result($result, $i, 'cryptmode'))) {
					break;
				} else {
					$customerId = null;
				}
			}
			$isActive = $adb->query_result($result, $i, 'isactive');
			if ($customerId) {
				$support_end_date = $adb->query_result($result, $i, 'support_end_date');
				if ($isActive && ($support_end_date >= $current_date || $support_end_date == null)) {
					$current_customer = CRMEntity::getInstance('Contacts');
					$current_customer->id = $customerId;
					$userName = $adb->query_result($result, $i, 'user_name');
					$current_customer->username = $userName;
					$this->setActiveCustomer($current_customer);

					global $current_user;
					$current_user = CRMEntity::getInstance('Users');
					$userid = Users::getActiveAdminId();
					$current_user->retrieveCurrentUserInfoFromFile($userid);
					$this->setActiveUser($current_user);
					$isAuthenticated = true;
				}
			} else if ($isActive && $support_end_date <= $current_date) {
				throw new Exception("Access to the portal was disabled on ".$support_end_date, 1413);
			} else if ($isActive == 0) {
				throw new Exception("Portal access has not been enabled for this account.", 1414);
			}
		}
		return $isAuthenticated;
	}

	protected function getParent($contactId) {
		$sql = sprintf("SELECT account_id FROM Contacts WHERE id = '%s';", $contactId);
		$result = vtws_query($sql, $this->getActiveUser());
		return $result[0]['account_id'];
	}

	protected function relatedRecordIds($module, $moduleLabel, $parentId = null) {
		global $adb, $log;
		$relatedIds = array();
		$mode = CustomerPortal_Settings_Utils::getDefaultMode($module);
		if ($parentId == null) {
			$contactWebserviceId = vtws_getWebserviceEntityId('Contacts', $this->getActiveCustomer()->id);
			if ($mode == 'mine') {
				$parentId = $contactWebserviceId;
			} else {
				if (in_array($module, array('Products', 'Services'))) {
					$relatedIds = CustomerPortal_Utils::getAllRecordIds($module, $this->getActiveUser());
					return $relatedIds;
				} else {
					$parentId = $this->getParent($contactWebserviceId);
					if (empty($parentId)) {
						$parentId = $contactWebserviceId;
					}
				}
			}
		}
		$webserviceObject = nectarcrmWebserviceObject::fromId($adb, $parentId);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $this->getActiveUser(), $adb, $log);
		$relatedIds = $handler->relatedIds($parentId, $module, $moduleLabel);
		return $relatedIds;
	}

	protected function isRecordAccessible($recordId, $module = null, $moduleLabel = null) {
		global $adb;

		if (empty($module)) {
			$module = nectarcrmWebserviceObject::fromId($adb, $recordId)->getEntityName();
			$moduleLabel = CustomerPortal_Utils::getRelatedModuleLabel($module);
		}

		if (empty($moduleLabel)) {
			$moduleLabel = CustomerPortal_Utils::getRelatedModuleLabel($module);
		}
		$mode = CustomerPortal_Settings_Utils::getDefaultMode($module);
		$relatedIds = $this->relatedRecordIds($module, $moduleLabel);
		if (in_array($recordId, $relatedIds) || ($mode == 'all' && in_array($module, array('Products', 'Services')))) {
			return true;
		} else {
			return false;
		}
	}

	protected function isFaqPublished($recordId) {
		$sql = sprintf('SELECT faqstatus FROM %s WHERE id=\'%s\';', 'Faq', $recordId);
		$result = vtws_query($sql, $this->getActiveUser());
		if ($result[0]['faqstatus'] == 'Published') {
			return true;
		} else {
			return false;
		}
	}

}
