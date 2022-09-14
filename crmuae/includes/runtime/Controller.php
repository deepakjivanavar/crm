<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Abstract Controller Class
 */
abstract class nectarcrm_Controller {

	function __construct() { }

	function loginRequired() {
		return true;
	}

	abstract function getViewer(nectarcrm_Request $request);
	abstract function process (nectarcrm_Request $request);
	
	function validateRequest(nectarcrm_Request $request) {}
	function preProcess(nectarcrm_Request $request) {}
	function postProcess(nectarcrm_Request $request) {}

	// Control the exposure of methods to be invoked from client (kind-of RPC)
	protected $exposedMethods = array();

	/**
	 * Function that will expose methods for external access
	 * @param <String> $name - method name
	 */
	protected function exposeMethod($name) {
		if(!in_array($name, $this->exposedMethods)) {
			$this->exposedMethods[] = $name;
		}
	}

	/**
	 * Function checks if the method is exposed for client usage
	 * @param string $name - method name
	 * @return boolean
	 */
	function isMethodExposed($name) {
		if(in_array($name, $this->exposedMethods)) {
			return true;
		}
		return false;
	}

	/**
	 * Function invokes exposed methods for this class
	 * @param string $name - method name
	 * @param nectarcrm_Request $request
	 * @throws Exception
	 */
	function invokeExposedMethod() {
		$parameters = func_get_args();
		$name = array_shift($parameters);
		if (!empty($name) && $this->isMethodExposed($name)) {
			return call_user_func_array(array($this, $name), $parameters);
		}
		throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
	}
}

/**
 * Abstract Action Controller Class
 */
abstract class nectarcrm_Action_Controller extends nectarcrm_Controller {
	function __construct() {
		parent::__construct();
	}

	function getViewer(nectarcrm_Request $request) {
		throw new AppException ('Action - implement getViewer - JSONViewer');
	}
	
	function validateRequest(nectarcrm_Request $request) {
		return $request->validateReadAccess();
	}

	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	protected function preProcessDisplay(nectarcrm_Request $request) {
	}

	protected function preProcessTplName(nectarcrm_Request $request) {
		return false;
	}

	//TODO: need to revisit on this as we are not sure if this is helpful
	/*function preProcessParentTplName(nectarcrm_Request $request) {
		return false;
	}*/

	function postProcess(nectarcrm_Request $request) {
		return true;
	}
}

/**
 * Abstract View Controller Class
 */
abstract class nectarcrm_View_Controller extends nectarcrm_Action_Controller {

    protected $viewer;
    
	function __construct() {
		parent::__construct();
	}

	function getViewer(nectarcrm_Request $request) {
		if(!$this->viewer) {
			global $nectarcrm_current_version, $nectarcrm_display_version, $onlyV7Instance;
			$viewer = new nectarcrm_Viewer();
			$viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
			$viewer->assign('NECTARCRM_VERSION', $nectarcrm_current_version);
			$viewer->assign('NECTARCRM_DISPLAY_VERSION', $nectarcrm_display_version);
            $viewer->assign('ONLY_V7_INSTANCE', $onlyV7Instance);
			$this->viewer = $viewer;
		}
		return $this->viewer;
	}

	function getPageTitle(nectarcrm_Request $request) {
		$moduleName = $request->getModule();
		$recordId	= $request->get('record');
		if($recordId && $moduleName) {
			$module = nectarcrm_Module_Model::getInstance($moduleName);
			if($module && $module->isEntityModule()) {
				$recordName = nectarcrm_Util_Helper::getRecordName($recordId);
			}
		}

		if ($recordName) {
			return vtranslate($moduleName, $moduleName).' - '.$recordName;
		} else {
			$currentLang = nectarcrm_Language_Handler::getLanguage();
			$customWebTitle = nectarcrm_Language_Handler::getLanguageTranslatedString($currentLang, 'LBL_'.$moduleName.'_WEBTITLE', $request->getModule(false));
			if ($customWebTitle) {
				return $customWebTitle;
			}
			return vtranslate($moduleName, $moduleName);
		}
	}

	function preProcess(nectarcrm_Request $request, $display=true) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$viewer->assign('PAGETITLE', $this->getPageTitle($request));
		$viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
		$viewer->assign('STYLES',$this->getHeaderCss($request));
		$viewer->assign('SKIN_PATH', nectarcrm_Theme::getCurrentUserThemePath());
		$viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
		$viewer->assign('LANGUAGE', $currentUser->get('language'));

