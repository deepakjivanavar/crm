<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

chdir(dirname(__FILE__) . '/../..');
include_once 'vtlib/nectarcrm/Module.php';
include_once 'vtlib/nectarcrm/Package.php';
include_once 'includes/main/WebUI.php';

include_once 'include/Webservices/Utils.php';

class nectarcrm_Tools_Console_Controller {
	const PROMPT_ANY      = 1;
	const PROMPT_OPTIONAL = 2;
	const PROMPT_NUMERIC  = 3;
	const PROMPT_ALPHANUMERIC = 4;
	const PROMPT_NAME     = 5;
	const PROMPT_LABEL    = 6;
	const PROMPT_PATH     = 7;
	
	protected $interactive = true;
	protected $arguments   = array();

	protected function __construct() { }
	
	public function setArguments($args, $interactive) {
		$this->arguments   = $args;
		$this->interactive = $interactive;
		return $this;
	}

	protected function handle() {
		global $argv;
		$this->arguments = $argv;
		
		// Discard the script name.
		array_shift($this->arguments);
		
		if ($this->arguments) {
			$this->arguments = explode('=', $this->arguments[0]);
			$this->interactive = false;
		}

		$this->welcome();
		$this->options();	
	}

	protected function welcome() {
		if ($this->interactive) {
			echo "Welcome to nectarcrm CRM Creator.\n";
			echo "This tool will enable you to get started with developing extensions with ease.\n";
			echo "Have a good time. Press CTRL+C to \"quit\".\n";
		}
	}

	protected function options() {
		if ($this->interactive) {
			echo "Choose the options below:\n";
			echo "1. Create New Module.\n";
			echo "2. Create New Layout.\n";
			echo "3. Create New Language Pack.\n";
			echo "4. Create Test Language Pack.\n";
			echo "5. Import Module.\n";
			echo "6. Update Module.\n";
			echo "7. Remove Module.\n";
			$option = $this->prompt("Enter your choice: ", self::PROMPT_NUMERIC);
		} else {
			$option = array_shift($this->arguments);
			switch ($option) {
				case '--import': $option = 5; break;
				case '--update': $option = 6; break;
				case '--remove': $option = 7; break;
			}
		}

		try {
			switch (intval($option)) {
				case 1: $this->handleCreateModule(); break;
				case 2: $this->handleCreateLayout(); break;
				case 3: $this->handleCreateLanguage(); break;
				case 4: $this->handleCreateTestLanguage(); break;
				case 5: $this->handleImportModule(); break;
				case 6: $this->handleUpdateModule(); break;
				case 7: $this->handleRemoveModule(); break;
			}
		} catch (Exception $e) {
			echo "ERROR: " .$e->getMessage() . "\n";
			echo $e->getTraceAsString();
			echo "\n";
		}
	}

	protected function prompt($msg='', $type=self::PROMPT_ANY) {
		do {
			if ($msg) echo $msg;
			$value = trim(fgets(STDIN));

			if (!$value && $type == self::PROMPT_OPTIONAL) {
				return $value;

			} else if ($value) {

				switch ($type) {
					case self::PROMPT_NUMERIC:
						if (is_numeric($value)) {
							return $value;
						}
						break;
					case self::PROMPT_ALPHANUMERIC:
						if (!preg_match("/^[a-zA-Z0-9]+$/", $value)) {
							return $value;
						}
						break;
					case self::PROMPT_NAME:
						if (!preg_match("/^[a-zA-Z][^a-zA-Z0-9_ ]*$/", $value)) {
							return $value;
						}
						break;
					case self::PROMPT_LABEL:
						if (!preg_match("/^[a-zA-Z0-9_ ]+$/", $value)) {
							return $value;
						}
						break;
					case self::PROMPT_PATH:
						if (!preg_match("/^[a-zA-Z0-9_:+.-\/\\\\]+$/", $value)) {
							return $value;
						}
					default:
						return $value;
				}
			}
		} while (true);
	}

