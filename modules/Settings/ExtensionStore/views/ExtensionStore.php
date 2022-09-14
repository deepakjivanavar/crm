<?php
/* +**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 * ***********************************************************************************/

include_once dirname(__FILE__).'/../libraries/LoaderSuggest.php';

class Settings_ExtensionStore_ExtensionStore_View extends Settings_nectarcrm_Index_View {

	protected $registrationStatus = false;
	protected $passwordStatus = false;
	protected $customerProfile = array();
	protected $customerCardInfo = array();

	public function __construct() {
		parent::__construct();
		$this->init();
		$this->exposeMethod('searchExtension');
		$this->exposeMethod('detail');
		$this->exposeMethod('installationLog');
		$this->exposeMethod('oneClickInstall');
	}

	protected function init() {
		$modelInstance = $this->getModelInstance();
		$this->registrationStatus = $modelInstance->checkRegistration();

		if ($this->registrationStatus) {
			$pwdStatus = false;
			$pwdStatus = $modelInstance->passwordStatus();
			if (!$pwdStatus) {
				$sessionIdentifer = $modelInstance->getSessionIdentifier();
				$pwd = $_SESSION[$sessionIdentifer.'_password'];
				if (!empty($pwd)) {
					$pwdStatus = true;
				}
			}
			$this->passwordStatus = $pwdStatus;
		}

		if ($this->registrationStatus && $this->passwordStatus) {
			$customerProfile = $modelInstance->getProfile();
			/* check if pwd is updated in marketplace by user, then marketplace will
			 * respond with unauthozied message while getting customer profile. 
			 * So at this time we will remove 
			 * old password from DB and session, So user will login again with new
			 * password
			 */
			if ($customerProfile['id']) {
				$this->customerProfile = $customerProfile;
				$customerCardId = $customerProfile['CustomerCardId'];
				if (!empty($customerCardId)) {
					$this->customerCardInfo = $modelInstance->getCardDetails($customerCardId);
				}
			} else {
				$modelInstance->unsetPassword();
				$this->passwordStatus = false;
			}
		}
	}

	protected function getModelInstance() {
		if (!isset($this->modelInstance)) {
			$this->modelInstance = Settings_ExtensionStore_Extension_Model::getInstance();
		}
		return $this->modelInstance;
	}

	function preProcess(nectarcrm_Request $request) {
		parent::preProcess($request, false);
		$extensionStoreModuleModel = Settings_ExtensionStore_Module_Model::getInstance();
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_MODEL', $extensionStoreModuleModel);
		$viewer->assign('PASSWORD_STATUS', $this->passwordStatus);
		$viewer->assign('CUSTOMER_PROFILE', $this->customerProfile);
		$this->preProcessSettings($request, false);
		$this->preProcessDisplay($request);
	}

	public function process(nectarcrm_Request $request) {
		$modelInstance = $this->getModelInstance();
		$mode = $request->getMode();
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);

		if ($this->registrationStatus) {
			$userName = $modelInstance->getRegisteredUser();
			$viewer->assign('USER_NAME', $userName);
		}
		if ($this->registrationStatus && $this->passwordStatus) {
			$viewer->assign('CUSTOMER_CARD_INFO', $this->customerCardInfo);
			$viewer->assign('CUSTOMER_PROFILE', $this->customerProfile);
		}

		$loaderRequired = false;
		$loaderInstance = new Settings_ModuleManager_LoaderSuggest();
		$loaderInfo = $loaderRequired ? $loaderInstance->nectarcrm_extensionloader_suggest() : null;

