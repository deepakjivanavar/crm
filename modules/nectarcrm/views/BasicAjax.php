<?php
/*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************/

class nectarcrm_BasicAjax_View extends nectarcrm_Basic_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showAdvancedSearch');
		$this->exposeMethod('showSearchResults');
	}

	function checkPermission() { }

	function preProcess(nectarcrm_Request $request) {
		return true;
	}

	function postProcess(nectarcrm_Request $request) {
		return true;
	}

	function process(nectarcrm_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
		}
		return;
	}

	/**
	 * Function to display the UI for advance search on any of the module
	 * @param nectarcrm_Request $request
	 */
	function showAdvancedSearch(nectarcrm_Request $request) {
		//Modules for which search is excluded
		$excludedModuleForSearch = array('nectarcrm', 'Reports');

		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		
		if($request->get('source_module')) {
			$moduleName = $request->get('source_module');
		}
        
		$saveFilterPermitted = true;
        $saveFilterexcludedModules =  array('ModComments','RSS','Portal','Integration','PBXManager','DashBoard');
        if(in_array($moduleName, $saveFilterexcludedModules)){
            $saveFilterPermitted = false;
        }
        
		//See if it is an excluded module, If so search in home module
		if(in_array($moduleName, $excludedModuleForSearch)) {
			$moduleName = 'Home';
		}
		$module = $request->getModule();

		$customViewModel = new CustomView_Record_Model();
        $customViewModel->setModule($moduleName);
		$moduleModel = nectarcrm_Module_Model::getInstance($moduleName);
        if(!empty($moduleModel)) {
            $recordStructureInstance = nectarcrm_RecordStructure_Model::getInstanceForModule($moduleModel, nectarcrm_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
            $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        }
		$viewer->assign('SEARCHABLE_MODULES', nectarcrm_Module_Model::getSearchableModules());
		$viewer->assign('CUSTOMVIEW_MODEL', $customViewModel);
		
		if($moduleName == 'Calendar'){
			$advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
		} else{
			$advanceFilterOpsByFieldType = nectarcrm_Field_Model::getAdvancedFilterOpsByFieldType();
		}
		$viewer->assign('ADVANCED_FILTER_OPTIONS', nectarcrm_Field_Model::getAdvancedFilterOptions());
		$viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);
        $dateFilters = nectarcrm_Field_Model::getDateFilterTypes();
        foreach($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label'] = vtranslate($comparatorInfo['label'],$module);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
		$viewer->assign('SOURCE_MODULE',$moduleName);
        $viewer->assign('SOURCE_MODULE_MODEL', $moduleModel);
		$viewer->assign('MODULE', $module);
        
        $viewer->assign('SAVE_FILTER_PERMITTED', $saveFilterPermitted);

		echo $viewer->view('AdvanceSearch.tpl',$moduleName, true);
	}

	/**
	 * Function to display the Search Results
	 * @param nectarcrm_Request $request
	 */
	function showSearchResults(nectarcrm_Request $request) {
		$db = PearDatabase::getInstance();

		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$advFilterList = $request->get('advfilterlist');

		//used to show the save modify filter option
		$isAdvanceSearch = false;
		$matchingRecords = array();
		if(is_array($advFilterList) && count($advFilterList) > 0) {
			$isAdvanceSearch = true;
			$user = Users_Record_Model::getCurrentUserModel();
			$queryGenerator = new EnhancedQueryGenerator($moduleName, $user);
			$queryGenerator->setFields(array('id'));
          
            vimport('~~/modules/CustomView/CustomView.php');
            $customView = new CustomView($moduleName);
            $dateSpecificConditions = $customView->getStdFilterConditions();

			foreach ($advFilterList as $groupindex=>$groupcolumns) {
				$filtercolumns = $groupcolumns['columns'];
				if(count($filtercolumns) > 0) {
					$queryGenerator->startGroup('');
					foreach ($filtercolumns as $index=>$filter) {
                        $specialDateTimeConditions = nectarcrm_Functions::getSpecialDateTimeCondtions();
						$nameComponents = explode(':',$filter['columnname']);
						if(empty($nameComponents[2]) && $nameComponents[1] == 'crmid' && $nameComponents[0] == 'nectarcrm_crmentity') {
							$name = $queryGenerator->getSQLColumn('id');
						} else {
							$name = $nameComponents[2];
						}
                        if(($nameComponents[4] == 'D' || $nameComponents[4] == 'DT') && in_array($filter['comparator'], $dateSpecificConditions)) {
                            $filter['stdfilter'] = $filter['comparator'];
                            $valueComponents = explode(',',$filter['value']);
                            if($filter['comparator'] == 'custom') {
                                $filter['startdate'] = DateTimeField::convertToDBFormat($valueComponents[0]);
                                $filter['enddate'] = DateTimeField::convertToDBFormat($valueComponents[1]);
                            }
                            $dateFilterResolvedList = $customView->resolveDateFilterValue($filter);
                            $value[] = $queryGenerator->fixDateTimeValue($name, $dateFilterResolvedList['startdate']);
                            $value[] = $queryGenerator->fixDateTimeValue($name, $dateFilterResolvedList['enddate'], false);
                            $queryGenerator->addCondition($name, $value, 'BETWEEN');
                        } else if(($nameComponents[4] == 'D' || $nameComponents[4] == 'DT') && in_array($filter['comparator'], $specialDateTimeConditions)) {
                            $values = EnhancedQueryGenerator::getSpecialDateConditionValue($filter['comparator'], $filter['value'], $nameComponents[4], true);
                            $queryGenerator->addCondition($name, $values['date'], $values['comparator']);
                        } else{
                            $queryGenerator->addCondition($name, $filter['value'], $filter['comparator']);
                        }
						$columncondition = $filter['column_condition'];
						if(!empty($columncondition)) {
							$queryGenerator->addConditionGlue($columncondition);
						}
					}
					$queryGenerator->endGroup();
					$groupConditionGlue = $groupcolumns['condition'];
					if(!empty($groupConditionGlue))
						$queryGenerator->addConditionGlue($groupConditionGlue);
				}
			}
            if($moduleName=='Calendar'){
                $queryGenerator->addCondition('activitytype','Emails','n','AND');
            }
			$query = $queryGenerator->getQuery();
			//Remove the ordering for now to improve the speed
			//$query .= ' ORDER BY createdtime DESC';
			$result = $db->pquery($query, array());
			$rows = $db->num_rows($result);

			for($i=0; $i<$rows; ++$i) {
				$row = $db->query_result_rowdata($result, $i);
				$recordInstance = nectarcrm_Record_Model::getInstanceById($row[0]);
				$moduleName = $recordInstance->getModuleName();
				$matchingRecords[$moduleName][$row[0]] = $recordInstance;
			}
			$viewer->assign('SEARCH_MODULE', $moduleName);
		} else {
			$searchKey = $request->get('value');
			$searchModule = false;
			
			if($request->get('searchModule')) {
				$searchModule = $request->get('searchModule');
			}
			
			$viewer->assign('SEARCH_KEY', $searchKey);
			$viewer->assign('SEARCH_MODULE', $searchModule);
			$matchingRecords =  nectarcrm_Record_Model::getSearchResult($searchKey, $searchModule);
		}
		
		$matchingRecordsList = array();
		if ($matchingRecords[$moduleName]) {
			$matchingRecordsList[$moduleName] = $matchingRecords[$moduleName];
		}
		foreach ($matchingRecords as $module => $recordModelsList) {
			$matchingRecordsList[$module] = $recordModelsList;
		}

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('MATCHING_RECORDS', $matchingRecordsList);
		$viewer->assign('IS_ADVANCE_SEARCH', $isAdvanceSearch);

		echo $viewer->view('UnifiedSearchResults.tpl', '', true);
	}	
}