	protected function toAlphaNumeric($value) {
		return preg_replace("/[^a-zA-Z0-9_]/", "", $value);
	}

	protected function findFiles($dir, $file_pattern, &$files) {
		$items = glob($dir . '/*', GLOB_NOSORT);
		foreach ($items as $item) {
			if (is_file($item)) {
				if (!$file_pattern || preg_match("/$file_pattern/", $item)) {
					$files[] = $item;
				}
			} else if (is_dir($item) && ($dir != $item)) {
				$this->findFiles($item, $file_pattern, $files);
			}
		}
	}

	// Option Handlers
	protected function handleCreateModule() {
		$controller = new nectarcrm_Tools_Console_ModuleController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}

	protected function handleCreateLanguage() {
		$controller = new nectarcrm_Tools_Console_LanguageController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}

	protected function handleCreateTestLanguage() {
		$controller = new nectarcrm_Tools_Console_TestLanguageController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}

	protected function handleCreateLayout() {
		$controller = new nectarcrm_Tools_Console_LayoutController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}
	
	protected function handleImportModule() {
		$controller = new nectarcrm_Tools_Console_ImportController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}
	
	protected function handleUpdateModule() {
		$controller = new nectarcrm_Tools_Console_UpdateController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}
	
	protected function handleRemoveModule() {
		$controller = new nectarcrm_Tools_Console_RemoveController();
		$controller->setArguments($this->arguments, $this->interactive)->handle();
	}

	// Static
	public static function run() {
		$singleton = new self();
		$singleton->handle();
	}
}

class nectarcrm_Tools_Console_ModuleController extends nectarcrm_Tools_Console_Controller {

	public function handle() {
		echo ">>> MODULE <<<\n";

		$moduleInformation = array();
		do {
			$moduleInformation['name'] = ucwords($this->prompt("Enter module name: ", self::PROMPT_NAME));
			$module = $this->find($moduleInformation['name']);
			if (!$module) {
				break;
			}
			echo "ERROR: " . $moduleInformation['name'] . " module already exists, try another.\n";
		} while (true);

		$moduleInformation['entityfieldlabel'] = 'Name';

		$entityfieldlabel = ucwords($this->prompt(sprintf("Entity field (%s): ",
				$moduleInformation['entityfieldlabel']), self::PROMPT_OPTIONAL));
		if ($entityfieldlabel) {
			$moduleInformation['entityfieldlabel'] = $entityfieldlabel;
		}

		$moduleInformation['parent'] = 'Tools';

		echo "Creating ...";
		$this->create($moduleInformation);
		echo "DONE.\n";
	}

	public function find($name) {
		return nectarcrm_Module::getInstance($name);
	}

