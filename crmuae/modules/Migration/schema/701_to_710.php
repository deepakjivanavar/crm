<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *********************************************************************************/

if (defined('NECTARCRM_UPGRADE')) {
	global $current_user, $adb;
	$db = PearDatabase::getInstance();

	//START::Workflow task's template path
	$pathsList = array();
	$taskResult = $db->pquery('SELECT classname FROM com_nectarcrm_workflow_tasktypes', array());
	while($rowData = $db->fetch_row($taskResult)) {
		$className = $rowData['classname'];
		if ($className) {
			$pathsList[$className] = vtemplate_path("Tasks/$className.tpl", 'Settings:Workflows');
		}
	}

	if ($pathsList) {
		$taskUpdateQuery = 'UPDATE com_nectarcrm_workflow_tasktypes SET templatepath = CASE';
		foreach ($pathsList as $className => $templatePath) {
			$taskUpdateQuery .= " WHEN classname='$className' THEN '$templatePath'";
		}
		$taskUpdateQuery .= ' ELSE templatepath END';
		$db->pquery($taskUpdateQuery, array());
	}
	//END::Workflow task's template path

	//START::Duplication Prevention
	$nectarcrmFieldColumns = $db->getColumnNames('nectarcrm_field');
	if (!in_array('isunique', $nectarcrmFieldColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_field ADD COLUMN isunique BOOLEAN DEFAULT 0');
	}

	$nectarcrmTabColumns = $db->getColumnNames('nectarcrm_tab');
	if (!in_array('issyncable', $nectarcrmTabColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_tab ADD COLUMN issyncable BOOLEAN DEFAULT 0');
	}
	if (!in_array('allowduplicates', $nectarcrmTabColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_tab ADD COLUMN allowduplicates BOOLEAN DEFAULT 1');
	}
	if (!in_array('sync_action_for_duplicates', $nectarcrmTabColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_tab ADD COLUMN sync_action_for_duplicates INT(1) DEFAULT 1');
	}

	//START - Enable prevention for Accounts module
	$accounts = 'Accounts';
	$db->pquery('UPDATE nectarcrm_field SET isunique=? WHERE fieldname=? AND tabid=(SELECT tabid FROM nectarcrm_tab WHERE name=?)', array(1, 'accountname', $accounts));
	$db->pquery('UPDATE nectarcrm_tab SET allowduplicates=? WHERE name=?', array(0, $accounts));
	//End - Enable prevention for Accounts module

	$db->pquery('UPDATE nectarcrm_tab SET issyncable=1', array());
	$em = new VTEventsManager($db);
	$em->registerHandler('nectarcrm.entity.beforesave', 'modules/nectarcrm/handlers/CheckDuplicateHandler.php', 'CheckDuplicateHandler');

	$em = new VTEventsManager($db);
	$em->registerHandler('nectarcrm.entity.beforerestore', 'modules/nectarcrm/handlers/CheckDuplicateHandler.php', 'CheckDuplicateHandler');
	echo '<br>Succecssfully handled duplications<br>';
	//END::Duplication Prevention

	//START::Webform Attachements
	if (!nectarcrm_Utils::CheckTable('nectarcrm_webform_file_fields')) {
		$db->pquery('CREATE TABLE IF NOT EXISTS nectarcrm_webform_file_fields(id INT(19) NOT NULL AUTO_INCREMENT, webformid INT(19) NOT NULL, fieldname VARCHAR(100) NOT NULL, fieldlabel VARCHAR(100) NOT NULL, required INT(1) NOT NULL DEFAULT 0, PRIMARY KEY (id), KEY fk_nectarcrm_webforms (webformid), CONSTRAINT fk_nectarcrm_webforms FOREIGN KEY (webformid) REFERENCES nectarcrm_webforms (id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=UTF8;', array());
	}

	$operationResult = $db->pquery('SELECT 1 FROM nectarcrm_ws_operation WHERE name=?', array('add_related'));
	if (!$db->num_rows($operationResult)) {
		$operationId = vtws_addWebserviceOperation('add_related', 'include/Webservices/AddRelated.php', 'vtws_add_related', 'POST');
		vtws_addWebserviceOperationParam($operationId, 'sourceRecordId', 'string', 1);
		vtws_addWebserviceOperationParam($operationId, 'relatedRecordId', 'string', 2);
		vtws_addWebserviceOperationParam($operationId, 'relationIdLabel', 'string', 3);
	}
	echo '<br>Succecssfully added Webforms attachements<br>';
	//END::Webform Attachements

	//START::Tag fields are pointed to cf table for the modules Assets, Services etc..
	$fieldName = 'tags';
	$moduleModels = nectarcrm_Module_Model::getAll();
	foreach ($moduleModels as $moduleModel) {
		$baseTableId = $moduleModel->basetableid;
		if ($baseTableId) {
			$baseTableName = $moduleModel->basetable;
			$customTableName = $baseTableName.'cf';
			$customTableColumns = $db->getColumnNames($customTableName);
			if (in_array($fieldName, $customTableColumns)) {
				$fieldModel = nectarcrm_Field_Model::getInstance($fieldName, $moduleModel);
				$db->pquery("UPDATE nectarcrm_field SET tablename=? WHERE fieldid=?", array($baseTableName, $fieldModel->id));
				$db->pquery("ALTER TABLE $baseTableName ADD COLUMN $fieldName VARCHAR(1)", array());

				$db->pquery("UPDATE $baseTableName, $customTableName SET $baseTableName.tags=$customTableName.tags WHERE $baseTableName.$baseTableId=$customTableName.$baseTableId", array());
				$db->pquery("ALTER TABLE $customTableName DROP COLUMN $fieldName", array());
			}
		}
	}
	echo '<br>Succecssfully generalized tag fields<br>';
	//END::Tag fields are pointed to cf table for the modules Assets, Services etc..

	//START::Follow & unfollow features
	$em = new VTEventsManager($db);
	$em->registerHandler('nectarcrm.entity.aftersave', 'modules/nectarcrm/handlers/FollowRecordHandler.php', 'FollowRecordHandler');
	//END::Follow & unfollow features

	//START::Reordering Timezones
	$fieldName = 'time_zone';
	$userModuleModel = nectarcrm_Module_Model::getInstance('Users');
	$fieldModel = nectarcrm_Field_Model::getInstance($fieldName, $userModuleModel);
	if ($fieldModel) {
		$picklistValues = $fieldModel->getPicklistValues();

		$utcTimezones = preg_grep('/\(UTC\)/', $picklistValues);
		asort($utcTimezones);

		$utcPlusTimezones = preg_grep('/\(UTC\+/', $picklistValues);
		asort($utcPlusTimezones);

		$utcMinusTimezones = preg_grep('/\(UTC\-/', $picklistValues);
		arsort($utcMinusTimezones);

		$timeZones = array_merge($utcMinusTimezones, $utcTimezones, $utcPlusTimezones);
		$originalPicklistValues = array_flip(nectarcrm_Util_Helper::getPickListValues($fieldName));

		$orderedPicklists = array();
		$i = 0;
		foreach ($timeZones as $timeZone => $value) {
			$orderedPicklists[$originalPicklistValues[$timeZone]] = $i++;
		}
		ksort($orderedPicklists);

		$moduleModel = new Settings_Picklist_Module_Model();
		$moduleModel->updateSequence($fieldName, $orderedPicklists);
		echo '<br>Succecssfully reordered timezones<br>';
	}
	//END::Reordering Timezones

	//START::Differentiate custom modules from nectarcrm modules
	$nectarcrmTabColumns = $db->getColumnNames('nectarcrm_tab');
	if (!in_array('source', $nectarcrmTabColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_tab ADD COLUMN source VARCHAR(255) DEFAULT "custom"', array());
	}
	$db->pquery('UPDATE nectarcrm_tab SET source=NULL', array());

	$packageModules = array('Project', 'ProjectTask', 'ProjectMilestone'); /* Projects zip is bundle */
	$packageZips = glob("packages/nectarcrm/*/*.zip");
	foreach ($packageZips as $zipfile) {
		$packageModules[] = str_replace('.zip', '', array_pop(explode("/", $zipfile)));
	}

	$db->pquery('UPDATE nectarcrm_tab SET source="custom" WHERE version IS NOT NULL AND name NOT IN ('.generateQuestionMarks($packageModules).')', $packageModules);
	echo '<br>Succecssfully added source column nectarcrm tab table<br>';
	//END::Differentiate custom modules from nectarcrm modules

	//START::Google calendar sync settings
	if (!nectarcrm_Utils::CheckTable('nectarcrm_google_event_calendar_mapping')) {
		$db->pquery('CREATE TABLE nectarcrm_google_event_calendar_mapping (event_id VARCHAR(255) DEFAULT NULL, calendar_id VARCHAR(255) DEFAULT NULL, user_id INT(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8', array());
		echo '<br>Succecssfully nectarcrm_google_event_calendar_mapping table created<br>';
	}
	//END::Google calendar sync settings

	//START::Centralize user field table for easy query with context of user across module
	$generalUserFieldTable = 'nectarcrm_crmentity_user_field';
	if (!nectarcrm_Utils::CheckTable($generalUserFieldTable)) {
		nectarcrm_Utils::CreateTable($generalUserFieldTable,
				'(`recordid` INT(19) NOT NULL, 
				`userid` INT(19) NOT NULL,
				`starred` VARCHAR(100) DEFAULT NULL)', true);
	}

	if (nectarcrm_Utils::CheckTable($generalUserFieldTable)) {
		$indexRes = $db->pquery("SHOW INDEX FROM $generalUserFieldTable WHERE NON_UNIQUE=? AND KEY_NAME=?", array('1', 'record_user_idx'));
		if ($db->num_rows($indexRes) < 2) {
			$db->pquery('ALTER TABLE nectarcrm_crmentity_user_field ADD CONSTRAINT record_user_idx UNIQUE KEY(recordid, userid)', array());
		}

		$checkUserFieldConstraintExists = $db->pquery('SELECT DISTINCT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE table_name=? AND CONSTRAINT_SCHEMA=?', array($generalUserFieldTable, $db->dbName));
		if ($db->num_rows($checkUserFieldConstraintExists) < 1) {
			$db->pquery('ALTER TABLE nectarcrm_crmentity_user_field ADD CONSTRAINT `fk_nectarcrm_crmentity_user_field_recordid` FOREIGN KEY (`recordid`) REFERENCES `nectarcrm_crmentity`(`crmid`) ON DELETE CASCADE', array());
		}
		
	}

	$migratedTables = array();
	$userTableResult = $db->pquery('SELECT nectarcrm_tab.tabid, nectarcrm_tab.name, tablename, fieldid FROM nectarcrm_field INNER JOIN nectarcrm_tab ON nectarcrm_tab.tabid=nectarcrm_field.tabid WHERE fieldname=?', array('starred'));
	while ($row = $db->fetch_array($userTableResult)) {
		$fieldId = $row['fieldid'];
		$moduleName = $row['name'];
		$oldTableName = $row['tablename'];

		$db->pquery('UPDATE nectarcrm_field SET tablename=? WHERE fieldid=? AND tablename=?', array($generalUserFieldTable, $fieldId, $oldTableName));
		echo "Updated starred field for module $moduleName to point generic table => $generalUserFieldTable<br>";

		if (nectarcrm_Utils::CheckTable($oldTableName)) {
			if (!in_array($oldTableName, $migratedTables)) {
				if ($oldTableName != $generalUserFieldTable) {
					//Insert entries from module specific table to generic table for follow up records
					$db->pquery("INSERT INTO $generalUserFieldTable (recordid, userid, starred) (SELECT recordid,userid,starred FROM $oldTableName INNER JOIN nectarcrm_crmentity ON $oldTableName.recordid = nectarcrm_crmentity.crmid)", array());
					echo "entries moved from $oldTableName to $generalUserFieldTable table<br>";

					//Drop module specific user table
					$db->pquery("DROP TABLE $oldTableName", array());
					echo "module specific user field table $oldTableName has been dropped<br>";
					array_push($migratedTables, $oldTableName);
				}
			}
		}
	}
	echo '<br>Succesfully centralize user field table for easy query with context of user across module<br>';
	//END::Centralize user field table for easy query with context of user across module

	//START::Adding new parent TOOLS in menu
	$appsList = array('Tools' => array('Rss', 'Portal', 'EmailTemplates', 'RecycleBin'));
	foreach ($appsList as $appName => $appModules) {
		$menuInstance = nectarcrm_Menu::getInstance($appName);
		foreach ($appModules as $moduleName) {
			$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
			if ($moduleModel) {
				Settings_MenuEditor_Module_Model::addModuleToApp($moduleName, $appName);
				$menuInstance->addModule($moduleModel);
			}
		}
	}

	$tabResult1 = $db->pquery('SELECT tabid, name, parent FROM nectarcrm_tab WHERE presence IN (?, ?) AND source=?', array(0, 2, 'custom'));
	while ($row = $db->fetch_row($tabResult1)) {
		$parentFromDb = $row['parent'];
		if ($parentFromDb) {
			$moduleName = $row['name'];
			$parentTabs = explode(',', $parentFromDb);
			foreach ($parentTabs as $parentTab) {
				Settings_MenuEditor_Module_Model::addModuleToApp($moduleName, $parentTab);
			}

			$menuTab = $parentTabs[0];
			$menuInstance = nectarcrm_Menu::getInstance($menuTab);
			if ($menuInstance) {
				$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
				$menuInstance->addModule($moduleModel);
			}
		}
	}

	$tabResult2 = $db->pquery('SELECT tabid, name FROM nectarcrm_tab', array());
	$moduleTabIds = array();
	while ($row = $db->fetch_array($tabResult2)) {
		$moduleTabIds[$row['name']] = $row['tabid'];
	}

	$defSequenceList = array(
		'MARKETING' => array(	$moduleTabIds['Campaigns'],
								$moduleTabIds['Leads'],
								$moduleTabIds['Contacts'],
								$moduleTabIds['Accounts'],
		),
		'SALES' => array(		$moduleTabIds['Potentials'],
								$moduleTabIds['Quotes'],
								$moduleTabIds['Products'],
								$moduleTabIds['Services'],
								$moduleTabIds['SMSNotifier'],
								$moduleTabIds['Contacts'],
								$moduleTabIds['Accounts']
		),
		'SUPPORT' => array(		$moduleTabIds['HelpDesk'],
								$moduleTabIds['Faq'],
								$moduleTabIds['ServiceContracts'],
								$moduleTabIds['Assets'],
								$moduleTabIds['SMSNotifier'],
								$moduleTabIds['Contacts'],
								$moduleTabIds['Accounts']
		),
		'INVENTORY' => array(	$moduleTabIds['Products'],
								$moduleTabIds['Services'],
								$moduleTabIds['PriceBooks'],
								$moduleTabIds['Invoice'],
								$moduleTabIds['SalesOrder'],
								$moduleTabIds['PurchaseOrder'],
								$moduleTabIds['Vendors'],
								$moduleTabIds['Contacts'],
								$moduleTabIds['Accounts']
		),
		'PROJECT' => array(		$moduleTabIds['Project'],
								$moduleTabIds['ProjectTask'],
								$moduleTabIds['ProjectMilestone'],
								$moduleTabIds['Contacts'],
								$moduleTabIds['Accounts']
		),
		'TOOLS' => array(		$moduleTabIds['EmailTemplates'],
								$moduleTabIds['Rss'],
								$moduleTabIds['Portal'],
								$moduleTabIds['RecycleBin']
		)
	);

	$db->pquery('DELETE FROM nectarcrm_app2tab WHERE appname=? AND tabid IN (?, ?, ?)', array('SUPPORT', $moduleTabIds['Project'], $moduleTabIds['ProjectTask'], $moduleTabIds['ProjectMilestone']));
	$db->pquery('DELETE FROM nectarcrm_app2tab WHERE appname=? AND tabid=?', array('INVENTORY', $moduleTabIds['Assets']));

	foreach ($defSequenceList as $appName => $tabIdsList) {
		$appTabResult1 = $db->pquery('SELECT tabid FROM nectarcrm_app2tab WHERE appname=? AND tabid NOT IN ('.generateQuestionMarks($tabIdsList).')', array($appName, $tabIdsList));
		while ($row = $db->fetch_array($appTabResult1)) {
			$defSequenceList[$appName][] = $row['tabid'];
		}
	}

	foreach ($defSequenceList as $appName => $tabIdsList) {
		foreach ($tabIdsList as $seq => $tabId) {
			$appTabResult2 = $db->pquery('SELECT 1 FROM nectarcrm_app2tab WHERE tabid=? AND appname=?', array($tabId, $appName));

			$params = array($seq+1, $tabId, $appName);
			if ($db->num_rows($appTabResult2)) {
				$query = 'UPDATE nectarcrm_app2tab SET sequence=? WHERE tabid=? AND appname=?';
			} else {
				$query = 'INSERT INTO nectarcrm_app2tab(sequence,tabid,appname) VALUES(?,?,?)';
			}
			$db->pquery($query, $params);
		}
	}
	echo '<br>Succesfully added RSS, Email Templates for new parent TOOLS<br>';
	//END::Adding new parent TOOLS in menu

	//START::Supporting to store dashboard size
	$dashboardWidgetColumns = $db->getColumnNames('nectarcrm_module_dashboard_widgets');
	if (!in_array('size', $dashboardWidgetColumns)) {
		$db->pquery('ALTER TABLE nectarcrm_module_dashboard_widgets ADD COLUMN size VARCHAR(50)', array());
	}
	//END::Supporting to store dashboard size

	//START::Profile save failures because of Reports module entry is not available in the nectarcrm_profile2standardpermissions
	$query = 'SELECT DISTINCT profileid FROM nectarcrm_profile';
	$result = $adb->pquery($query, array());

	$tabIdsList = array(getTabid('Reports'));
	$actionIdPerms = array(	0 => 1,//Save
							1 => 1,//EditView
							2 => 1,//Delete
							3 => 0,//Index
							4 => 0,//DetailView
							7 => 1);//CreateView

	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$profileId = $adb->query_result($result, $i, 'profileid');

		foreach ($tabIdsList as $tabId) {
			foreach ($actionIdPerms as $actionId => $permission) {
				$isExist = $adb->pquery('SELECT 1 FROM nectarcrm_profile2standardpermissions WHERE profileid=? AND tabid=? AND operation=?', array($profileId, $tabId, $actionId));
				if ($adb->num_rows($isExist)) {
					$query = 'UPDATE nectarcrm_profile2standardpermissions SET permissions=? WHERE profileid=? AND tabid=? AND operation=?';
				} else {
					$query = 'INSERT INTO nectarcrm_profile2standardpermissions(permissions, profileid, tabid, operation) VALUES (?, ?, ?, ?)';
				}
				$db->pquery($query, array($actionIdPerms[$actionId], $profileId, $tabId, $actionId));
			}
		}
	}
	//END::Profile save failures because of Reports module entry is not available in the nectarcrm_profile2standardpermissions

	//START::Updating custom view and report columns, filters for createdtime and modifiedtime fields as typeofdata (T~...) is being transformed to (DT~...)
	$cvTables = array('nectarcrm_cvcolumnlist', 'nectarcrm_cvadvfilter');
	foreach ($cvTables as $tableName) {
		$updatedColumnsList = array();
		$result = $db->pquery("SELECT columnname FROM $tableName WHERE columnname LIKE ? OR columnname LIKE ?", array('nectarcrm_crmentity:createdtime%:T', 'nectarcrm_crmentity:modifiedtime%:T'));
		while ($rowData = $db->fetch_array($result)) {
			$columnName = $rowData['columnname'];
			if (!array_key_exists($columnName, $updatedColumnsList)) {
				if (preg_match('/nectarcrm_crmentity:createdtime:(\w*\:)*T/', $columnName) || preg_match('/nectarcrm_crmentity:modifiedtime:(\w*\:)*T/', $columnName)) {
					$columnParts = explode(':', $columnName);
					$lastKey = count($columnParts)-1;

					if ($columnParts[$lastKey] == 'T') {
						$columnParts[$lastKey] = 'DT';
						$updatedColumnsList[$columnName] = implode(':', $columnParts);
					}
				}
			}
		}

		if ($updatedColumnsList) {
			$cvQuery = "UPDATE $tableName SET columnname = CASE columnname";
			foreach ($updatedColumnsList as $oldColumnName => $newColumnName) {
				$cvQuery .= " WHEN '$oldColumnName' THEN '$newColumnName'";
			}
			$cvQuery .= ' ELSE columnname END';
			$db->pquery($cvQuery, array());
		}
		echo "<br>Succecssfully migrated columns in <b>$tableName</b> table<br>";
	}

	$reportTables = array('nectarcrm_selectcolumn', 'nectarcrm_relcriteria');
	foreach ($reportTables as $tableName) {
		$updatedColumnsList = array();
		$result = $db->pquery("SELECT columnname FROM $tableName WHERE columnname LIKE ? OR columnname LIKE ?", array('nectarcrm_crmentity%:createdtime:%T', 'nectarcrm_crmentity%:modifiedtime:%T'));
		while ($rowData = $db->fetch_array($result)) {
			$columnName = $rowData['columnname'];
			if (!array_key_exists($columnName, $updatedColumnsList)) {
				if (preg_match('/nectarcrm_crmentity(\w*):createdtime:(\w*\:)*T/', $columnName) || preg_match('/nectarcrm_crmentity(\w*):modifiedtime:(\w*\:)*T/', $columnName)) {
					$columnParts = explode(':', $columnName);
					$lastKey = count($columnParts)-1;

					if ($columnParts[$lastKey] == 'T') {
						$columnParts[$lastKey] = 'DT';
						$updatedColumnsList[$columnName] = implode(':', $columnParts);
					}
				}
			}
		}

		if ($updatedColumnsList) {
			$reportQuery = "UPDATE $tableName SET columnname = CASE columnname";
			foreach ($updatedColumnsList as $oldColumnName => $newColumnName) {
				$reportQuery .= " WHEN '$oldColumnName' THEN '$newColumnName'";
			}
			$reportQuery .= ' ELSE columnname END';
			$db->pquery($reportQuery, array());
		}
		echo "<br>Succecssfully migrated columns in <b>$tableName</b> table<br>";
	}
	//END::Updating custom view and report columns, filters for createdtime and modifiedtime fields as typeofdata (T~...) is being transformed to (DT~...)

	//Update existing package modules
	Install_Utils_Model::installModules();

	echo '<br>Succecssfully nectarcrm version updated to <b>7.1.0</b><br>';
}
