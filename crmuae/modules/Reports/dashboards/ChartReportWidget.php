<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_ChartReportWidget_Dashboard extends nectarcrm_IndexAjax_View {

	public function process(nectarcrm_Request $request) {
        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$record = $request->get('reportid');
        $widgetId = $request->get('widgetid');

		$reportModel = Reports_Record_Model::getInstanceById($record);
		$reportChartModel = Reports_Chart_Model::getInstanceById($reportModel);
        $primaryModule = $reportModel->getPrimaryModule();
        $moduleModel = nectarcrm_Module_Model::getInstance($primaryModule);
        if(!$moduleModel->isPermitted('DetailView')){
			$viewer->assign('MESSAGE', $primaryModule.' '.vtranslate('LBL_NOT_ACCESSIBLE'));
			$viewer->view('OperationNotPermitted.tpl', $primaryModule);
        }
		$secondaryModules = $reportModel->getSecondaryModules();
		if(empty($secondaryModules)) {
			$viewer->assign('CLICK_THROUGH', true);
		}

		$viewer->assign('CHART_TYPE', $reportChartModel->getChartType());
        $data = $reportChartModel->getData();
        $data = json_encode($data, JSON_HEX_APOS);
		$viewer->assign('DATA', $data);
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $widget = nectarcrm_Widget_Model::getInstanceWithReportId($record, $currentUser->getId());
        $widget->set('title',$reportModel->getName().' ('.vtranslate($primaryModule, $primaryModule).')');
		$viewer->assign('WIDGET', $widget);

		$viewer->assign('RECORD_ID', $record);
        $viewer->assign('WIDGET_ID', $widgetId);
		$viewer->assign('REPORT_MODEL', $reportModel);
		$viewer->assign('SECONDARY_MODULES',$secondaryModules);
		$viewer->assign('MODULE', $moduleName);
        $viewer->assign('PRIMARY_MODULE', $primaryModule);

		$isPercentExist = false;
		$selectedDataFields = $reportChartModel->get('datafields');
		foreach ($selectedDataFields as $dataField) {
			list($tableName, $columnName, $moduleField, $fieldName, $single) = split(':', $dataField);
			list($relModuleName, $fieldLabel) = split('_', $moduleField);
			$relModuleModel = nectarcrm_Module_Model::getInstance($relModuleName);
			$fieldModel = nectarcrm_Field_Model::getInstance($fieldName, $relModuleModel);
			if ($fieldModel && $fieldModel->getFieldDataType() != 'currency') {
				$isPercentExist = true;
				break;
			} else if (!$fieldModel) {
				$isPercentExist = true;
			}
		}
		$yAxisFieldDataType = (!$isPercentExist) ? 'currency' : '';
		$viewer->assign('YAXIS_FIELD_TYPE', $yAxisFieldDataType);

        $content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/DashBoardWidget.tpl', $moduleName);
		}
	}
}