	protected function create($moduleInformation) {
		$moduleInformation['entityfieldname']  = strtolower($this->toAlphaNumeric($moduleInformation['entityfieldlabel']));

		$module = new nectarcrm_Module();
		$module->name = $moduleInformation['name'];
		$module->parent=$moduleInformation['parent'];
		$module->save();

		$module->initTables();

		$block = new nectarcrm_Block();
		$block->label = 'LBL_'. strtoupper($module->name) . '_INFORMATION';
		$module->addBlock($block);

		$blockcf = new nectarcrm_Block();
		$blockcf->label = 'LBL_CUSTOM_INFORMATION';
		$module->addBlock($blockcf);

		$field1  = new nectarcrm_Field();
		$field1->name = $moduleInformation['entityfieldname'];
		$field1->label= $moduleInformation['entityfieldlabel'];
		$field1->uitype= 2;
		$field1->column = $field1->name;
		$field1->columntype = 'VARCHAR(255)';
		$field1->typeofdata = 'V~M';
		$block->addField($field1);

		$module->setEntityIdentifier($field1);

		/** Common fields that should be in every module, linked to nectarcrm CRM core table */
		$field2 = new nectarcrm_Field();
		$field2->name = 'assigned_user_id';
		$field2->label = 'Assigned To';
		$field2->table = 'nectarcrm_crmentity';
		$field2->column = 'smownerid';
		$field2->uitype = 53;
		$field2->typeofdata = 'V~M';
		$block->addField($field2);

		$field3 = new nectarcrm_Field();
		$field3->name = 'createdtime';
		$field3->label= 'Created Time';
		$field3->table = 'nectarcrm_crmentity';
		$field3->column = 'createdtime';
		$field3->uitype = 70;
		$field3->typeofdata = 'T~O';
		$field3->displaytype= 2;
		$block->addField($field3);

		$field4 = new nectarcrm_Field();
		$field4->name = 'modifiedtime';
		$field4->label= 'Modified Time';
		$field4->table = 'nectarcrm_crmentity';
		$field4->column = 'modifiedtime';
		$field4->uitype = 70;
		$field4->typeofdata = 'T~O';
		$field4->displaytype= 2;
		$block->addField($field4);

		// Create default custom filter (mandatory)
		$filter1 = new nectarcrm_Filter();
		$filter1->name = 'All';
		$filter1->isdefault = true;
		$module->addFilter($filter1);
		// Add fields to the filter created
		$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2);

		// Set sharing access of this module
		$module->setDefaultSharing();

		// Enable and Disable available tools
		$module->enableTools(Array('Import', 'Export', 'Merge'));

		// Initialize Webservice support
		$module->initWebservice();

		// Create files
		$this->createFiles($module, $field1);

		// Link to menu
		Settings_MenuEditor_Module_Model::addModuleToApp($module->name, $module->parent);
	}

	protected function createFiles(nectarcrm_Module $module, nectarcrm_Field $entityField) {
		$targetpath = 'modules/' . $module->name;

		if (!is_file($targetpath)) {
			mkdir($targetpath);
			mkdir($targetpath . '/language');

			$templatepath = 'vtlib/ModuleDir/6.0.0';

			$moduleFileContents = file_get_contents($templatepath . '/ModuleName.php');
			$replacevars = array(
				'ModuleName'   => $module->name,
				'<modulename>' => strtolower($module->name),
				'<entityfieldlabel>' => $entityField->label,
				'<entitycolumn>' => $entityField->column,
				'<entityfieldname>' => $entityField->name,
			);

			foreach ($replacevars as $key => $value) {
				$moduleFileContents = str_replace($key, $value, $moduleFileContents);
			}
			file_put_contents($targetpath.'/'.$module->name.'.php', $moduleFileContents);
		}
	}

}

class nectarcrm_Tools_Console_LayoutController extends nectarcrm_Tools_Console_Controller {

	// Similar grouped patterns to identify the line on which tpl filename is specified.
	const VIEWERREGEX = '/\$viewer->(view|fetch)[^\(]*\(([^\)]+)/';
	const RETURNTPLREGEX = '/(return)([ ]+[\'"]+[^;]+)/';

	const TPLREGEX    = '/[\'"]([^\'"]+)/';

	public function handle() {
		echo ">>> LAYOUT <<<\n";

		$layoutInformation = array();
		do {
			$layoutInformation['name'] = strtolower($this->prompt("Enter layout name: ", self::PROMPT_NAME));
			if (!file_exists( 'layouts/' . $layoutInformation['name'])) {
				break;
			}
			echo "ERROR: " . $layoutInformation['name'] . " already exists, try another.\n";
		} while (true);

		echo "Creating ...";
		$this->create($layoutInformation);
		echo "DONE.\n";
	}

