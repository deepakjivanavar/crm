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
class Campaigns extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "nectarcrm_campaign";
	var $table_index= 'campaignid';

	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_campaign','nectarcrm_campaignscf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_campaign'=>'campaignid','nectarcrm_campaignscf'=>'campaignid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_campaignscf', 'campaignid');
	var $column_fields = Array();

	var $sortby_fields = Array('campaignname','smownerid','campaigntype','productname','expectedrevenue','closingdate','campaignstatus','expectedresponse','targetaudience','expectedcost');

	var $list_fields = Array(
					'Campaign Name'=>Array('campaign'=>'campaignname'),
					'Campaign Type'=>Array('campaign'=>'campaigntype'),
					'Campaign Status'=>Array('campaign'=>'campaignstatus'),
					'Expected Revenue'=>Array('campaign'=>'expectedrevenue'),
					'Expected Close Date'=>Array('campaign'=>'closingdate'),
					'Assigned To' => Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
					'Campaign Name'=>'campaignname',
					'Campaign Type'=>'campaigntype',
					'Campaign Status'=>'campaignstatus',
					'Expected Revenue'=>'expectedrevenue',
					'Expected Close Date'=>'closingdate',
					'Assigned To'=>'assigned_user_id'
				     );

	var $list_link_field= 'campaignname';
	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'DESC';

	//var $groupTable = Array('nectarcrm_campaigngrouprelation','campaignid');

	var $search_fields = Array(
			'Campaign Name'=>Array('nectarcrm_campaign'=>'campaignname'),
			'Campaign Type'=>Array('nectarcrm_campaign'=>'campaigntype'),
			);

	var $search_fields_name = Array(
			'Campaign Name'=>'campaignname',
			'Campaign Type'=>'campaigntype',
			);
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('campaignname','createdtime' ,'modifiedtime','assigned_user_id');

	// For Alphabetical search
	var $def_basicsearch_col = 'campaignname';

	function Campaigns()
	{
		$this->log =LoggerManager::getLogger('campaign');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Campaigns');
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module)
	{
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/**
	 * Function to get Campaign related Accouts
	 * @param  integer   $id      - campaignid
	 * returns related Accounts record in array format
	 */
	function get_accounts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_accounts(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('nectarcrm_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = (getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Accounts
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';
		/* To get Accounts CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$ahtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$ahtml .= $customviewcombo_html;
		$ahtml .= "</select>";
		/* To get Accounts CustomView -END */

		$button .= $ahtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		if($actions)
		{
			if(is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes')
			{
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes')
			{
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_account.*,
				CASE when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_crmentity.*, nectarcrm_crmentity.modifiedtime, nectarcrm_campaignrelstatus.*, nectarcrm_accountbillads.*
				FROM nectarcrm_account
				INNER JOIN nectarcrm_campaignaccountrel ON nectarcrm_campaignaccountrel.accountid = nectarcrm_account.accountid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid
				INNER JOIN nectarcrm_accountshipads ON nectarcrm_accountshipads.accountaddressid = nectarcrm_account.accountid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid=nectarcrm_users.id
				LEFT JOIN nectarcrm_accountbillads ON nectarcrm_accountbillads.accountaddressid = nectarcrm_account.accountid
				LEFT JOIN nectarcrm_accountscf ON nectarcrm_account.accountid = nectarcrm_accountscf.accountid
				LEFT JOIN nectarcrm_campaignrelstatus ON nectarcrm_campaignrelstatus.campaignrelstatusid = nectarcrm_campaignaccountrel.campaignrelstatusid
				WHERE nectarcrm_campaignaccountrel.campaignid = ".$id." AND nectarcrm_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Accounts');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_accounts method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Contacts
	 * @param  integer   $id      - campaignid
	 * returns related Contacts record in array format
	 */
	function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_contacts(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('nectarcrm_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = (getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_contactdetails.accountid, nectarcrm_account.accountname,
				CASE when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name ,
				nectarcrm_contactdetails.contactid, nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.title,
				nectarcrm_contactdetails.department, nectarcrm_contactdetails.email, nectarcrm_contactdetails.phone, nectarcrm_crmentity.crmid,
				nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime, nectarcrm_campaignrelstatus.*
				FROM nectarcrm_contactdetails
				INNER JOIN nectarcrm_campaigncontrel ON nectarcrm_campaigncontrel.contactid = nectarcrm_contactdetails.contactid
				INNER JOIN nectarcrm_contactaddress ON nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid
				INNER JOIN nectarcrm_contactsubdetails ON nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid
				INNER JOIN nectarcrm_customerdetails ON nectarcrm_contactdetails.contactid = nectarcrm_customerdetails.customerid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
				LEFT JOIN nectarcrm_contactscf ON nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid=nectarcrm_users.id
				LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
				LEFT JOIN nectarcrm_campaignrelstatus ON nectarcrm_campaignrelstatus.campaignrelstatusid = nectarcrm_campaigncontrel.campaignrelstatusid
				WHERE nectarcrm_campaigncontrel.campaignid = ".$id." AND nectarcrm_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Contacts');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Leads
	 * @param  integer   $id      - campaignid
	 * returns related Leads record in array format
	 */
	function get_leads($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view, $currentModule;
        $log->debug("Entering get_leads(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('nectarcrm_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed  = (getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_leaddetails.*, nectarcrm_crmentity.crmid,nectarcrm_leadaddress.phone,nectarcrm_leadsubdetails.website,
				CASE when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_crmentity.smownerid, nectarcrm_campaignrelstatus.*
				FROM nectarcrm_leaddetails
				INNER JOIN nectarcrm_campaignleadrel ON nectarcrm_campaignleadrel.leadid=nectarcrm_leaddetails.leadid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_leaddetails.leadid
				INNER JOIN nectarcrm_leadsubdetails  ON nectarcrm_leadsubdetails.leadsubscriptionid = nectarcrm_leaddetails.leadid
				INNER JOIN nectarcrm_leadaddress ON nectarcrm_leadaddress.leadaddressid = nectarcrm_leadsubdetails.leadsubscriptionid
				INNER JOIN nectarcrm_leadscf ON nectarcrm_leaddetails.leadid = nectarcrm_leadscf.leadid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_campaignrelstatus ON nectarcrm_campaignrelstatus.campaignrelstatusid = nectarcrm_campaignleadrel.campaignrelstatusid
				WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_leaddetails.converted=0 AND nectarcrm_campaignleadrel.campaignid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Leads');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_leads method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Potentials
	 * @param  integer   $id      - campaignid
	 * returns related potentials record in array format
	 */
	function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_opportunities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module,$current_user->id,'campaignid', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT CASE when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
					nectarcrm_potential.related_to, nectarcrm_potential.contact_id, nectarcrm_account.accountname, nectarcrm_potential.potentialid, nectarcrm_potential.potentialname,
					nectarcrm_potential.potentialtype, nectarcrm_potential.sales_stage, nectarcrm_potential.amount, nectarcrm_potential.closingdate,
					nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid FROM nectarcrm_campaign
					INNER JOIN nectarcrm_potential ON nectarcrm_campaign.campaignid = nectarcrm_potential.campaignid
					INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid
					INNER JOIN nectarcrm_potentialscf ON nectarcrm_potential.potentialid = nectarcrm_potentialscf.potentialid
					LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
					LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
					LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_potential.related_to
					LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_potential.contact_id
					WHERE nectarcrm_campaign.campaignid = ".$id." AND nectarcrm_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Activities
	 * @param  integer   $id      - campaignid
	 * returns related activities record in array format
	 */
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
		$query = "SELECT nectarcrm_contactdetails.lastname,
			nectarcrm_contactdetails.firstname,
			nectarcrm_contactdetails.contactid,
			nectarcrm_activity.*,
			nectarcrm_seactivityrel.crmid as parent_id,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_crmentity.modifiedtime,
			CASE when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
			nectarcrm_recurringevents.recurringtype,
			CASE WHEN (nectarcrm_activity.activitytype = 'Task') THEN nectarcrm_activity.status ELSE nectarcrm_activity.eventstatus END AS status
			FROM nectarcrm_activity
			INNER JOIN nectarcrm_seactivityrel
				ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid=nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_cntactivityrel
				ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT OUTER JOIN nectarcrm_recurringevents
				ON nectarcrm_recurringevents.activityid = nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			WHERE nectarcrm_seactivityrel.crmid=".$id."
			AND nectarcrm_crmentity.deleted = 0
			AND (activitytype = 'Task'
				OR activitytype !='Emails')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;

	}
	/*
	 * Function populate the status columns' HTML
	 * @param - $related_list return value from GetRelatedList
	 * @param - $status_column index of the status column in the list.
	 * returns true on success
	 */
	function add_status_popup($related_list, $status_column = 7, $related_module)
	{
		global $adb;

		if(!$this->campaignrelstatus)
		{
			$result = $adb->query('SELECT * FROM nectarcrm_campaignrelstatus;');
			while($row = $adb->fetchByAssoc($result))
			{
				$this->campaignrelstatus[$row['campaignrelstatus']] = $row;
			}
		}
		foreach($related_list['entries'] as $key => &$entry)
		{
			$popupitemshtml = '';
			foreach($this->campaignrelstatus as $campaingrelstatus)
			{
				$camprelstatus = getTranslatedString($campaingrelstatus[campaignrelstatus],'Campaigns');
				$popupitemshtml .= "<a onmouseover=\"javascript: showBlock('campaignstatus_popup_$key')\" href=\"javascript:updateCampaignRelationStatus('$related_module', '".$this->id."', '$key', '$campaingrelstatus[campaignrelstatusid]', '".addslashes($camprelstatus)."');\">$camprelstatus</a><br />";
			}
			$popuphtml = '<div onmouseover="javascript:clearTimeout(statusPopupTimer);" onmouseout="javascript:closeStatusPopup(\'campaignstatus_popup_'.$key.'\');" style="margin-top: -14px; width: 200px;" id="campaignstatus_popup_'.$key.'" class="calAction"><div style="background-color: #FFFFFF; padding: 8px;">'.$popupitemshtml.'</div></div>';

			$entry[$status_column] = "<a href=\"javascript: showBlock('campaignstatus_popup_$key');\">[+]</a> <span id='campaignstatus_$key'>".$entry[$status_column]."</span>".$popuphtml;
		}

		return $related_list;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryplanner){
		$matrix = $queryplanner->newDependencyMatrix();
        $matrix->setDependency('nectarcrm_crmentityCampaigns',array('nectarcrm_groupsCampaigns','nectarcrm_usersCampaignss','nectarcrm_lastModifiedByCampaigns','nectarcrm_campaignscf'));
        
		if (!$queryplanner->requireTable("nectarcrm_campaign",$matrix)){
			return '';
		}

        $matrix->setDependency('nectarcrm_campaign', array('nectarcrm_crmentityCampaigns','nectarcrm_productsCampaigns'));

		$query = $this->getRelationQuery($module,$secmodule,"nectarcrm_campaign","campaignid", $queryplanner);

		if ($queryplanner->requireTable("nectarcrm_crmentityCampaigns",$matrix)){
			$query .=" left join nectarcrm_crmentity as nectarcrm_crmentityCampaigns on nectarcrm_crmentityCampaigns.crmid=nectarcrm_campaign.campaignid and nectarcrm_crmentityCampaigns.deleted=0";
		}
		if ($queryplanner->requireTable("nectarcrm_productsCampaigns")){
			$query .=" 	left join nectarcrm_products as nectarcrm_productsCampaigns on nectarcrm_campaign.product_id = nectarcrm_productsCampaigns.productid";
		}
		if ($queryplanner->requireTable("nectarcrm_campaignscf")){
			$query .=" 	left join nectarcrm_campaignscf on nectarcrm_campaignscf.campaignid = nectarcrm_crmentityCampaigns.crmid";
		}
		if ($queryplanner->requireTable("nectarcrm_groupsCampaigns")){
			$query .=" left join nectarcrm_groups as nectarcrm_groupsCampaigns on nectarcrm_groupsCampaigns.groupid = nectarcrm_crmentityCampaigns.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_usersCampaigns")){
			$query .=" left join nectarcrm_users as nectarcrm_usersCampaigns on nectarcrm_usersCampaigns.id = nectarcrm_crmentityCampaigns.smownerid";
		}
		if ($queryplanner->requireTable("nectarcrm_lastModifiedByCampaigns")){
			$query .=" left join nectarcrm_users as nectarcrm_lastModifiedByCampaigns on nectarcrm_lastModifiedByCampaigns.id = nectarcrm_crmentityCampaigns.modifiedby ";
		}
        if ($queryplanner->requireTable("nectarcrm_createdbyCampaigns")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyCampaigns on nectarcrm_createdbyCampaigns.id = nectarcrm_crmentityCampaigns.smcreatorid ";
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
			"Contacts" => array("nectarcrm_campaigncontrel"=>array("campaignid","contactid"),"nectarcrm_campaign"=>"campaignid"),
			"Leads" => array("nectarcrm_campaignleadrel"=>array("campaignid","leadid"),"nectarcrm_campaign"=>"campaignid"),
			"Accounts" => array("nectarcrm_campaignaccountrel"=>array("campaignid","accountid"),"nectarcrm_campaign"=>"campaignid"),
			"Potentials" => array("nectarcrm_potential"=>array("campaignid","potentialid"),"nectarcrm_campaign"=>"campaignid"),
			"Calendar" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_campaign"=>"campaignid"),
			"Products" => array("nectarcrm_campaign"=>array("campaignid","product_id")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Leads') {
			$sql = 'DELETE FROM nectarcrm_campaignleadrel WHERE campaignid=? AND leadid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Contacts') {
			$sql = 'DELETE FROM nectarcrm_campaigncontrel WHERE campaignid=? AND contactid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Accounts') {
			$sql = 'DELETE FROM nectarcrm_campaignaccountrel WHERE campaignid=? AND accountid=?';
			$this->db->pquery($sql, array($id, $return_id));
			$sql = 'DELETE FROM nectarcrm_campaigncontrel WHERE campaignid=? AND contactid IN (SELECT contactid FROM nectarcrm_contactdetails WHERE accountid=?)';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if ($with_module == 'Leads') {
				$checkResult = $adb->pquery('SELECT 1 FROM nectarcrm_campaignleadrel WHERE campaignid = ? AND leadid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO nectarcrm_campaignleadrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));

			} elseif($with_module == 'Contacts') {
				$checkResult = $adb->pquery('SELECT 1 FROM nectarcrm_campaigncontrel WHERE campaignid = ? AND contactid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO nectarcrm_campaigncontrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));

			} elseif($with_module == 'Accounts') {
				$checkResult = $adb->pquery('SELECT 1 FROM nectarcrm_campaignaccountrel WHERE campaignid = ? AND accountid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO nectarcrm_campaignaccountrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));

			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

}
?>