		$viewer->assign('LOADER_REQUIRED', $loaderRequired);
		$viewer->assign('LOADER_INFO', $loaderInfo);
		$viewer->assign('PASSWORD_STATUS', $this->passwordStatus);
		$viewer->assign('IS_PRO', true);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('EXTENSIONS_LIST', $modelInstance->getListings());
		$viewer->assign('REGISTRATION_STATUS', $this->registrationStatus);
		$viewer->view('Index.tpl', $qualifiedModuleName);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param nectarcrm_Request $request
	 * @return <Array> - List of nectarcrm_JsScript_Model instances
	 */
	function getHeaderScripts(nectarcrm_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"libraries.jquery.jqueryRating",
			"libraries.jquery.boxslider.jqueryBxslider",
			"~modules/Settings/ExtensionStore/libraries/jasny-bootstrap.min.js",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	protected function searchExtension(nectarcrm_Request $request) {
		$searchTerm = $request->get('searchTerm');
		$searchType = $request->get('type');
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$modelInstance = $this->getModelInstance();

		$viewer->assign('PASSWORD_STATUS', $this->passwordStatus);
		$viewer->assign('IS_PRO', true);
		$viewer->assign('REGISTRATION_STATUS', $this->registrationStatus);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('EXTENSIONS_LIST', $modelInstance->findListings($searchTerm, $searchType));
		$viewer->view('ExtensionModules.tpl', $qualifiedModuleName);
	}

	protected function detail(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$extensionId = $request->get('extensionId');
		$moduleAction = $request->get('moduleAction');
		$modelInstance = $this->getModelInstance();

		$extensionDetail = $modelInstance->getExtensionListings($extensionId);
		$extension = $extensionDetail[$extensionId];

		if ($extension && $extension->isnectarcrmCompatible()) {
			$customerReviews = $modelInstance->getCustomerReviews($extensionId);
			$screenShots = $modelInstance->getScreenShots($extensionId);
			$authorInfo = $modelInstance->getListingAuthor($extensionId);

			$viewer->assign('PASSWORD_STATUS', $this->passwordStatus);
			$viewer->assign('CUSTOMER_CARD_INFO', $this->customerCardInfo);
			$viewer->assign('CUSTOMER_PROFILE', $this->customerProfile);

			if ($request->get('extensionName') == 'Payments') {
				$moduleModel = nectarcrm_Module_Model::getInstance('Subscription');
				if ($moduleModel && $moduleModel->get('presence') == 0) {
					$viewer->assign('CHECK_SUBSCRIPTION', TRUE);
				}
			}

			$extension = $extensionDetail[$extensionId];
			$viewer->assign('IS_PRO', true);
			$viewer->assign('MODULE_ACTION', $moduleAction);
			$viewer->assign('SCREEN_SHOTS', $screenShots);
			$viewer->assign('AUTHOR_INFO', $authorInfo);
			$viewer->assign('CUSTOMER_REVIEWS', $customerReviews);
			$viewer->assign('EXTENSION_DETAIL', $extension);
			$viewer->assign('EXTENSION_MODULE_MODEL', $extension->get('moduleModel'));
			$viewer->assign('EXTENSION_ID', $extensionId);
			$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
			$viewer->assign('REGISTRATION_STATUS', $this->registrationStatus);
			$viewer->view('Detail.tpl', $qualifiedModuleName);
		} else {
			$viewer->assign('EXTENSION_LABEL', $extension->get('label'));
			$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
			$viewer->view('ExtensionCompatibleError.tpl', $qualifiedModuleName);
		}
	}

	protected function installationLog(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		global $nectarcrm_Utils_Log;
		$viewer->assign('NECTARCRM_UTILS_LOG', $nectarcrm_Utils_Log);
		$nectarcrm_Utils_Log = true;
		$qualifiedModuleName = $request->getModule(false);

		$extensionId = $request->get('extensionId');
		$targetModuleName = $request->get('targetModule');
		$moduleAction = $request->get('moduleAction');
		$modelInstance = $this->getModelInstance();

		$response = $modelInstance->getInstanceById($extensionId);
		if ($response['success']) {
			$extensionModel = $response['result'];
			$package = $extensionModel->getPackage();
			if ($package) {
				$importedModuleName = $package->getModuleName();
				$isLanguagePackage = $package->isLanguageType();

				if ($moduleAction === 'Upgrade') {
					if (($isLanguagePackage && (trim($package->xpath_value('prefix')) == $targetModuleName)) || (!$isLanguagePackage && $importedModuleName === $targetModuleName)) {
						$upgradeError = false;
					}
				} else {
					$upgradeError = false;
				}
				if (!$upgradeError) {
					if (!$isLanguagePackage) {
						$moduleModel = nectarcrm_Module_Model::getInstance($importedModuleName);
						$viewer->assign('MODULE_EXISTS', ($moduleModel) ? true : false);
						$viewer->assign('MODULE_DIR_NAME', '../modules/'.$importedModuleName);

						if (!$extensionModel->isUpgradable()) {
							$viewer->assign('SAME_VERSION', true);
						}
					}
					$moduleType = $package->type();
					$fileName = $extensionModel->getFileName();
				} else {
					$viewer->assign('ERROR', true);
					$viewer->assign('ERROR_MESSAGE', vtranslate('LBL_INVALID_FILE', $qualifiedModuleName));
				}
			} else {
				$viewer->assign('ERROR', true);
				$viewer->assign('ERROR_MESSAGE', vtranslate('LBL_INVALID_FILE', $qualifiedModuleName));
			}
		} else {
			$viewer->assign('ERROR', true);
			$viewer->assign('ERROR_MESSAGE', $response['message']);
		}

		if ($extensionId && $extensionModel) {
			if ($moduleAction !== 'Upgrade') {
				$extensionModel->installTrackDetails();
			}
			if (strtolower($moduleType) === 'language') {
				$package = new nectarcrm_Language();
			} else {
				$package = new nectarcrm_Package();
			}

			$viewer->assign('EXTENSION_NAME', $targetModuleName);
			$viewer->assign('MODULE_ACTION', $moduleAction);
			$viewer->assign('MODULE_PACKAGE', $package);
			$viewer->assign('TARGET_MODULE_INSTANCE', nectarcrm_Module_Model::getInstance($targetModuleName));
			$viewer->assign('MODULE_FILE_NAME', Settings_ExtensionStore_Extension_Model::getUploadDirectory().'/'.$fileName);
		}
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('InstallationLog.tpl', $qualifiedModuleName);
	}

	protected function oneClickInstall(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
		global $nectarcrm_Utils_Log;
		$viewer->assign('NECTARCRM_UTILS_LOG', $nectarcrm_Utils_Log);
		$nectarcrm_Utils_Log = true;
		$upgradeError = true;
		$qualifiedModuleName = $request->getModule(false);
		$extensionId = $request->get('extensionId');
		$moduleAction = $request->get('moduleAction'); //Import/Upgrade
		$trial = $request->get('trial');
		$modelInstance = $this->getModelInstance();
		$response = $modelInstance->getInstanceById($extensionId, $trial);

		if ($response['success']) {
			$extensionModel = $response['result'];
			$package = $extensionModel->getPackage();
			if ($package) {
				$importedModuleName = $package->getModuleName();
				$isLanguagePackage = $package->isLanguageType();

				if ($moduleAction === 'Upgrade') {
					$targetModuleName = $request->get('extensionName');
					if (($isLanguagePackage && (trim($package->xpath_value('prefix')) == $targetModuleName)) || (!$isLanguagePackage && $importedModuleName === $targetModuleName)) {
						$upgradeError = false;
					}
				} else {
					$upgradeError = false;
				}
				if (!$upgradeError) {
					if (!$isLanguagePackage) {
						if (!$extensionModel->isUpgradable()) {
							$viewer->assign('SAME_VERSION', true);
						}
					}

					$moduleType = $packageType = $package->type();
					$fileName = $extensionModel->getFileName();
				} else {
					$viewer->assign('ERROR', true);
					$viewer->assign('ERROR_MESSAGE', vtranslate('LBL_INVALID_FILE', $qualifiedModuleName));
				}
			} else {
				$viewer->assign('ERROR', true);
				$viewer->assign('ERROR_MESSAGE', vtranslate('LBL_INVALID_FILE', $qualifiedModuleName));
			}
		} else {
			$viewer->assign('ERROR', true);
			$viewer->assign('ERROR_MESSAGE', $response['message']);
		}

		if ($extensionId && $extensionModel) {
			if ($moduleAction !== 'Upgrade') {
				$extensionModel->installTrackDetails();
			}
			if (strtolower($moduleType) === 'language') {
				$package = new nectarcrm_Language();
			} else {
				$package = new nectarcrm_Package();
			}

			$viewer->assign('EXTENSION_NAME', $request->get('extensionName'));
			$viewer->assign('MODULE_ACTION', $moduleAction);
			$viewer->assign('MODULE_PACKAGE', $package);
			$viewer->assign('TARGET_MODULE_INSTANCE', nectarcrm_Module_Model::getInstance($targetModuleName));
			$viewer->assign('MODULE_FILE_NAME', Settings_ExtensionStore_Extension_Model::getUploadDirectory().'/'.$fileName);
		}

		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('InstallationLog.tpl', $qualifiedModuleName);
	}

}
