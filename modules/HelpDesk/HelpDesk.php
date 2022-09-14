<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of txhe License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

class HelpDesk extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "nectarcrm_troubletickets";
	var $table_index= 'ticketid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_troubletickets','nectarcrm_ticketcf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_troubletickets'=>'ticketid','nectarcrm_ticketcf'=>'ticketid','nectarcrm_ticketcomments'=>'ticketid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_ticketcf', 'ticketid');

	var $column_fields = Array();
	//Pavani: Assign value to entity_table
        var $entity_table = "nectarcrm_crmentity";

	var $sortby_fields = Array('title','status','priority','crmid','firstname','smownerid');

	var $list_fields = Array(
					//Module Sequence Numbering
					//'Ticket ID'=>Array('crmentity'=>'crmid'),
					'Ticket No'=>Array('troubletickets'=>'ticket_no'),
					// END
					'Subject'=>Array('troubletickets'=>'title'),
					'Related to'=>Array('troubletickets'=>'parent_id'),
					'Contact Name'=>Array('troubletickets'=>'contact_id'),
					'Status'=>Array('troubletickets'=>'status'),
					'Priority'=>Array('troubletickets'=>'priority'),
					'Assigned To'=>Array('crmentity','smownerid')
				);

	var $list_fields_name = Array(
					'Ticket No'=>'ticket_no',
					'Subject'=>'ticket_title',
					'Related to'=>'parent_id',
					'Contact Name' => 'contact_id',
					'Status'=>'ticketstatus',
					'Priority'=>'ticketpriorities',
					'Assigned To'=>'assigned_user_id'
				     );

	var $list_link_field= 'ticket_title';

	var $range_fields = Array(
				        'ticketid',
					'title',
			        	'firstname',
				        'lastname',
			        	'parent_id',
			        	'productid',
			        	'productname',
			        	'priority',
			        	'severity',
				        'status',
			        	'category',
					'description',
					'solution',
					'modifiedtime',
					'createdtime'
				);
	var $search_fields = Array(
		//'Ticket ID' => Array('nectarcrm_crmentity'=>'crmid'),
		'Ticket No' =>Array('nectarcrm_troubletickets'=>'ticket_no'),
		'Title' => Array('nectarcrm_troubletickets'=>'title')
		);
	var $search_fields_name = Array(
		'Ticket No' => 'ticket_no',
		'Title'=>'ticket_title',
		);
	//Specify Required fields
    var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'ticket_title', 'update_log','ticketpriorities','ticketstatus');

     //Added these variables which are used as default order by and sortorder in ListView
        var $default_order_by = 'title';
        var $default_sort_order = 'DESC';

	// For Alphabetical search
	var $def_basicsearch_col = 'ticket_title';

	//var $groupTable = Array('nectarcrm_ticketgrouprelation','ticketid');

	/**	Constructor which will set the column_fields in this object
	 */
	function HelpDesk()
	{
		$this->log =LoggerManager::getLogger('helpdesk');
		$this->log->debug("Entering HelpDesk() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('HelpDesk');
		$this->log->debug("Exiting HelpDesk method ...");
	}


	function save_module($module)
	{
		//Inserting into Ticket Comment Table
		$this->insertIntoTicketCommentTable("nectarcrm_ticketcomments",$module);

		//Inserting into nectarcrm_attachments
		$this->insertIntoAttachment($this->id,$module);

		//service contract update
		$return_action = $_REQUEST['return_action'];
		$for_module = $_REQUEST['return_module'];
		$for_crmid  = $_REQUEST['return_id'];
		if ($return_action && $for_module && $for_crmid) {
			if ($for_module == 'ServiceContracts') {
				$on_focus = CRMEntity::getInstance($for_module);
				$on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
			}
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmid, $otherParams = array()) {
		parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		if ($with_module == 'ServiceContracts') {
			$serviceContract = CRMEntity::getInstance("ServiceContracts");
	 		$serviceContract->updateHelpDeskRelatedTo($with_crmid,$crmid);
	 		$serviceContract->updateServiceContractState($with_crmid);
	 	}
	}

	/** Function to insert values in nectarcrm_ticketcomments  for the specified tablename and  module
  	  * @param $table_name -- table name:: Type varchar
  	  * @param $module -- module:: Type varchar
 	 */
	function insertIntoTicketCommentTable($table_name, $module)
	{
		global $log;
		$log->info("in insertIntoTicketCommentTable  ".$table_name."    module is  ".$module);
		global $adb;
		global $current_user;

		$current_time = $adb->formatDate(date('Y-m-d H:i:s'), true);
		if ($this->column_fields['from_portal'] != 1) {
			$ownertype = 'user';
			$ownerId = $current_user->id;
		} else {
			$ownertype = 'customer';
			$ownerId = $this->column_fields['parent_id'];
		}

		$comment = $this->column_fields['comments'];
		if ($comment != '') {
			$sql = "insert into nectarcrm_ticketcomments values(?,?,?,?,?,?)";
			$params = array('', $this->id, from_html($comment), $ownerId, $ownertype, $current_time);
			$adb->pquery($sql, $params);
		}
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
		
		if(count($_FILES)) {
			foreach($_FILES as $fileindex => $files)
			{
				if($files['name'] != '' && $files['size'] > 0)
				{
					$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
					$file_saved = $this->uploadAndSaveFile($id,$module,$files);
				}
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/** Function to form the query to get the list of activities
     *  @param  int $id - ticket id
	 *	@return array - return an array which will be returned from the function GetRelatedList
     **/
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
				if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name," .
					" nectarcrm_activity.*, nectarcrm_cntactivityrel.contactid, nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname," .
					" nectarcrm_crmentity.crmid, nectarcrm_recurringevents.recurringtype, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime," .
					" nectarcrm_seactivityrel.crmid as parent_id " .
					" from nectarcrm_activity inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid=nectarcrm_activity.activityid" .
					" inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid" .
					" left join nectarcrm_cntactivityrel on nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid " .
					" left join nectarcrm_contactdetails on nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid" .
					" left outer join nectarcrm_recurringevents on nectarcrm_recurringevents.activityid=nectarcrm_activity.activityid" .
					" left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid" .
					" left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid" .
					" where nectarcrm_seactivityrel.crmid=".$id." and nectarcrm_crmentity.deleted=0 and (activitytype NOT IN ('Emails'))" .
							" AND ( nectarcrm_activity.status is NULL OR nectarcrm_activity.status != 'Completed' )" .
							" and ( nectarcrm_activity.eventstatus is NULL OR nectarcrm_activity.eventstatus != 'Held') ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**     Function to get the Ticket History information as in array format
	 *	@param int $ticketid - ticket id
	 *	@return array - return an array with title and the ticket history informations in the following format
							array(
								header=>array('0'=>'title'),
								entries=>array('0'=>'info1','1'=>'info2',etc.,)
							     )
	 */
	function get_ticket_history($ticketid)
	{
		global $log, $adb;
		$log->debug("Entering into get_ticket_history($ticketid) method ...");

		$query="select title,update_log from nectarcrm_troubletickets where ticketid=?";
		$result=$adb->pquery($query, array($ticketid));
		$update_log = $adb->query_result($result,0,"update_log");

		$splitval = split('--//--',trim($update_log,'--//--'));

		$header[] = $adb->query_result($result,0,"title");

		$return_value = Array('header'=>$header,'entries'=>$splitval);

		$log->debug("Exiting from get_ticket_history($ticketid) method ...");

		return $return_value;
	}

	/**	Function to process the list query and return the result with number of rows
	 *	@param  string $query - query
	 *	@return array  $response - array(	list           => array(
											$i => array(key => val)
									       ),
							row_count      => '',
							next_offset    => '',
							previous_offset	=>''
						)
		where $i=0,1,..n & key = ticketid, title, firstname, ..etc(range_fields) & val = value of the key from db retrieved row
	**/
	function process_list_query($query, $row_offset, $limit = -1, $max_per_page = -1) {
		global $log;
		$log->debug("Entering process_list_query(".$query.") method ...");

   		$result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
		$list = Array();
	        $rows_found =  $this->db->getRowCount($result);
        	if($rows_found != 0)
	        {
			$ticket = Array();
			for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
			{
		                foreach($this->range_fields as $columnName)
                		{
		                	if (isset($row[$columnName]))
					{
			                	$ticket[$columnName] = $row[$columnName];
                    			}
		                       	else
				        {
		                        	$ticket[$columnName] = "";
			                }
	     			}
    		                $list[] = $ticket;
                	}
        	}

		$response = Array();
	        $response['list'] = $list;
        	$response['row_count'] = $rows_found;
	        $response['next_offset'] = $next_offset;
        	$response['previous_offset'] = $previous_offset;

		$log->debug("Exiting process_list_query method ...");
	        return $response;
	}

	/**	Function to get the HelpDesk field labels in caps letters without space
	 *	@return array $mergeflds - array(	key => val	)    where   key=0,1,2..n & val = ASSIGNEDTO,RELATEDTO, .,etc
	**/
	function getColumnNames_Hd()
	{
		global $log,$current_user;
		$log->debug("Entering getColumnNames_Hd() method ...");
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql1 = "select fieldlabel from nectarcrm_field where tabid=13 and block <> 30 and nectarcrm_field.uitype <> '61' and nectarcrm_field.presence in (0,2)";
			$params1 = array();
		}else
		{
			$profileList = getCurrentUserProfileList();
			$sql1 = "select nectarcrm_field.fieldid,fieldlabel from nectarcrm_field inner join nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=13 and nectarcrm_field.block <> 30 and nectarcrm_field.uitype <> '61' and nectarcrm_field.displaytype in (1,2,3,4) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .")  group by fieldid";
				array_push($params1, $profileList);
			}
		}
		$result = $this->db->pquery($sql1, $params1);
		$numRows = $this->db->num_rows($result);
		for($i=0; $i < $numRows;$i++)
		{
			$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
			$custom_fields[$i] = preg_replace("/\s+/","",$custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug("Exiting getColumnNames_Hd method ...");
		return $mergeflds;
	}

	/**     Function to get the Customer Name who has made comment to the ticket from the customer portal
	 *      @param  int    $id   - Ticket id
	 *      @return string $customername - The contact name
	**/
	function getCustomerName($id)
	{
		global $log;
		$log->debug("Entering getCustomerName(".$id.") method ...");
        	global $adb;
	        $sql = "select * from nectarcrm_portalinfo inner join nectarcrm_troubletickets on nectarcrm_troubletickets.contact_id = nectarcrm_portalinfo.id where nectarcrm_troubletickets.ticketid=?";
        	$result = $adb->pquery($sql, array($id));
	        $customername = $adb->query_result($result,0,'user_name');
		$log->debug("Exiting getCustomerName method ...");
        	return $customername;
	}
	// Function to create, export query for helpdesk module
        /** Function to export the ticket records in CSV Format
        * @param reference variable - where condition is passed when the query is executed
        * Returns Export Tickets Query.
        */
        function create_export_query($where)
        {
                global $log;
                global $current_user;
                $log->debug("Entering create_export_query(".$where.") method ...");

                include("include/utils/ExportUtils.php");

                //To get the Permitted fields query and the permitted fields list
                $sql = getPermittedFieldsQuery("HelpDesk", "detail_view");
                $fields_list = getFieldsListFromQuery($sql);
				//Ticket changes--5198
				$fields_list = 	str_replace(",nectarcrm_ticketcomments.comments as 'Add Comment'",' ',$fields_list);


				$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
                $query = "SELECT $fields_list,case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
                       FROM ".$this->entity_table. "
				INNER JOIN nectarcrm_troubletickets
					ON nectarcrm_troubletickets.ticketid =nectarcrm_crmentity.crmid
				LEFT JOIN nectarcrm_account
					ON nectarcrm_account.accountid = nectarcrm_troubletickets.parent_id
				LEFT JOIN nectarcrm_contactdetails
					ON nectarcrm_contactdetails.contactid = nectarcrm_troubletickets.contact_id
				LEFT JOIN nectarcrm_ticketcf
					ON nectarcrm_ticketcf.ticketid=nectarcrm_troubletickets.ticketid
				LEFT JOIN nectarcrm_groups
					ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_users.id=nectarcrm_crmentity.smownerid and nectarcrm_users.status='Active'
				LEFT JOIN nectarcrm_products
					ON nectarcrm_products.productid=nectarcrm_troubletickets.product_id";
				//end
			$query .= getNonAdminAccessControlQuery('HelpDesk',$current_user);
			$where_auto=" nectarcrm_crmentity.deleted = 0 ";

			if($where != "")
				$query .= "  WHERE ($where) AND ".$where_auto;
			else
				$query .= "  WHERE ".$where_auto;

                $log->debug("Exiting create_export_query method ...");
                return $query;
        }


	/**	Function used to get the Activity History
	 *	@param	int	$id - ticket id to which we want to display the activity history
	 *	@return  array	- return an array which will be returned from the function getHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.status, nectarcrm_activity.eventstatus, nectarcrm_activity.date_start, nectarcrm_activity.due_date,nectarcrm_activity.time_start,nectarcrm_activity.time_end,nectarcrm_activity.activitytype, nectarcrm_troubletickets.ticketid, nectarcrm_troubletickets.title, nectarcrm_crmentity.modifiedtime,nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description,
case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				from nectarcrm_activity
				inner join nectarcrm_seactivityrel on nectarcrm_seactivityrel.activityid= nectarcrm_activity.activityid
				inner join nectarcrm_troubletickets on nectarcrm_troubletickets.ticketid = nectarcrm_seactivityrel.crmid
				inner join nectarcrm_crmentity on nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
                                left join nectarcrm_groups on nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				left join nectarcrm_users on nectarcrm_users.id=nectarcrm_crmentity.smownerid
				where (nectarcrm_activity.activitytype != 'Emails')
				and (nectarcrm_activity.status = 'Completed' or nectarcrm_activity.status = 'Deferred' or (nectarcrm_activity.eventstatus = 'Held' and nectarcrm_activity.eventstatus != ''))
				and nectarcrm_seactivityrel.crmid=".$id."
                                and nectarcrm_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('HelpDesk',$query,$id);
	}

	/** Function to get the update ticket history for the specified ticketid
	  * @param $id -- $ticketid:: Type Integer
	 */
	function constructUpdateLog($focus, $mode, $assigned_group_name, $assigntype)
	{
		global $adb;
		global $current_user;

		if($mode != 'edit')//this will be updated when we create new ticket
		{
			$updatelog = "Ticket created. Assigned to ";

			if(!empty($assigned_group_name) && $assigntype == 'T')
			{
				$updatelog .= " group ".(is_array($assigned_group_name)? $assigned_group_name[0] : $assigned_group_name);
			}
			elseif($focus->column_fields['assigned_user_id'] != '')
			{
				$updatelog .= " user ".getUserFullName($focus->column_fields['assigned_user_id']);
			}
			else
			{
				$updatelog .= " user ".getUserFullName($current_user->id);
			}

			$fldvalue = date("l dS F Y h:i:s A").' by '.$current_user->user_name;
			$updatelog .= " -- ".$fldvalue."--//--";
		}
		else
		{
			$ticketid = $focus->id;

			//First retrieve the existing information
			$tktresult = $adb->pquery("select * from nectarcrm_troubletickets where ticketid=?", array($ticketid));
			$crmresult = $adb->pquery("select * from nectarcrm_crmentity where crmid=?", array($ticketid));

			$updatelog = decode_html($adb->query_result($tktresult,0,"update_log"));

			$old_owner_id = $adb->query_result($crmresult,0,"smownerid");
			$old_status = $adb->query_result($tktresult,0,"status");
			$old_priority = $adb->query_result($tktresult,0,"priority");
			$old_severity = $adb->query_result($tktresult,0,"severity");
			$old_category = $adb->query_result($tktresult,0,"category");

			//Assigned to change log
			if($focus->column_fields['assigned_user_id'] != $old_owner_id)
			{
				$owner_name = getOwnerName($focus->column_fields['assigned_user_id']);
				if($assigntype == 'T')
					$updatelog .= ' Transferred to group '.$owner_name.'\.';
				else
					$updatelog .= ' Transferred to user '.decode_html($owner_name).'\.'; // Need to decode UTF characters which are migrated from versions < 5.0.4.
			}
			//Status change log
			if($old_status != $focus->column_fields['ticketstatus'] && $focus->column_fields['ticketstatus'] != '')
			{
				$updatelog .= ' Status Changed to '.$focus->column_fields['ticketstatus'].'\.';
			}
			//Priority change log
			if($old_priority != $focus->column_fields['ticketpriorities'] && $focus->column_fields['ticketpriorities'] != '')
			{
				$updatelog .= ' Priority Changed to '.$focus->column_fields['ticketpriorities'].'\.';
			}
			//Severity change log
			if($old_severity != $focus->column_fields['ticketseverities'] && $focus->column_fields['ticketseverities'] != '')
			{
				$updatelog .= ' Severity Changed to '.$focus->column_fields['ticketseverities'].'\.';
			}
			//Category change log
			if($old_category != $focus->column_fields['ticketcategories'] && $focus->column_fields['ticketcategories'] != '')
			{
				$updatelog .= ' Category Changed to '.$focus->column_fields['ticketcategories'].'\.';
			}

			$updatelog .= ' -- '.date("l dS F Y h:i:s A").' by '.$current_user->user_name.'--//--';
		}
		return $updatelog;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Activities"=>"nectarcrm_seactivityrel","Attachments"=>"nectarcrm_seattachmentsrel","Documents"=>"nectarcrm_senotesrel");

		$tbl_field_arr = Array("nectarcrm_seactivityrel"=>"activityid","nectarcrm_seattachmentsrel"=>"attachmentsid","nectarcrm_senotesrel"=>"notesid");

		$entity_tbl_field_arr = Array("nectarcrm_seactivityrel"=>"crmid","nectarcrm_seattachmentsrel"=>"crmid","nectarcrm_senotesrel"=>"crmid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
						array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule, $queryplanner) {
		$matrix = $queryplanner->newDependencyMatrix();
		$matrix->setDependency("nectarcrm_crmentityHelpDesk",array("nectarcrm_groupsHelpDesk","nectarcrm_usersHelpDesk","nectarcrm_lastModifiedByHelpDesk"));
		$matrix->setDependency("nectarcrm_crmentityRelHelpDesk",array("nectarcrm_accountRelHelpDesk","nectarcrm_contactdetailsRelHelpDesk"));

		if (!$queryplanner->requireTable('nectarcrm_troubletickets', $matrix)) {
			return '';
		}
        
        $matrix->setDependency("nectarcrm_troubletickets",array("nectarcrm_crmentityHelpDesk","nectarcrm_ticketcf","nectarcrm_crmentityRelHelpDesk","nectarcrm_productsRel"));
		
		// TODO Support query planner
		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_troubletickets","ticketid", $queryplanner);

		if ($queryplanner->requireTable("nectarcrm_crmentityHelpDesk",$matrix)){
		    $query .=" left join nectarcrm_crmentity as nectarcrm_crmentityHelpDesk on nectarcrm_crmentityHelpDesk.crmid=nectarcrm_troubletickets.ticketid and nectarcrm_crmentityHelpDesk.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_ticketcf")){
		    $query .=" left join nectarcrm_ticketcf on nectarcrm_ticketcf.ticketid = nectarcrm_troubletickets.ticketid";
		}
		if ($queryplanner->requireTable("nectarcrm_crmentityRelHelpDesk",$matrix)){
		    $query .=" left join nectarcrm_crmentity as nectarcrm_crmentityRelHelpDesk on nectarcrm_crmentityRelHelpDesk.crmid = nectarcrm_troubletickets.parent_id";
		}
		if ($queryplanner->requireTable("nectarcrm_accountRelHelpDesk")){
		    $query .=" left join nectarcrm_account as nectarcrm_accountRelHelpDesk on nectarcrm_accountRelHelpDesk.accountid=nectarcrm_crmentityRelHelpDesk.crmid";
		}
		if ($queryplanner->requireTable("nectarcrm_contactdetailsRelHelpDesk")){
		    $query .=" left join nectarcrm_contactdetails as nectarcrm_contactdetailsRelHelpDesk on nectarcrm_contactdetailsRelHelpDesk.contactid= nectarcrm_troubletickets.contact_id";
		}
		if ($queryplanner->requireTable("nectarcrm_productsRel")){
		    $query .=" left join nectarcrm_products as nectarcrm_productsRel on nectarcrm_productsRel.productid = nectarcrm_troubletickets.product_id";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsHelpDesk")){
		    $query .=" left join nectarcrm_groups as nectarcrm_groupsHelpDesk on nectarcrm_groupsHelpDesk.groupid = nectarcrm_crmentityHelpDesk.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersHelpDesk")){
		    $query .=" left join nectarcrm_users as nectarcrm_usersHelpDesk on nectarcrm_usersHelpDesk.id = nectarcrm_crmentityHelpDesk.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByHelpDesk")){
		    $query .=" left join nectarcrm_users as nectarcrm_lastModifiedByHelpDesk on nectarcrm_lastModifiedByHelpDesk.id = nectarcrm_crmentityHelpDesk.modifiedby ";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyHelpDesk")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyHelpDesk on nectarcrm_createdbyHelpDesk.id = nectarcrm_crmentityHelpDesk.smcreatorid ";
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
		$rel_tables = array (
			"Calendar" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_troubletickets"=>"ticketid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_troubletickets"=>"ticketid"),
			"Products" => array("nectarcrm_troubletickets"=>array("ticketid","product_id")),
			"Services" => array("nectarcrm_crmentityrel"=>array("crmid","relcrmid"),"nectarcrm_troubletickets"=>"ticketid"),
            "Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_troubletickets"=>"ticketid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$sql = 'UPDATE nectarcrm_troubletickets SET parent_id=? WHERE ticketid=?';
			$this->db->pquery($sql, array(null, $id));
			$se_sql= 'DELETE FROM nectarcrm_seticketsrel WHERE ticketid=?';
			$this->db->pquery($se_sql, array($id));
		} elseif($return_module == 'Contacts') {
			$sql = 'UPDATE nectarcrm_troubletickets SET contact_id=? WHERE ticketid=?';
			$this->db->pquery($sql, array(null, $id));
			$se_sql= 'DELETE FROM nectarcrm_seticketsrel WHERE ticketid=?';
			$this->db->pquery($se_sql, array($id));
		}elseif($return_module == 'Products') {
			$sql = 'UPDATE nectarcrm_troubletickets SET product_id=? WHERE ticketid=?';
			$this->db->pquery($sql, array(null, $id));
		} elseif($return_module == 'Documents') {
            $sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public static function getTicketEmailContents($entityData, $toOwner=false) {
		global $HELPDESK_SUPPORT_NAME;
		$adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();
		$wsId = $entityData->getId();

		if (strpos($wsId,'x')) {
			$parts = explode('x', $wsId);
			$entityId = $parts[1];
		} else {
			$entityId = $wsId;
		}

		$isNew = $entityData->isNew();

		if (!$isNew) {
			$reply = getTranslatedString("replied", $moduleName);
			$temp = getTranslatedString("Re", $moduleName);
		} else {
			$reply = getTranslatedString("created", $moduleName);
			$temp = " ";
		}


		$wsParentId = $entityData->get('contact_id');
		$parentIdParts = explode('x', $wsParentId);

		// If this function is being triggered as part of Eventing API
		// Then the reference field ID will not matching the webservice format.
		// Regardless of the entry we need just the ID
		$parentId = array_pop($parentIdParts);

		$desc = getTranslatedString('Ticket ID', $moduleName) . ' : ' . $entityId . '<br>'
				. getTranslatedString('Ticket Title', $moduleName) . ' : ' . $temp . ' '
				. $entityData->get('ticket_title');
		$name = (!$toOwner)?getParentName($parentId):'';
		$desc .= "<br><br>" . getTranslatedString('Hi', $moduleName) . " " . $name . ",<br><br>"
				. getTranslatedString('LBL_PORTAL_BODY_MAILINFO', $moduleName) . " " . $reply . " " . getTranslatedString('LBL_DETAIL', $moduleName) . "<br>";
		$desc .= "<br>" . getTranslatedString('Ticket No', $moduleName) . " : " . $entityData->get('ticket_no');
		$desc .= "<br>" . getTranslatedString('Status', $moduleName) . " : " . $entityData->get('ticketstatus');
		$desc .= "<br>" . getTranslatedString('Category', $moduleName) . " : " . $entityData->get('ticketcategories');
		$desc .= "<br>" . getTranslatedString('Severity', $moduleName) . " : " . $entityData->get('ticketseverities');
		$desc .= "<br>" . getTranslatedString('Priority', $moduleName) . " : " . $entityData->get('ticketpriorities');
		$desc .= "<br><br>" . getTranslatedString('Description', $moduleName) . " : <br>" . $entityData->get('description');
		$desc .= "<br><br>" . getTranslatedString('Solution', $moduleName) . " : <br>" . $entityData->get('solution');
		$desc .= getTicketComments($entityId);

		$sql = "SELECT * FROM nectarcrm_ticketcf WHERE ticketid = ?";
		$result = $adb->pquery($sql, array($entityId));
		$cffields = $adb->getFieldsArray($result);
		foreach ($cffields as $cfOneField) {
			if ($cfOneField != 'ticketid' && $cfOneField != 'from_portal') {
				$cfData = $adb->query_result($result, 0, $cfOneField);
				$sql = "SELECT fieldlabel FROM nectarcrm_field WHERE columnname = ? and nectarcrm_field.presence in (0,2)";
				$cfLabel = $adb->query_result($adb->pquery($sql, array($cfOneField)), 0, 'fieldlabel');
				$desc .= '<br>' . $cfLabel . ' : ' . $cfData;
			}
		}
		$desc .= '<br><br>' . getTranslatedString("LBL_REGARDS", $moduleName) . ',<br>' . $HELPDESK_SUPPORT_NAME ;
		return $desc;
	}

	public static function getPortalTicketEmailContents($entityData) {
		require_once 'config.inc.php';
		global $PORTAL_URL, $HELPDESK_SUPPORT_NAME;

		$moduleName = $entityData->getModuleName();
		$wsId = $entityData->getId();

		if(strpos($wsId,'x')){
			$parts = explode('x', $wsId);
			$entityId = $parts[1];
		} else{
			$entityId = $wsId;
		}
		$wsParentId = $entityData->get('contact_id');
		$parentIdParts = explode('x', $wsParentId);

		// If this function is being triggered as part of Eventing API
		// Then the reference field ID will not matching the webservice format.
		// Regardless of the entry we need just the ID
		$parentId = array_pop($parentIdParts);

		$portalUrl = "<a href='" . $PORTAL_URL . "/index.php?module=HelpDesk&action=index&ticketid=" . $entityId . "&fun=detail'>"
				. getTranslatedString('LBL_TICKET_DETAILS', $moduleName) . "</a>";
		$contents = getTranslatedString('Dear', $moduleName).' ';
		$contents .= ($parentId) ? getParentName($parentId) : '';
		$contents .= ",<br>";
		$contents .= getTranslatedString('reply', $moduleName) . ' <b>' . $entityData->get('ticket_title')
				. '</b> ' . getTranslatedString('customer_portal', $moduleName);
		$contents .= getTranslatedString("link", $moduleName) . '<br>';
		$contents .= $portalUrl;
		$contents .= '<br><br>' . getTranslatedString("Thanks", $moduleName) . '<br>' . $HELPDESK_SUPPORT_NAME;
		return $contents;
	}

	function clearSingletonSaveFields() {
		$this->column_fields['comments'] = '';
	}
    
    function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $currentModule;
        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);

        $returnset = '&return_module='.$currentModule.'&return_action=CallRelatedList&return_id='.$id;

		$button = '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT CASE WHEN (nectarcrm_users.user_name NOT LIKE '') THEN $userNameSql ELSE nectarcrm_groups.groupname END AS user_name,
                nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime,
                nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start, nectarcrm_activity.time_start,
                nectarcrm_seactivityrel.crmid as parent_id FROM nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_troubletickets, nectarcrm_users,
                nectarcrm_crmentity LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid WHERE 
                nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid AND 
                nectarcrm_troubletickets.ticketid = nectarcrm_seactivityrel.crmid AND nectarcrm_users.id = nectarcrm_crmentity.smownerid
                AND nectarcrm_crmentity.crmid = nectarcrm_activity.activityid  AND nectarcrm_troubletickets.ticketid = $id AND
                nectarcrm_activity.activitytype = 'Emails' AND nectarcrm_crmentity.deleted = 0";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}
}

?>