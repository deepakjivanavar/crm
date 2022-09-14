<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ********************************************************************************/

// Note is used to store customer information.
class Documents extends CRMEntity {

	var $log;
	var $db;
	var $table_name = "nectarcrm_notes";
	var $table_index= 'notesid';
	var $default_note_name_dom = array('Meeting nectarcrm_notes', 'Reminder');

	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_notes','nectarcrm_notescf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_notes'=>'notesid','nectarcrm_senotesrel'=>'notesid','nectarcrm_notescf'=>'notesid');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_notescf', 'notesid');

	var $column_fields = Array();

	var $sortby_fields = Array('title','modifiedtime','filename','createdtime','lastname','filedownloadcount','smownerid');

	// This is used to retrieve related nectarcrm_fields from form posts.
	var $additional_column_fields = Array('', '', '', '');

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
				'Title'=>Array('notes'=>'title'),
				'File Name'=>Array('notes'=>'filename'),
				'Modified Time'=>Array('crmentity'=>'modifiedtime'),
				'Assigned To' => Array('crmentity'=>'smownerid'),
				'Folder Name' => Array('attachmentsfolder'=>'folderid')
				);
	var $list_fields_name = Array(
					'Title'=>'notes_title',
					'File Name'=>'filename',
					'Modified Time'=>'modifiedtime',
					'Assigned To'=>'assigned_user_id',
					'Folder Name' => 'folderid'
					 );

	var $search_fields = Array(
					'Title' => Array('notes'=>'notes_title'),
					'File Name' => Array('notes'=>'filename'),
					'Assigned To' => Array('crmentity'=>'smownerid'),
					'Folder Name' => Array('attachmentsfolder'=>'foldername')
		);

	var $search_fields_name = Array(
					'Title' => 'notes_title',
					'File Name' => 'filename',
					'Assigned To' => 'assigned_user_id',
					'Folder Name' => 'folderid'
	);
	var $list_link_field= 'notes_title';
	var $old_filename = '';
	//var $groupTable = Array('nectarcrm_notegrouprelation','notesid');

	var $mandatory_fields = Array('notes_title','createdtime' ,'modifiedtime','filename','filesize','filetype','filedownloadcount','assigned_user_id','document_source','notecontent','filelocationtype','folderid');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'title';
	var $default_sort_order = 'ASC';
	function Documents() {
		$this->log = LoggerManager::getLogger('notes');
		$this->log->debug("Entering Documents() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Documents');
		$this->log->debug("Exiting Documents method ...");
	}

	function save_module($module)
	{
		global $log,$adb,$upload_badext;
		$insertion_mode = $this->mode;
		if(isset($this->parentid) && $this->parentid != '')
			$relid =  $this->parentid;
		//inserting into nectarcrm_senotesrel
		if(isset($relid) && $relid != '')
		{
			$this->insertintonotesrel($relid,$this->id);
		}
		$filetype_fieldname = $this->getFileTypeFieldName();
		$filename_fieldname = $this->getFile_FieldName();
		if($this->column_fields[$filetype_fieldname] == 'I' ){
			if($_FILES[$filename_fieldname]['name'] != ''){
				$errCode=$_FILES[$filename_fieldname]['error'];
					if($errCode == 0){
						foreach($_FILES as $fileindex => $files)
						{
							if($files['name'] != '' && $files['size'] > 0){
								$filename = $_FILES[$filename_fieldname]['name'];
								$filename = from_html(preg_replace('/\s+/', '_', $filename));
								$filetype = $_FILES[$filename_fieldname]['type'];
								$filesize = $_FILES[$filename_fieldname]['size'];
								$filelocationtype = 'I';
								$binFile = sanitizeUploadFileName($filename, $upload_badext);
								$filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
							}
						}

					}
			}elseif($this->mode == 'edit') {
				$fileres = $adb->pquery("select filetype, filesize,filename,filedownloadcount,filelocationtype from nectarcrm_notes where notesid=?", array($this->id));
				if ($adb->num_rows($fileres) > 0) {
					$filename = $adb->query_result($fileres, 0, 'filename');
					$filetype = $adb->query_result($fileres, 0, 'filetype');
					$filesize = $adb->query_result($fileres, 0, 'filesize');
					$filedownloadcount = $adb->query_result($fileres, 0, 'filedownloadcount');
					$filelocationtype = $adb->query_result($fileres, 0, 'filelocationtype');
				}
			}elseif($this->column_fields[$filename_fieldname]) {
				$filename = $this->column_fields[$filename_fieldname];
				$filesize = $this->column_fields['filesize'];
				$filetype = $this->column_fields['filetype'];
				$filelocationtype = $this->column_fields[$filetype_fieldname];
				$filedownloadcount = 0;
			} else {
				$filelocationtype = 'I';
				$filetype = '';
				$filesize = 0;
				$filedownloadcount = null;
			}
		} else if($this->column_fields[$filetype_fieldname] == 'E' ){
			$filelocationtype = 'E';
			$filename = $this->column_fields[$filename_fieldname];
			// If filename does not has the protocol prefix, default it to http://
			// Protocol prefix could be like (https://, smb://, file://, \\, smb:\\,...)
			if(!empty($filename) && !preg_match('/^\w{1,5}:\/\/|^\w{0,3}:?\\\\\\\\/', trim($filename), $match)) {
				$filename = "http://$filename";
			}
			$filetype = '';
			$filesize = 0;
			$filedownloadcount = null;
		}
		$query = "UPDATE nectarcrm_notes SET filename = ? ,filesize = ?, filetype = ? , filelocationtype = ? , filedownloadcount = ? WHERE notesid = ?";
		$re=$adb->pquery($query,array(decode_html($filename),$filesize,$filetype,$filelocationtype,$filedownloadcount,$this->id));
		//Inserting into attachments table
		if($filelocationtype == 'I') {
			$this->insertIntoAttachment($this->id,'Documents');
		}else{
			$query = "delete from nectarcrm_seattachmentsrel where crmid = ?";
			$qparams = array($this->id);
			$adb->pquery($query, $qparams);
		}
		//set the column_fields so that its available in the event handlers
		$this->column_fields['filename'] = $filename;
		$this->column_fields['filesize'] = $filesize;
		$this->column_fields['filetype'] = $filetype;
		$this->column_fields['filedownloadcount'] = $filedownloadcount;
	}


	/**
	 *      This function is used to add the nectarcrm_attachments. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 *      @param int $id  - entity id to which the nectarcrm_files to be uploaded
	 *      @param string $module  - the current module name
	*/
	function insertIntoAttachment($id,$module)
	{
		global $log, $adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/**    Function used to get the sort order for Documents listview
	*      @return string  $sorder - first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['NOTES_SORT_ORDER'] if this session value is empty then default sort order will be returned.
	*/
	function getSortOrder()
	{
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if(isset($_REQUEST['sorder']))
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else
			$sorder = (($_SESSION['NOTES_SORT_ORDER'] != '')?($_SESSION['NOTES_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	/**     Function used to get the order by value for Documents listview
	*       @return string  $order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['NOTES_ORDER_BY'] if this session value is empty then default order by will be returned.
	*/
	function getOrderBy()
	{
		global $log;
		$log->debug("Entering getOrderBy() method ...");

		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}

		if (isset($_REQUEST['order_by']))
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		else
			$order_by = (($_SESSION['NOTES_ORDER_BY'] != '')?($_SESSION['NOTES_ORDER_BY']):($use_default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}

	/**
	 * Function used to get the sort order for Documents listview
	 * @return String $sorder - sort order for a given folder.
	 */
	function getSortOrderForFolder($folderId) {
		if(isset($_REQUEST['sorder']) && $_REQUEST['folderid'] == $folderId) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} elseif(is_array($_SESSION['NOTES_FOLDER_SORT_ORDER']) &&
					!empty($_SESSION['NOTES_FOLDER_SORT_ORDER'][$folderId])) {
				$sorder = $_SESSION['NOTES_FOLDER_SORT_ORDER'][$folderId];
		} else {
			$sorder = $this->default_sort_order;
		}
		return $sorder;
	}

	/**
	 * Function used to get the order by value for Documents listview
	 * @return String order by column for a given folder.
	 */
	function getOrderByForFolder($folderId) {
		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		if (isset($_REQUEST['order_by'])  && $_REQUEST['folderid'] == $folderId) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} elseif(is_array($_SESSION['NOTES_FOLDER_ORDER_BY']) &&
				!empty($_SESSION['NOTES_FOLDER_ORDER_BY'][$folderId])) {
			$order_by = $_SESSION['NOTES_FOLDER_ORDER_BY'][$folderId];
		} else {
			$order_by = ($use_default_order_by);
		}
		return $order_by;
	}

	/** Function to export the notes in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Documents Query.
	*/
	function create_export_query($where)
	{
		global $log,$current_user;
		$log->debug("Entering create_export_query(". $where.") method ...");

		include("include/utils/ExportUtils.php");
		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Documents", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT $fields_list, case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name" .
				" FROM nectarcrm_notes
				inner join nectarcrm_crmentity
					on nectarcrm_crmentity.crmid=nectarcrm_notes.notesid
				LEFT JOIN nectarcrm_attachmentsfolder on nectarcrm_notes.folderid=nectarcrm_attachmentsfolder.folderid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid=nectarcrm_users.id " .
				" LEFT JOIN nectarcrm_groups ON nectarcrm_crmentity.smownerid=nectarcrm_groups.groupid "
				;
		$query .= getNonAdminAccessControlQuery('Documents',$current_user);
		$where_auto=" nectarcrm_crmentity.deleted=0";
		if($where != "")
			$query .= "  WHERE ($where) AND ".$where_auto;
		else
			$query .= "  WHERE ".$where_auto;

		$log->debug("Exiting create_export_query method ...");
				return $query;
	}

	function del_create_def_folder($query)
	{
		global $adb;
		$dbQuery = $query." AND nectarcrm_attachmentsfolder.folderid = 0";
		$dbresult = $adb->pquery($dbQuery,array());
		$noofnotes = $adb->num_rows($dbresult);
		if($noofnotes > 0)
		{
			$folderQuery = "select folderid from nectarcrm_attachmentsfolder";
			$folderresult = $adb->pquery($folderQuery,array());
			$noofdeffolders = $adb->num_rows($folderresult);

			if($noofdeffolders == 0)
			{
				$insertQuery = "insert into nectarcrm_attachmentsfolder values (0,'Default','Contains all attachments for which a folder is not set',1,0)";
				$insertresult = $adb->pquery($insertQuery,array());
			}
		}

	}

	function insertintonotesrel($relid,$id)
	{
		global $adb;
		$dbQuery = "insert into nectarcrm_senotesrel values ( ?, ? )";
		$dbresult = $adb->pquery($dbQuery,array($relid,$id));
	}

	/*function save_related_module($module, $crmid, $with_module, $with_crmid){
	}*/


	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	function generateReportsQuery($module,$queryplanner){
		$moduletable = $this->table_name;
		$moduleindex = $this->tab_name_index[$moduletable];
		$query = "from $moduletable
			inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=$moduletable.$moduleindex";
		if ($queryplanner->requireTable("nectarcrm_attachmentsfolder")){
			$query .= " inner join nectarcrm_attachmentsfolder on nectarcrm_attachmentsfolder.folderid=$moduletable.folderid";
		}
		if ($queryplanner->requireTable("nectarcrm_groups".$module)){
			$query .= " left join nectarcrm_groups as nectarcrm_groups".$module." on nectarcrm_groups".$module.".groupid = nectarcrm_crmentity.smownerid";
		}
		if($queryplanner->requireTable('nectarcrm_createdby'.$module)){
			$query .= " LEFT JOIN nectarcrm_users AS nectarcrm_createdby$module ON nectarcrm_createdby$module.id = nectarcrm_crmentity.smcreatorid";
		}
		if ($queryplanner->requireTable("nectarcrm_users".$module)){
			$query .= " left join nectarcrm_users as nectarcrm_users".$module." on nectarcrm_users".$module.".id = nectarcrm_crmentity.smownerid";
		}
		$query .= " left join nectarcrm_groups on nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
		$query .= " left join nectarcrm_notescf on nectarcrm_notes.notesid = nectarcrm_notescf.notesid";
		$query .= " left join nectarcrm_users on nectarcrm_users.id = nectarcrm_crmentity.smownerid";
		if ($queryplanner->requireTable("nectarcrm_lastModifiedBy".$module)){
			$query .= " left join nectarcrm_users as nectarcrm_lastModifiedBy".$module." on nectarcrm_lastModifiedBy".$module.".id = nectarcrm_crmentity.modifiedby ";
		}
		$relQuery = $this->getReportsUiType10Query($module,$queryplanner);
		$query .= ' '.$relQuery;
		return $query;

	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryplanner) {

		$matrix = $queryplanner->newDependencyMatrix();
		$matrix->setDependency("nectarcrm_crmentityDocuments",array("nectarcrm_groupsDocuments","nectarcrm_usersDocuments","nectarcrm_lastModifiedByDocuments"));

		if (!$queryplanner->requireTable('nectarcrm_notes', $matrix)) {
			return '';
		}
		$matrix->setDependency("nectarcrm_notes",array("nectarcrm_crmentityDocuments","nectarcrm_attachmentsfolder"));
		// TODO Support query planner
		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_notes","notesid", $queryplanner);
		$query .= " left join nectarcrm_notescf on nectarcrm_notes.notesid = nectarcrm_notescf.notesid";
		if ($queryplanner->requireTable("nectarcrm_crmentityDocuments",$matrix)){
			$query .=" left join nectarcrm_crmentity as nectarcrm_crmentityDocuments on nectarcrm_crmentityDocuments.crmid=nectarcrm_notes.notesid and nectarcrm_crmentityDocuments.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_attachmentsfolder")){
			$query .=" left join nectarcrm_attachmentsfolder on nectarcrm_attachmentsfolder.folderid=nectarcrm_notes.folderid";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsDocuments")){
			$query .=" left join nectarcrm_groups as nectarcrm_groupsDocuments on nectarcrm_groupsDocuments.groupid = nectarcrm_crmentityDocuments.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersDocuments")){
			$query .=" left join nectarcrm_users as nectarcrm_usersDocuments on nectarcrm_usersDocuments.id = nectarcrm_crmentityDocuments.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByDocuments")){
			$query .=" left join nectarcrm_users as nectarcrm_lastModifiedByDocuments on nectarcrm_lastModifiedByDocuments.id = nectarcrm_crmentityDocuments.modifiedby ";
		}
		if ($queryplanner->requireTable("nectarcrm_createdbyDocuments")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyDocuments on nectarcrm_createdbyDocuments.id = nectarcrm_crmentityDocuments.smcreatorid ";
		}

		//if secondary modules custom reference field is selected
        $query .= parent::getReportsUiType10Query($secmodule, $queryPlanner);
        
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array();
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		/*//Backup Documents Related Records
		$se_q = 'SELECT crmid FROM nectarcrm_senotesrel WHERE notesid = ?';
		$se_res = $this->db->pquery($se_q, array($id));
		if ($this->db->num_rows($se_res) > 0) {
			for($k=0;$k < $this->db->num_rows($se_res);$k++)
			{
				$se_id = $this->db->query_result($se_res,$k,"crmid");
				$params = array($id, RB_RECORD_DELETED, 'nectarcrm_senotesrel', 'notesid', 'crmid', $se_id);
				$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
			}
		}
		$sql = 'DELETE FROM nectarcrm_senotesrel WHERE notesid = ?';
		$this->db->pquery($sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$sql = 'DELETE FROM nectarcrm_senotesrel WHERE notesid = ? AND (crmid = ? OR crmid IN (SELECT contactid FROM nectarcrm_contactdetails WHERE accountid=?))';
			$this->db->pquery($sql, array($id, $return_id, $return_id));
		} else {
			$sql = 'DELETE FROM nectarcrm_senotesrel WHERE notesid = ? AND crmid = ?';
			$this->db->pquery($sql, array($id, $return_id));

			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}


// Function to get fieldname for uitype 27 assuming that documents have only one file type field

	function getFileTypeFieldName(){
		global $adb,$log;
		$query = 'SELECT fieldname from nectarcrm_field where tabid = ? and uitype = ?';
		$tabid = getTabid('Documents');
		$filetype_uitype = 27;
		$res = $adb->pquery($query,array($tabid,$filetype_uitype));
		$fieldname = null;
		if(isset($res)){
			$rowCount = $adb->num_rows($res);
			if($rowCount > 0){
				$fieldname = $adb->query_result($res,0,'fieldname');
			}
		}
		return $fieldname;

	}

//	Function to get fieldname for uitype 28 assuming that doc has only one file upload type

	function getFile_FieldName(){
		global $adb,$log;
		$query = 'SELECT fieldname from nectarcrm_field where tabid = ? and uitype = ?';
		$tabid = getTabid('Documents');
		$filename_uitype = 28;
		$res = $adb->pquery($query,array($tabid,$filename_uitype));
		$fieldname = null;
		if(isset($res)){
			$rowCount = $adb->num_rows($res);
			if($rowCount > 0){
				$fieldname = $adb->query_result($res,0,'fieldname');
			}
		}
		return $fieldname;
	}

	/**
	 * Check the existence of folder by folderid
	 */
	function isFolderPresent($folderid) {
		global $adb;
		$result = $adb->pquery("SELECT folderid FROM nectarcrm_attachmentsfolder WHERE folderid = ?", array($folderid));
		if(!empty($result) && $adb->num_rows($result) > 0) return true;
		return false;
	}

	/**
	 * Customizing the restore procedure.
	 */
	function restore($modulename, $id) {
		parent::restore($modulename, $id);

		global $adb;
		$fresult = $adb->pquery("SELECT folderid FROM nectarcrm_notes WHERE notesid = ?", array($id));
		if(!empty($fresult) && $adb->num_rows($fresult)) {
			$folderid = $adb->query_result($fresult, 0, 'folderid');
			if(!$this->isFolderPresent($folderid)) {
				// Re-link to default folder
				$adb->pquery("UPDATE nectarcrm_notes set folderid = 1 WHERE notesid = ?", array($id));
			}
		}
	}

	function getQueryByModuleField($module, $fieldname, $srcrecord, $query) {
		if($module == "MailManager") {
			$tempQuery = split('WHERE', $query);
			if(!empty($tempQuery[1])) {
				$where = " nectarcrm_notes.filelocationtype = 'I' AND nectarcrm_notes.filename != '' AND nectarcrm_notes.filestatus != 0 AND ";
				$query = $tempQuery[0].' WHERE '.$where.$tempQuery[1];
			} else{
				$query = $tempQuery[0].' WHERE '.$tempQuery;
			}
			return $query;
		}
	}

	/**
	 * Function to check the module active and user action permissions before showing as link in other modules
	 * like in more actions of detail view.
	 */
	static function isLinkPermitted($linkData) {
		$moduleName = "Documents";
		if(vtlib_isModuleActive($moduleName) && isPermitted($moduleName, 'CreateView') == 'yes') {
			return true;
		}
		return false;
	}

	/**
	 * Function to get query for related list in Documents module
	 */
	function get_related_list($id, $cur_tab_id, $rel_tab_id) {
		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);
		vtlib_setup_modulevars('Documents', $this);
		vtlib_setup_modulevars($related_module, $other);

		$returnset = "&return_module=Documents&return_action=CallRelatedList&return_id=".$id;

		$query = "SELECT nectarcrm_crmentity.*, $other->table_name.*";

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";
				if (empty($relmap[1]))
					$relmap[1] = $other->table_name;
				if (empty($relmap[2]))
					$relmap[2] = $relmap[0];
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}
		$query .= " FROM $other->table_name";
		$query .= " INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $other->table_name.$other->table_index";
		$query .= " INNER JOIN nectarcrm_senotesrel ON nectarcrm_senotesrel.crmid = nectarcrm_crmentity.crmid ".$more_relation;
		$query .= " LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
		$query .= " LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
		$query .= " WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_senotesrel.notesid=$id";

		//eliminate lead converted 
		if($related_module == 'Leads') {
			$query .= " AND nectarcrm_leaddetails.converted=0 ";
		}

		$return_value = GetRelatedList('Documents', $related_module, $other, $query, '', $returnset);
		return $return_value;
	}
}
?>