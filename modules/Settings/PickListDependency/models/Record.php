<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~modules/PickList/DependentPickListUtils.php');

class Settings_PickListDependency_Record_Model extends Settings_nectarcrm_Record_Model {

	private $mapping = false;
	private $sourcePickListValues = false;
	private $targetPickListValues = false;
	private $nonMappedSourcePickListValues = false;

	/**
	 * Function to get the Id
	 * @return <Number>
	 */
	public function getId() {
		return '';
	}

	public function getName() {
		return '';
	}

	public function getRecordLinks() {
		$soureModule = $this->get('sourceModule');
		$sourceField = $this->get('sourcefield');
		$targetField = $this->get('targetfield');
		$editLink = array(
			'linkurl' => "javascript:Settings_PickListDependency_Js.triggerEdit(event, '$soureModule', '$sourceField', '$targetField')",
			'linklabel' => 'LBL_EDIT',
			'linkicon' => 'icon-pencil'
		);
		$editLinkInstance = nectarcrm_Link_Model::getInstanceFromValues($editLink);

		$deleteLink = array(
			'linkurl' => "javascript:Settings_PickListDependency_Js.triggerDelete(event, '$soureModule','$sourceField', '$targetField')",
			'linklabel' => 'LBL_DELETE',
			'linkicon' => 'icon-trash'
		);
		$deleteLinkInstance = nectarcrm_Link_Model::getInstanceFromValues($deleteLink);
		return array($editLinkInstance,$deleteLinkInstance);
	}

	public function getAllPickListFields() {
		$db = PearDatabase::getInstance();
		$tabId = getTabid($this->get('sourceModule'));

		$query="select nectarcrm_field.fieldlabel,nectarcrm_field.fieldname FROM nectarcrm_field" .
				" where displaytype=1 and nectarcrm_field.tabid=? and nectarcrm_field.uitype in ('15','16') " .
				" and nectarcrm_field.presence in ('0','2') and nectarcrm_field.block != 'NULL'";

		$result = $db->pquery($query, array($tabId));
		$noofrows = $db->num_rows($result);

		$fieldlist = array();
		if($noofrows > 0) {
			for($i=0; $i<$noofrows; ++$i) {
				$fieldlist[$db->query_result($result,$i,"fieldname")] = $db->query_result($result,$i,"fieldlabel");
			}
		}
		return $fieldlist;
	}

	public function getPickListDependency() {
		if(empty($this->mapping)) {
			$dependency = nectarcrm_DependencyPicklist::getPickListDependency($this->get('sourceModule'), $this->get('sourcefield'), $this->get('targetfield'));
			$this->mapping = $dependency['valuemapping'];
		}
		return $this->mapping;
	}

	private function getPickListValues($fieldName) {
		$sourceModule = $this->get('sourceModule');
		//Need to decode the picklist values twice which are saved from old ui

		if ($fieldName == 'group_id') {
			$userRecordModel = Users_Record_Model::getCurrentUserModel();
			$picklists = array_map('decode_html', $userRecordModel->getAccessibleGroups());
		} else if ($fieldName == 'assigned_user_id') {
			$userRecordModel = Users_Record_Model::getCurrentUserModel();
			$picklists = array_map('decode_html', $userRecordModel->getAccessibleUsers());
		} else {
			$picklists = array_map('decode_html', getAllPickListValues($fieldName));
		}

		$picklistValues = array();
		foreach($picklists as $key=>$value) {
			$picklistValues[$key] = vtranslate($value, $sourceModule);
		}
		return $picklistValues;
	}

	public function getSourcePickListValues() {
		if(empty($this->sourcePickListValues)) {
			$this->sourcePickListValues = $this->getPickListValues($this->get('sourcefield'));
		}
		return $this->sourcePickListValues;
	}

	public function getTargetPickListValues() {
		if(empty($this->targetPickListValues)) {
			$this->targetPickListValues = $this->getPickListValues($this->get('targetfield'));
		}
		return $this->targetPickListValues;
	}

	public function getNonMappedSourcePickListValues() {
		if(empty($this->nonMappedSourcePickListValues)) {
			$sourcePickListValues = $this->getSourcePickListValues();
			$dependencyMapping = $this->getPickListDependency();
			foreach($dependencyMapping as $mappingDetails) {
				unset($sourcePickListValues[$mappingDetails['sourcevalue']]);
			}
			$this->nonMappedSourcePickListValues =  $sourcePickListValues;
		}
		return $this->nonMappedSourcePickListValues;
	}

	public function save($mapping) {
		$dependencyMap = array();
		$dependencyMap['sourcefield'] = $this->get('sourcefield');
		$dependencyMap['targetfield'] = $this->get('targetfield');
		$dependencyMap['valuemapping'] = $mapping;
		nectarcrm_DependencyPicklist::savePickListDependencies($this->get('sourceModule'), $dependencyMap);
		return true;
	}

	public function delete() {
		nectarcrm_DependencyPicklist::deletePickListDependencies($this->get('sourceModule'), $this->get('sourcefield'), $this->get('targetfield'));
		return true;
	}

	private function loadFieldLabels()  {
		$db = PearDatabase::getInstance();

		$tabId = getTabid($this->get('sourceModule'));
		$fieldNames = array($this->get('sourcefield'),$this->get('targetfield'));

		$query = 'SELECT fieldlabel,fieldname FROM nectarcrm_field WHERE fieldname IN ('.generateQuestionMarks($fieldNames).') AND tabid = ?';
		$params = array($fieldNames, $tabId);
		$result = $db->pquery($query, $params);
		$num_rows = $db->num_rows($result);
		for($i=0; $i<$num_rows; $i++) {
			$row = $db->query_result_rowdata($result,$i);
			$fieldName = $row['fieldname'];
			if($fieldName == $this->get('sourcefield')) {
				$this->set('sourcelabel', $row['fieldlabel']);
			}else{
				$this->set('targetlabel', $row['fieldlabel']);
			}
		}
	}

	public function getSourceFieldLabel() {
		$sourceFieldLabel = $this->get('sourcelabel');
		if(empty($sourceFieldLabel)) {
			$this->loadFieldLabels();
		}
		return vtranslate($this->get('sourcelabel'), $this->get('sourceModule'));
	}

	public function getTargetFieldLabel() {
		$targetFieldLabel = $this->get('targetlabel');
		if(empty($targetFieldLabel)) {
			$this->loadFieldLabels();
		}
		return vtranslate($this->get('targetlabel'), $this->get('sourceModule'));
	}

	public static function getInstance($module, $sourceField, $targetField) {
		$self = new self();
		$self->set('sourceModule', $module)
			->set('sourcefield', $sourceField)
			->set('targetfield', $targetField);
		return $self;
	}

	public function getDisplayValue($key) {
		return vtranslate($this->get($key), $this->get('sourceModule'));
	}

}