	protected function create($layoutInformation) {
		$files = array();
		$this->findFiles( 'includes', '.php$', $files);
		$this->findFiles( 'modules', '.php$', $files);

		$layoutdir =  'layouts/' . $layoutInformation['name'] . '/';

		foreach ($files as $file) {
			$tplfolder = $layoutdir . "modules/nectarcrm";
			if (preg_match("/modules\/([^\/]+)\/([^\/]+)\//", $file, $fmatch)) {
				$tplfolder = $layoutdir . "modules/" . $fmatch[1];
				if ($fmatch[1] == 'Settings') {
					$tplfolder .= '/' . $fmatch[2];
				}
			}

			$tpls = array();
			$this->findTemplateNames($file, $tpls);
			$tpls = array_unique($tpls);

			if ($tpls) {
				foreach ($tpls as $tpl) {
					$tplname = basename($tpl, true);
					// Fix sub-directory path
					$tplpath = $tplfolder . '/'. substr($tpl, 0, strpos($tpl, $tplname));
					if (!file_exists($tplpath)) {
						mkdir($tplpath, 0755, true);
					}
					if (!file_exists($tplpath.$tplname)) {
						$initialContent = "{* License Text *}\n";
						// Enable debug to make it easy to implement.
						$initialContent.= "{debug}{* REMOVE THIS LINE AFTER IMPLEMENTATION *}\n\n";
						file_put_contents($tplpath.$tplname, $initialContent);
					}
					file_put_contents($tplpath.$tplname, "{* $file *}\n", FILE_APPEND);
				}
			}
		}
	}

	protected function findTemplateNames($file, &$tpls, $inreturn=false) {
		$contents = file_get_contents($file);

		$regex = $inreturn ? self::RETURNTPLREGEX : self::VIEWERREGEX;
		if (preg_match_all($regex, $contents, $matches)) {
			foreach ($matches[2] as $match) {
				if (preg_match(self::TPLREGEX, $match, $matches2)) {
					if (stripos($matches2[1], '.tpl') !== false) {
						$tpls[] = $matches2[1];
					}
				}
			}
			// Viewer files can also have return tpl calls - find them.
			if (!$inreturn) {
				$this->findTemplateNames($file, $tpls, true);
			}
		}
	}
}

class nectarcrm_Tools_Console_LanguageController extends nectarcrm_Tools_Console_Controller {

	const BASE_LANG_PREFIX = 'en_us';

	public function handle() {
		echo ">>> LANGUAGE <<<\n";

		$languageInformation = array();
		do {
			$languageInformation['prefix'] = strtolower($this->prompt("Enter (languagecode_countrycode): ", self::PROMPT_NAME));
			if (!file_exists( 'languages/' . $languageInformation['prefix'])) {
				break;
			}
			echo "ERROR: " . $languageInformation['prefix'] . " already exists, try another.\n";
		} while (true);

		echo "Creating ...";
		$this->create($languageInformation);
		echo "DONE.\n";
	}

	protected function create($languageInformation) {
		$files = array();
		$this->findFiles( 'languages/'.self::BASE_LANG_PREFIX, '.php$', $files);

		foreach ($files as $file) {
			$filename = basename($file, true);
			$dir = substr($file, 0, strpos($file, $filename));
			$dir = str_replace('languages/'.self::BASE_LANG_PREFIX, 'languages/'.$languageInformation['prefix'], $dir);
			if (!file_exists($dir)) mkdir($dir);

			if (isset($languageInformation['prefix_value'])) {
				$contents = file_get_contents($file);
				$contents = preg_replace("/(=>[^'\"]+['\"])(.*)/", sprintf('$1%s$2', $languageInformation['prefix_value']), $contents);
				file_put_contents($dir.'/'.$filename, $contents);
			} else {
				copy($file, $dir.'/'.$filename);
			}
		}
	}

	protected function deploy($languageInformation) {
		if (!isset($languageInformation['label'])) {
			echo "Language label not specified.";
			return;
		}

		$db = PearDatabase::getInstance();
		$check = $db->pquery('SELECT 1 FROM nectarcrm_language WHERE prefix=?', $languageInformation['prefix']);
		if ($check && $db->num_rows($check)) {
			;
		} else {
			$db->pquery('INSERT INTO nectarcrm_language (id,name,prefix,label,lastupdated,isdefault,active) VALUES(?,?,?,?,?,?,?)',
					array($db->getUniqueId('nectarcrm_language'), $languageInformation['label'], $languageInformation['prefix'],
							$languageInformation['label'], date('Y-m-d H:i:s'), 0, 1));
		}
	}
}