		if ($request->getModule() != 'Install') {
			$userCurrencyInfo = getCurrencySymbolandCRate($currentUser->get('currency_id'));
			$viewer->assign('USER_CURRENCY_SYMBOL', $userCurrencyInfo['symbol']);
		}
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());

		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	protected function preProcessTplName(nectarcrm_Request $request) {
		return 'Header.tpl';
	}

	//Note : To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	//TODO: Need to revisit this.
	/*function preProcessParentTplName(nectarcrm_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	protected function preProcessDisplay(nectarcrm_Request $request) {
		$viewer = $this->getViewer($request);
        $displayed = $viewer->view($this->preProcessTplName($request), $request->getModule(false));
		/*if(!$displayed) {
			$tplName = $this->preProcessParentTplName($request);
			if($tplName) {
				$viewer->view($tplName, $request->getModule());
			}
		}*/
	}


	function postProcess(nectarcrm_Request $request) {
        $moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('ACTIVITY_REMINDER', $currentUser->getCurrentUserActivityReminderInSeconds());
		$viewer->view('Footer.tpl', $moduleName);
	}

	/**
	 * Retrieves headers scripts that need to loaded in the page
	 * @param nectarcrm_Request $request - request model
	 * @return <array> - array of nectarcrm_JsScript_Model
	 */
	function getHeaderScripts(nectarcrm_Request $request){
		$headerScriptInstances = array();
		$languageHandlerShortName = nectarcrm_Language_Handler::getShortLanguageName();
		$fileName = "libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
		if (!file_exists($fileName)) {
			$fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js";
		} else {
			$fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
		}
		$jsFileNames = array($fileName);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($jsScriptInstances,$headerScriptInstances);
		return $headerScriptInstances;
	}

	function checkAndConvertJsScripts($jsFileNames) {
		$fileExtension = 'js';

		$jsScriptInstances = array();
		if($jsFileNames) {
			foreach($jsFileNames as $jsFileName) {
				// TODO Handle absolute inclusions (~/...) like in checkAndConvertCssStyles
				$jsScript = new nectarcrm_JsScript_Model();

				// external javascript source file handling
				if(strpos($jsFileName, 'http://') === 0 || strpos($jsFileName, 'https://') === 0) {
					$jsScriptInstances[$jsFileName] = $jsScript->set('src', $jsFileName);
					continue;
				}

				$completeFilePath = nectarcrm_Loader::resolveNameToPath($jsFileName, $fileExtension);

				if(file_exists($completeFilePath)) {
					if (strpos($jsFileName, '~') === 0) {
						$filePath = ltrim(ltrim($jsFileName, '~'), '/');
						// if ~~ (reference is outside nectarcrm6 folder)
						if (substr_count($jsFileName, "~") == 2) {
							$filePath = "../" . $filePath;
						}
					} else {
						$filePath = str_replace('.','/', $jsFileName) . '.'.$fileExtension;
					}

					$jsScriptInstances[$jsFileName] = $jsScript->set('src', $filePath);
				} else {
					$fallBackFilePath = nectarcrm_Loader::resolveNameToPath(nectarcrm_JavaScript::getBaseJavaScriptPath().'/'.$jsFileName, 'js');
					if(file_exists($fallBackFilePath)) {
						$filePath = str_replace('.','/', $jsFileName) . '.js';
						$jsScriptInstances[$jsFileName] = $jsScript->set('src', nectarcrm_JavaScript::getFilePath($filePath));
					}
				}
			}
		}
		return $jsScriptInstances;
	}

	/**
	 * Function returns the css files
	 * @param <Array> $cssFileNames
	 * @param <String> $fileExtension
	 * @return <Array of nectarcrm_CssScript_Model>
	 *
	 * First check if $cssFileName exists
	 * if not, check under layout folder $cssFileName eg:layouts/vlayout/$cssFileName
	 */
	function checkAndConvertCssStyles($cssFileNames, $fileExtension='css') {
		$cssStyleInstances = array();
		foreach($cssFileNames as $cssFileName) {
			$cssScriptModel = new nectarcrm_CssScript_Model();

			if(strpos($cssFileName, 'http://') === 0 || strpos($cssFileName, 'https://') === 0) {
				$cssStyleInstances[] = $cssScriptModel->set('href', $cssFileName);
				continue;
			}
			$completeFilePath = nectarcrm_Loader::resolveNameToPath($cssFileName, $fileExtension);
			$filePath = NULL;
			if(file_exists($completeFilePath)) {
				if (strpos($cssFileName, '~') === 0) {
					$filePath = ltrim(ltrim($cssFileName, '~'), '/');
					// if ~~ (reference is outside nectarcrm6 folder)
					if (substr_count($cssFileName, "~") == 2) {
						$filePath = "../" . $filePath;
					}
				} else {
					$filePath = str_replace('.','/', $cssFileName) . '.'.$fileExtension;
					$filePath = nectarcrm_Theme::getStylePath($filePath);
				}
				$cssStyleInstances[] = $cssScriptModel->set('href', $filePath);
			}
		}
		return $cssStyleInstances;
	}

	/**
	 * Retrieves css styles that need to loaded in the page
	 * @param nectarcrm_Request $request - request model
	 * @return <array> - array of nectarcrm_CssScript_Model
	 */
	function getHeaderCss(nectarcrm_Request $request){
		return array();
	}

	/**
	 * Function returns the Client side language string
	 * @param nectarcrm_Request $request
	 */
	function getJSLanguageStrings(nectarcrm_Request $request) {
		$moduleName = $request->getModule(false);
		if ($moduleName === 'Settings:Users') {
			$moduleName = 'Users';
		} 
		return nectarcrm_Language_Handler::export($moduleName, 'jsLanguageStrings');
	}
}