class nectarcrm_Tools_Console_TestLanguageController extends nectarcrm_Tools_Console_LanguageController {

	public function handle() {
		echo ">>> TEST LANGUAGE <<<\n";

		$languageInformation = array('label' => 'TEST', 'prefix' => 'te_st', 'prefix_value' => '✔ ');

		echo "Creating ...";
		$this->create($languageInformation);
		echo "DONE\n";

		echo "Deploying ...";
		$this->deploy($languageInformation);
		echo "DONE.\n";
	}
}

class nectarcrm_Tools_Console_ImportController extends nectarcrm_Tools_Console_Controller {
	
	public function handle() {
		if ($this->interactive) {
			echo ">>> IMPORT MODULE <<<\n";
			do {
				$path = $this->prompt("Enter package path: ", self::PROMPT_PATH);
				if (file_exists($path)) {
					break;
				}
				echo "ERROR: " . $path . " - file not found, try another.\n";
			} while (true);
		} else {
			$path = array_shift($this->arguments);
		}
		
		if (file_exists($path)) {
			$package = new nectarcrm_Package();
			$module  = $package->getModuleNameFromZip($path);
			
			$moduleInstance = nectarcrm_Module::getInstance($module);
			if ($moduleInstance) {
				echo "ERROR: Module $module already exists!\n";
			} else {
				echo "Importing ...";
				$package->import($path);
				echo "DONE.\n";
			}			
			
		} else {
			throw new Exception("Package file $path not found.");
		}
		
	}
}

class nectarcrm_Tools_Console_UpdateController extends nectarcrm_Tools_Console_Controller {
	
	public function handle() {
		if ($this->interactive) {
			echo ">>> UPDATE MODULE <<<\n";
			do {
				$path = $this->prompt("Enter package path: ", self::PROMPT_PATH);
				if (file_exists($path)) {
					break;
				}
				echo "ERROR: " . $path . " - file not found, try another.\n";
			} while (true);
		} else {
			$path = array_shift($this->arguments);
		}
		
		if (file_exists($path)) {
			$package = new nectarcrm_Package();
			$module  = $package->getModuleNameFromZip($path);
			
			$moduleInstance = nectarcrm_Module::getInstance($module);
			if (!$moduleInstance) {
				echo "ERROR: Module $module not found!\n";
			} else {
				echo "Updating ...";
				$package->update($moduleInstance, $path);
				echo "DONE.\n";
			}			
			
		} else {
			throw new Exception("Package file $path not found.");
		}
		
	}
}

class nectarcrm_Tools_Console_RemoveController extends nectarcrm_Tools_Console_Controller {
	
	public function handle() {
		if ($this->interactive) {
			echo ">>> REMOVE MODULE <<<\n";
			do {
				$module = $this->prompt("Enter module name: ", self::PROMPT_NAME);
				$moduleInstance = nectarcrm_Module::getInstance($module);
				if (!$moduleInstance) {
					echo "ERROR: Module $module not found, try another.\n";
				} else {
					echo "Removing ...";
					$moduleInstance->delete();
					echo "DONE.\n";
				}
			} while (true);
		} else {
			$module = array_shift($this->arguments);
			$moduleInstance = nectarcrm_Module::getInstance($module);
			if (!$moduleInstance) {
				echo "ERROR: Module $module not found!\n";
			} else {
				echo "Removing ...";
				$moduleInstance->delete();
				echo "DONE.\n";
			}			
		}
	}
}

if (php_sapi_name() == 'cli') {
	nectarcrm_Tools_Console_Controller::run();
} else {
	echo "Usage: php -f vtlib/tools/creator.php";
}
