<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/nectarcrm_crm/sugarcrm/modules/Accounts/Accounts.php,v 1.53 2005/04/28 08:06:45 rank Exp $
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
class Accounts extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "nectarcrm_account";
	var $table_index= 'accountid';
	var $tab_name = Array('nectarcrm_crmentity','nectarcrm_account','nectarcrm_accountbillads','nectarcrm_accountshipads','nectarcrm_accountscf');
	var $tab_name_index = Array('nectarcrm_crmentity'=>'crmid','nectarcrm_account'=>'accountid','nectarcrm_accountbillads'=>'accountaddressid','nectarcrm_accountshipads'=>'accountaddressid','nectarcrm_accountscf'=>'accountid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('nectarcrm_accountscf', 'accountid');
	var $entity_table = "nectarcrm_crmentity";

	var $column_fields = Array();

	var $sortby_fields = Array('accountname','bill_city','website','phone','smownerid');

	//var $groupTable = Array('nectarcrm_accountgrouprelation','accountid');

	// This is the list of nectarcrm_fields that are in the lists.
	var $list_fields = Array(
			'Account Name'=>Array('nectarcrm_account'=>'accountname'),
			'Billing City'=>Array('nectarcrm_accountbillads'=>'bill_city'),
			'Website'=>Array('nectarcrm_account'=>'website'),
			'Phone'=>Array('nectarcrm_account'=> 'phone'),
			'Assigned To'=>Array('nectarcrm_crmentity'=>'smownerid')
			);

	var $list_fields_name = Array(
			'Account Name'=>'accountname',
			'Billing City'=>'bill_city',
			'Website'=>'website',
			'Phone'=>'phone',
			'Assigned To'=>'assigned_user_id'
			);
	var $list_link_field= 'accountname';

	var $search_fields = Array(
			'Account Name'=>Array('nectarcrm_account'=>'accountname'),
			'Billing City'=>Array('nectarcrm_accountbillads'=>'bill_city'),
			'Assigned To'=>Array('nectarcrm_crmentity'=>'smownerid'),
			);

	var $search_fields_name = Array(
			'Account Name'=>'accountname',
			'Billing City'=>'bill_city',
			'Assigned To'=>'assigned_user_id',
			);
	// This is the list of nectarcrm_fields that are required
	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to nectarcrm_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'accountname');

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array('accountname','account_type','industry','annualrevenue','phone','email1','rating','website','fax');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'accountname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'accountname';

	var $related_module_table_index = array(
		'Contacts' => array('table_name' => 'nectarcrm_contactdetails', 'table_index' => 'contactid', 'rel_index' => 'accountid'),
		'Potentials' => array('table_name' => 'nectarcrm_potential', 'table_index' => 'potentialid', 'rel_index' => 'related_to'),
		'Quotes' => array('table_name' => 'nectarcrm_quotes', 'table_index' => 'quoteid', 'rel_index' => 'accountid'),
		'SalesOrder' => array('table_name' => 'nectarcrm_salesorder', 'table_index' => 'salesorderid', 'rel_index' => 'accountid'),
		'Invoice' => array('table_name' => 'nectarcrm_invoice', 'table_index' => 'invoiceid', 'rel_index' => 'accountid'),
		'HelpDesk' => array('table_name' => 'nectarcrm_troubletickets', 'table_index' => 'ticketid', 'rel_index' => 'parent_id'),
		'Products' => array('table_name' => 'nectarcrm_seproductsrel', 'table_index' => 'productid', 'rel_index' => 'crmid'),
		'Calendar' => array('table_name' => 'nectarcrm_seactivityrel', 'table_index' => 'activityid', 'rel_index' => 'crmid'),
		'Documents' => array('table_name' => 'nectarcrm_senotesrel', 'table_index' => 'notesid', 'rel_index' => 'crmid'),
		'ServiceContracts' => array('table_name' => 'nectarcrm_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),
		'Services' => array('table_name' => 'nectarcrm_crmentityrel', 'table_index' => 'crmid', 'rel_index' => 'crmid'),
		'Campaigns' => array('table_name' => 'nectarcrm_campaignaccountrel', 'table_index' => 'campaignid', 'rel_index' => 'accountid'),
		'Assets' => array('table_name' => 'nectarcrm_assets', 'table_index' => 'assetsid', 'rel_index' => 'account'),
		'Project' => array('table_name' => 'nectarcrm_project', 'table_index' => 'projectid', 'rel_index' => 'linktoaccountscontacts'),
		'PurchaseOrder' => array('table_name' => 'nectarcrm_purchaseorder', 'table_index' => 'purchaseorderid', 'rel_index' => 'accountid'),
	);

	function Accounts() {
		$this->log =LoggerManager::getLogger('account');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Accounts');
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {

	}


	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/** Returns a list of the associated Campaigns
	 * @param $id -- campaign id :: Type Integer
	 * @returns list of campaigns in array format
	 */
	function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_campaigns(".$id.") method ...");
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

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_campaign.campaignid, nectarcrm_campaign.campaignname, nectarcrm_campaign.campaigntype, nectarcrm_campaign.campaignstatus,
				nectarcrm_campaign.expectedrevenue, nectarcrm_campaign.closingdate, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
				nectarcrm_crmentity.modifiedtime
				from nectarcrm_campaign
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_campaign.campaignid
				INNER JOIN nectarcrm_campaignscf ON nectarcrm_campaignscf.campaignid = nectarcrm_campaign.campaignid
				LEFT JOIN nectarcrm_campaignaccountrel ON nectarcrm_campaignaccountrel.campaignid=nectarcrm_campaign.campaignid
				LEFT JOIN nectarcrm_campaigncontrel ON nectarcrm_campaigncontrel.campaignid=nectarcrm_campaign.campaignid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted=0 AND (nectarcrm_campaignaccountrel.accountid=$id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_campaigncontrel.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
		}

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}

	/** Returns a list of the associated contacts
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_contacts(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
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
		$query = "SELECT nectarcrm_contactdetails.*,
			nectarcrm_crmentity.crmid,
                        nectarcrm_crmentity.smownerid,
			nectarcrm_account.accountname,
			case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_contactdetails
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
			INNER JOIN nectarcrm_contactaddress ON nectarcrm_contactdetails.contactid = nectarcrm_contactaddress.contactaddressid
			INNER JOIN nectarcrm_contactsubdetails ON nectarcrm_contactdetails.contactid = nectarcrm_contactsubdetails.contactsubscriptionid
			INNER JOIN nectarcrm_customerdetails ON nectarcrm_contactdetails.contactid = nectarcrm_customerdetails.customerid
			INNER JOIN nectarcrm_contactscf ON nectarcrm_contactdetails.contactid = nectarcrm_contactscf.contactid
			LEFT JOIN nectarcrm_groups	ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_contactdetails.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/** Returns a list of the associated contacts
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_purchaseorder($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_purchaseorder(".$id.") method ...");
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
		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
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
		$query = "SELECT nectarcrm_purchaseorder.*, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
			FROM nectarcrm_purchaseorder
			INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_purchaseordercf ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_poshipads ON nectarcrm_poshipads.poshipaddressid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_pobillads ON nectarcrm_pobillads.pobilladdressid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_purchaseorder.accountid
			LEFT JOIN nectarcrm_groups	ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
			WHERE nectarcrm_crmentity.deleted = 0
			AND nectarcrm_purchaseorder.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_purchaseorder method ...");
		return $return_value;
	}

	/** Returns a list of the associated opportunities
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
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

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		// TODO: We need to add pull contacts if its linked as secondary in Potentials too.
		// These relations are captued in nectarcrm_contpotentialrel
		// Better to provide switch to turn-on / off this feature like in
		// Contacts::get_opportunities

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT nectarcrm_potential.potentialid, nectarcrm_potential.related_to, nectarcrm_potential.potentialname, nectarcrm_potential.sales_stage,nectarcrm_potential.contact_id,
				nectarcrm_potential.potentialtype, nectarcrm_potential.amount, nectarcrm_potential.closingdate, nectarcrm_potential.potentialtype, nectarcrm_account.accountname,
				case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid
				FROM nectarcrm_potential
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid
				LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_potential.related_to
				INNER JOIN nectarcrm_potentialscf ON nectarcrm_potential.potentialid = nectarcrm_potentialscf.potentialid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted = 0 AND (nectarcrm_potential.related_to = $id ";
		if(!empty($entityIds)) {
			$query .= " OR nectarcrm_potential.contact_id IN (".$entityIds.")";
		}

		$query .= ')';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}

	/** Returns a list of the associated tasks
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
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

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT nectarcrm_activity.*, nectarcrm_cntactivityrel.*, nectarcrm_seactivityrel.crmid as parent_id, nectarcrm_contactdetails.lastname,
				nectarcrm_contactdetails.firstname, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime,
				case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_recurringevents.recurringtype
				FROM nectarcrm_activity
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_seactivityrel ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_cntactivityrel ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
				LEFT OUTER JOIN nectarcrm_recurringevents ON nectarcrm_recurringevents.activityid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted = 0
				AND ((nectarcrm_activity.activitytype='Task' and nectarcrm_activity.status not in ('Completed','Deferred'))
				OR (nectarcrm_activity.activitytype not in ('Emails','Task') and  nectarcrm_activity.eventstatus not in ('','Held')))
				AND (nectarcrm_seactivityrel.crmid = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_cntactivityrel.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
        }
        // There could be more than one contact for an activity.
        $query .= ' GROUP BY nectarcrm_activity.activityid';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**
	 * Function to get Account related Task & Event which have activity type Held, Completed or Deferred.
 	 * @param  integer   $id      - accountid
 	 * returns related Task or Event record in array format
 	 */
	function get_history($id)
	{
		global $log;
                $log->debug("Entering get_history(".$id.") method ...");

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT DISTINCT(nectarcrm_activity.activityid), nectarcrm_activity.subject, nectarcrm_activity.status, nectarcrm_activity.eventstatus,
				nectarcrm_activity.activitytype, nectarcrm_activity.date_start, nectarcrm_activity.due_date, nectarcrm_activity.time_start, nectarcrm_activity.time_end,
				nectarcrm_crmentity.modifiedtime, nectarcrm_crmentity.createdtime, nectarcrm_crmentity.description,
				case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				FROM nectarcrm_activity
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_seactivityrel ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_cntactivityrel ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
				LEFT JOIN nectarcrm_contactdetails ON nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				WHERE (nectarcrm_activity.activitytype != 'Emails')
				AND (nectarcrm_activity.status = 'Completed'
					OR nectarcrm_activity.status = 'Deferred'
					OR (nectarcrm_activity.eventstatus = 'Held' AND nectarcrm_activity.eventstatus != ''))
				AND nectarcrm_crmentity.deleted = 0 AND (nectarcrm_seactivityrel.crmid = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_cntactivityrel.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
		}

		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Exiting get_history method ...");
		return getHistory('Accounts',$query,$id);
	}

	/** Returns a list of the associated emails
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user, $adb;
		$log->debug("Entering get_emails(".$id.") method ...");
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

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}

		$entityIds = array_merge(array($id), $this->getRelatedContactsIds(), $this->getRelatedPotentialIds($id), $this->getRelatedTicketIds($id));
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
			nectarcrm_activity.activityid, nectarcrm_activity.subject, nectarcrm_activity.activitytype, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_activity.date_start,nectarcrm_activity.time_start, nectarcrm_seactivityrel.crmid as parent_id
			FROM nectarcrm_activity, nectarcrm_seactivityrel, nectarcrm_account, nectarcrm_users, nectarcrm_crmentity
			LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid=nectarcrm_crmentity.smownerid
			WHERE nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
				AND nectarcrm_seactivityrel.crmid IN (".$entityIds.")
				AND nectarcrm_users.id=nectarcrm_crmentity.smownerid
				AND nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
				AND nectarcrm_activity.activitytype='Emails'
				AND nectarcrm_account.accountid = ".$id."
				AND nectarcrm_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
		return $return_value;
	}


	/**
	* Function to get Account related Quotes
	* @param  integer   $id      - accountid
	* returns related Quotes record in array format
	*/
	function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_quotes(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
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

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_crmentity.*, nectarcrm_quotes.*, nectarcrm_potential.potentialname, nectarcrm_account.accountname
				FROM nectarcrm_quotes
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_quotes.quoteid
				LEFT OUTER JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_quotes.accountid
				LEFT OUTER JOIN nectarcrm_potential ON nectarcrm_potential.potentialid = nectarcrm_quotes.potentialid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
                LEFT JOIN nectarcrm_quotescf ON nectarcrm_quotescf.quoteid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_quotesbillads ON nectarcrm_quotesbillads.quotebilladdressid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_quotesshipads ON nectarcrm_quotesshipads.quoteshipaddressid = nectarcrm_quotes.quoteid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
				WHERE nectarcrm_crmentity.deleted = 0 AND (nectarcrm_account.accountid = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_quotes.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
		}

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	}
	/**
	* Function to get Account related Invoices
	* @param  integer   $id      - accountid
	* returns related Invoices record in array format
	*/
	function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_invoices(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
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

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				nectarcrm_crmentity.*, nectarcrm_invoice.*, nectarcrm_account.accountname, nectarcrm_salesorder.subject AS salessubject
				FROM nectarcrm_invoice
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_invoice.invoiceid
				LEFT OUTER JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_invoice.accountid
				LEFT OUTER JOIN nectarcrm_salesorder ON nectarcrm_salesorder.salesorderid = nectarcrm_invoice.salesorderid
                LEFT JOIN nectarcrm_invoicecf ON nectarcrm_invoicecf.invoiceid = nectarcrm_invoice.invoiceid
				LEFT JOIN nectarcrm_invoicebillads ON nectarcrm_invoicebillads.invoicebilladdressid = nectarcrm_invoice.invoiceid
				LEFT JOIN nectarcrm_invoiceshipads ON nectarcrm_invoiceshipads.invoiceshipaddressid = nectarcrm_invoice.invoiceid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
				WHERE nectarcrm_crmentity.deleted = 0 AND (nectarcrm_invoice.accountid = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_invoice.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
		}

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_invoices method ...");
		return $return_value;
	}

	/**
	* Function to get Account related SalesOrder
	* @param  integer   $id      - accountid
	* returns related SalesOrder record in array format
	*/
	function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_salesorder(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
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

		$entityIds = $this->getRelatedContactsIds();
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT nectarcrm_crmentity.*, nectarcrm_salesorder.*, nectarcrm_quotes.subject AS quotename, nectarcrm_account.accountname,
				case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name
				FROM nectarcrm_salesorder
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_salesorder.salesorderid
				LEFT OUTER JOIN nectarcrm_quotes ON nectarcrm_quotes.quoteid = nectarcrm_salesorder.quoteid
				LEFT OUTER JOIN nectarcrm_account ON nectarcrm_account.accountid = nectarcrm_salesorder.accountid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
                LEFT JOIN nectarcrm_invoice_recurring_info ON nectarcrm_invoice_recurring_info.start_period = nectarcrm_salesorder.salesorderid
                LEFT JOIN nectarcrm_salesordercf ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_sobillads ON nectarcrm_sobillads.sobilladdressid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_soshipads ON nectarcrm_soshipads.soshipaddressid = nectarcrm_salesorder.salesorderid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
				WHERE nectarcrm_crmentity.deleted = 0 AND (nectarcrm_salesorder.accountid = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_salesorder.contactid IN (".$entityIds."))";
		} else {
			$query .= ")";
		}

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
		return $return_value;
	}
	/**
	* Function to get Account related Tickets
	* @param  integer   $id      - accountid
	* returns related Ticket record in array format
	*/
	function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_tickets(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id','readwrite') == '0') {
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

		$entityIds = $this->getRelatedContactsIds($id);
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name, nectarcrm_users.id,
				nectarcrm_troubletickets.title, nectarcrm_troubletickets.ticketid AS crmid, nectarcrm_troubletickets.status, nectarcrm_troubletickets.priority,
				nectarcrm_troubletickets.parent_id, nectarcrm_troubletickets.contact_id, nectarcrm_troubletickets.ticket_no, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.modifiedtime
				FROM nectarcrm_troubletickets
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_troubletickets.ticketid
				LEFT JOIN nectarcrm_ticketcf ON nectarcrm_troubletickets.ticketid = nectarcrm_ticketcf.ticketid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE  nectarcrm_crmentity.deleted = 0 and (nectarcrm_troubletickets.parent_id = $id";

		if(!empty ($entityIds)){
			$query .= " OR nectarcrm_troubletickets.contact_id IN (".$entityIds."))";
		} else {
			$query .= ")";
		}
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_tickets method ...");
		return $return_value;
	}
	/**
	* Function to get Account related Products
	* @param  integer   $id      - accountid
	* returns related Products record in array format
	*/
	function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_products(".$id.") method ...");
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

		if($actions) {
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

		$entityIds = $this->getRelatedContactsIds();
		array_push($entityIds, $id);
		$entityIds = implode(',', $entityIds);

		$query = "SELECT nectarcrm_products.productid, nectarcrm_products.productname, nectarcrm_products.productcode, nectarcrm_products.commissionrate,
				nectarcrm_products.qty_per_unit, nectarcrm_products.unit_price, nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid
				FROM nectarcrm_products
				INNER JOIN nectarcrm_seproductsrel ON nectarcrm_products.productid = nectarcrm_seproductsrel.productid
				and nectarcrm_seproductsrel.setype IN ('Accounts', 'Contacts')
				INNER JOIN nectarcrm_productcf ON nectarcrm_products.productid = nectarcrm_productcf.productid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
				LEFT JOIN nectarcrm_users ON nectarcrm_users.id=nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_seproductsrel.crmid IN (".$entityIds.")";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/** Function to export the account records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Accounts Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
                $log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Accounts", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list,case when (nectarcrm_users.user_name not like '') then nectarcrm_users.user_name else nectarcrm_groups.groupname end as user_name
	       			FROM ".$this->entity_table."
				INNER JOIN nectarcrm_account
					ON nectarcrm_account.accountid = nectarcrm_crmentity.crmid
				LEFT JOIN nectarcrm_accountbillads
					ON nectarcrm_accountbillads.accountaddressid = nectarcrm_account.accountid
				LEFT JOIN nectarcrm_accountshipads
					ON nectarcrm_accountshipads.accountaddressid = nectarcrm_account.accountid
				LEFT JOIN nectarcrm_accountscf
					ON nectarcrm_accountscf.accountid = nectarcrm_account.accountid
	                        LEFT JOIN nectarcrm_groups
                        	        ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_users
					ON nectarcrm_users.id = nectarcrm_crmentity.smownerid and nectarcrm_users.status = 'Active'
				LEFT JOIN nectarcrm_account nectarcrm_account2
					ON nectarcrm_account2.accountid = nectarcrm_account.parentid
				";//nectarcrm_account2 is added to get the Member of account

		$query .= $this->getNonAdminAccessControlQuery('Accounts',$current_user);
		$where_auto = " nectarcrm_crmentity.deleted = 0 ";

		if($where != "")
			$query .= " WHERE ($where) AND ".$where_auto;
		else
			$query .= " WHERE ".$where_auto;

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/** Function to get the Columnnames of the Account Record
	* Used By nectarcrmCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	function getColumnNames_Acnt()
	{
		global $log,$current_user;
		$log->debug("Entering getColumnNames_Acnt() method ...");
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql1 = "SELECT fieldlabel FROM nectarcrm_field WHERE tabid = 6 and nectarcrm_field.presence in (0,2)";
			$params1 = array();
		}else
		{
			$profileList = getCurrentUserProfileList();
			$sql1 = "select nectarcrm_field.fieldid,fieldlabel from nectarcrm_field INNER JOIN nectarcrm_profile2field on nectarcrm_profile2field.fieldid=nectarcrm_field.fieldid inner join nectarcrm_def_org_field on nectarcrm_def_org_field.fieldid=nectarcrm_field.fieldid where nectarcrm_field.tabid=6 and nectarcrm_field.displaytype in (1,2,4) and nectarcrm_profile2field.visible=0 and nectarcrm_def_org_field.visible=0 and nectarcrm_field.presence in (0,2)";
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= " and nectarcrm_profile2field.profileid in (". generateQuestionMarks($profileList) .")  group by fieldid";
			    array_push($params1,  $profileList);
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
		$log->debug("Exiting getColumnNames_Acnt method ...");
		return $mergeflds;
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

		$rel_table_arr = Array("Contacts"=>"nectarcrm_contactdetails","Potentials"=>"nectarcrm_potential","Quotes"=>"nectarcrm_quotes",
					"SalesOrder"=>"nectarcrm_salesorder","Invoice"=>"nectarcrm_invoice","Activities"=>"nectarcrm_seactivityrel",
					"Documents"=>"nectarcrm_senotesrel","Attachments"=>"nectarcrm_seattachmentsrel","HelpDesk"=>"nectarcrm_troubletickets",
					"Products"=>"nectarcrm_seproductsrel","ServiceContracts"=>"nectarcrm_servicecontracts","Campaigns"=>"nectarcrm_campaignaccountrel",
					"Assets"=>"nectarcrm_assets","Project"=>"nectarcrm_project", "Emails"=>"nectarcrm_seactivityrel");

		$tbl_field_arr = Array("nectarcrm_contactdetails"=>"contactid","nectarcrm_potential"=>"potentialid","nectarcrm_quotes"=>"quoteid",
					"nectarcrm_salesorder"=>"salesorderid","nectarcrm_invoice"=>"invoiceid","nectarcrm_seactivityrel"=>"activityid",
					"nectarcrm_senotesrel"=>"notesid","nectarcrm_seattachmentsrel"=>"attachmentsid","nectarcrm_troubletickets"=>"ticketid",
					"nectarcrm_seproductsrel"=>"productid","nectarcrm_servicecontracts"=>"servicecontractsid","nectarcrm_campaignaccountrel"=>"campaignid",
					"nectarcrm_assets"=>"assetsid","nectarcrm_project"=>"projectid","nectarcrm_seactivityrel"=>"activityid");

		$entity_tbl_field_arr = Array("nectarcrm_contactdetails"=>"accountid","nectarcrm_potential"=>"related_to","nectarcrm_quotes"=>"accountid",
					"nectarcrm_salesorder"=>"accountid","nectarcrm_invoice"=>"accountid","nectarcrm_seactivityrel"=>"crmid",
					"nectarcrm_senotesrel"=>"crmid","nectarcrm_seattachmentsrel"=>"crmid","nectarcrm_troubletickets"=>"parent_id",
					"nectarcrm_seproductsrel"=>"crmid","nectarcrm_servicecontracts"=>"sc_related_to","nectarcrm_campaignaccountrel"=>"accountid",
					"nectarcrm_assets"=>"account","nectarcrm_project"=>"linktoaccountscontacts","nectarcrm_seactivityrel"=>"crmid");

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
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables =  array (
			"Contacts" => array("nectarcrm_contactdetails"=>array("accountid","contactid"),"nectarcrm_account"=>"accountid"),
			"Potentials" => array("nectarcrm_potential"=>array("related_to","potentialid"),"nectarcrm_account"=>"accountid"),
			"Quotes" => array("nectarcrm_quotes"=>array("accountid","quoteid"),"nectarcrm_account"=>"accountid"),
			"SalesOrder" => array("nectarcrm_salesorder"=>array("accountid","salesorderid"),"nectarcrm_account"=>"accountid"),
			"Invoice" => array("nectarcrm_invoice"=>array("accountid","invoiceid"),"nectarcrm_account"=>"accountid"),
			"Calendar" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_account"=>"accountid"),
			"HelpDesk" => array("nectarcrm_troubletickets"=>array("parent_id","ticketid"),"nectarcrm_account"=>"accountid"),
			"Products" => array("nectarcrm_seproductsrel"=>array("crmid","productid"),"nectarcrm_account"=>"accountid"),
			"Documents" => array("nectarcrm_senotesrel"=>array("crmid","notesid"),"nectarcrm_account"=>"accountid"),
			"Campaigns" => array("nectarcrm_campaignaccountrel"=>array("accountid","campaignid"),"nectarcrm_account"=>"accountid"),
			"Emails" => array("nectarcrm_seactivityrel"=>array("crmid","activityid"),"nectarcrm_account"=>"accountid"),
		);
		return $rel_tables[$secmodule];
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){

		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('nectarcrm_crmentityAccounts', array('nectarcrm_groupsAccounts', 'nectarcrm_usersAccounts', 'nectarcrm_lastModifiedByAccounts'));
		$matrix->setDependency('nectarcrm_account', array('nectarcrm_crmentityAccounts',' nectarcrm_accountbillads', 'nectarcrm_accountshipads', 'nectarcrm_accountscf', 'nectarcrm_accountAccounts', 'nectarcrm_email_trackAccounts'));

		if (!$queryPlanner->requireTable('nectarcrm_account', $matrix)) {
			return '';
		}

         // Activities related to contact should linked to accounts if contact is related to that account
        if($module == "Calendar"){
            // query to get all the contacts related to Accounts
            $relContactsQuery = "SELECT contactid FROM nectarcrm_contactdetails as nectarcrm_tmpContactCalendar
                        INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_tmpContactCalendar.contactid
                        WHERE nectarcrm_tmpContactCalendar.accountid IS NOT NULL AND nectarcrm_tmpContactCalendar.accountid !=''
                        AND nectarcrm_crmentity.deleted=0";

            $query = " left join nectarcrm_cntactivityrel as nectarcrm_tmpcntactivityrel ON
                nectarcrm_activity.activityid = nectarcrm_tmpcntactivityrel.activityid AND
                nectarcrm_tmpcntactivityrel.contactid IN ($relContactsQuery)
                left join nectarcrm_contactdetails as nectarcrm_tmpcontactdetails on nectarcrm_tmpcntactivityrel.contactid = nectarcrm_tmpcontactdetails.contactid ";
        }else {
            $query = "";
        }

		$query .= $this->getRelationQuery($module,$secmodule,"nectarcrm_account","accountid", $queryPlanner);

        if($module == "Calendar"){
            $query .= " OR nectarcrm_account.accountid = nectarcrm_tmpcontactdetails.accountid " ;
        }
        // End

		if ($queryPlanner->requireTable('nectarcrm_crmentityAccounts', $matrix)) {
			$query .= " left join nectarcrm_crmentity as nectarcrm_crmentityAccounts on nectarcrm_crmentityAccounts.crmid=nectarcrm_account.accountid and nectarcrm_crmentityAccounts.deleted=0";
		}
		if ($queryPlanner->requireTable('nectarcrm_accountbillads')) {
			$query .= " left join nectarcrm_accountbillads on nectarcrm_account.accountid=nectarcrm_accountbillads.accountaddressid";
		}
		if ($queryPlanner->requireTable('nectarcrm_accountshipads')) {
			$query .= " left join nectarcrm_accountshipads on nectarcrm_account.accountid=nectarcrm_accountshipads.accountaddressid";
		}
		if ($queryPlanner->requireTable('nectarcrm_accountscf')) {
			$query .= " left join nectarcrm_accountscf on nectarcrm_account.accountid = nectarcrm_accountscf.accountid";
		}
		if ($queryPlanner->requireTable('nectarcrm_accountAccounts', $matrix)) {
			$query .= "	left join nectarcrm_account as nectarcrm_accountAccounts on nectarcrm_accountAccounts.accountid = nectarcrm_account.parentid";
		}
		if ($queryPlanner->requireTable('nectarcrm_email_track')) {
			$query .= " LEFT JOIN nectarcrm_email_track AS nectarcrm_email_trackAccounts ON nectarcrm_email_trackAccounts .crmid = nectarcrm_account.accountid";
		}
		if ($queryPlanner->requireTable('nectarcrm_groupsAccounts')) {
			$query .= "	left join nectarcrm_groups as nectarcrm_groupsAccounts on nectarcrm_groupsAccounts.groupid = nectarcrm_crmentityAccounts.smownerid";
		}
		if ($queryPlanner->requireTable('nectarcrm_usersAccounts')) {
			$query .= " left join nectarcrm_users as nectarcrm_usersAccounts on nectarcrm_usersAccounts.id = nectarcrm_crmentityAccounts.smownerid";
		}
		if ($queryPlanner->requireTable('nectarcrm_lastModifiedByAccounts')) {
            $query .= " left join nectarcrm_users as nectarcrm_lastModifiedByAccounts on nectarcrm_lastModifiedByAccounts.id = nectarcrm_crmentityAccounts.modifiedby ";
		}
        if ($queryPlanner->requireTable("nectarcrm_createdbyAccounts")){
			$query .= " left join nectarcrm_users as nectarcrm_createdbyAccounts on nectarcrm_createdbyAccounts.id = nectarcrm_crmentityAccounts.smcreatorid ";
		}
		//if secondary modules custom reference field is selected
        $query .= parent::getReportsUiType10Query($secmodule, $queryPlanner);

		return $query;
	}

	/**
	* Function to get Account hierarchy of the given Account
	* @param  integer   $id      - accountid
	* returns Account hierarchy in array format
	*/
	function getAccountHierarchy($id) {
		global $log, $adb, $current_user;
        $log->debug("Entering getAccountHierarchy(".$id.") method ...");
		require('user_privileges/user_privileges_'.$current_user->id.'.php');

		$tabname = getParentTab();
		$listview_header = Array();
		$listview_entries = array();

		foreach ($this->list_fields_name as $fieldname=>$colname) {
			if(getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
				$listview_header[] = getTranslatedString($fieldname);
			}
		}

		$accounts_list = Array();

		// Get the accounts hierarchy from the top most account in the hierarch of the current account, including the current account
		$encountered_accounts = array($id);
		$accounts_list = $this->__getParentAccounts($id, $accounts_list, $encountered_accounts);

		// Get the accounts hierarchy (list of child accounts) based on the current account
		$accounts_list = $this->__getChildAccounts($id, $accounts_list, $accounts_list[$id]['depth']);

		// Create array of all the accounts in the hierarchy
		foreach($accounts_list as $account_id => $account_info) {
			$account_info_data = array();

			$hasRecordViewAccess = (is_admin($current_user)) || (isPermitted('Accounts', 'DetailView', $account_id) == 'yes');

			foreach ($this->list_fields_name as $fieldname=>$colname) {
				// Permission to view account is restricted, avoid showing field values (except account name)
				if(!$hasRecordViewAccess && $colname != 'accountname') {
					$account_info_data[] = '';
				} else if(getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
					$data = $account_info[$colname];
					if ($colname == 'accountname') {
						if ($account_id != $id) {
							if($hasRecordViewAccess) {
								$data = '<a href="index.php?module=Accounts&action=DetailView&record='.$account_id.'&parenttab='.$tabname.'">'.$data.'</a>';
							} else {
								$data = '<i>'.$data.'</i>';
							}
						} else {
							$data = '<b>'.$data.'</b>';
						}
						// - to show the hierarchy of the Accounts
						$account_depth = str_repeat(" .. ", $account_info['depth'] * 2);
						$data = $account_depth . $data;
					} else if ($colname == 'website') {
						$data = '<a href="http://'. $data .'" target="_blank">'.$data.'</a>';
					}
					$account_info_data[] = $data;
				}
			}
			$listview_entries[$account_id] = $account_info_data;
		}

		$account_hierarchy = array('header'=>$listview_header,'entries'=>$listview_entries);
        $log->debug("Exiting getAccountHierarchy method ...");
		return $account_hierarchy;
	}

	/**
	* Function to Recursively get all the upper accounts of a given Account
	* @param  integer   $id      		- accountid
	* @param  array   $parent_accounts   - Array of all the parent accounts
	* returns All the parent accounts of the given accountid in array format
	*/
	function __getParentAccounts($id, &$parent_accounts, &$encountered_accounts) {
		global $log, $adb;
        $log->debug("Entering __getParentAccounts(".$id.",".$parent_accounts.") method ...");

		$query = "SELECT parentid FROM nectarcrm_account " .
				" INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid" .
				" WHERE nectarcrm_crmentity.deleted = 0 and nectarcrm_account.accountid = ?";
		$params = array($id);

		$res = $adb->pquery($query, $params);

		if ($adb->num_rows($res) > 0 &&
			$adb->query_result($res, 0, 'parentid') != '' && $adb->query_result($res, 0, 'parentid') != 0 &&
			!in_array($adb->query_result($res, 0, 'parentid'),$encountered_accounts)) {

			$parentid = $adb->query_result($res, 0, 'parentid');
			$encountered_accounts[] = $parentid;
			$this->__getParentAccounts($parentid,$parent_accounts,$encountered_accounts);
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_account.*, nectarcrm_accountbillads.*," .
				" CASE when (nectarcrm_users.user_name not like '') THEN $userNameSql ELSE nectarcrm_groups.groupname END as user_name " .
				" FROM nectarcrm_account" .
				" INNER JOIN nectarcrm_crmentity " .
				" ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid" .
				" INNER JOIN nectarcrm_accountbillads" .
				" ON nectarcrm_account.accountid = nectarcrm_accountbillads.accountaddressid " .
				" LEFT JOIN nectarcrm_groups" .
				" ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid" .
				" LEFT JOIN nectarcrm_users" .
				" ON nectarcrm_users.id = nectarcrm_crmentity.smownerid" .
				" WHERE nectarcrm_crmentity.deleted = 0 and nectarcrm_account.accountid = ?";
		$params = array($id);
		$res = $adb->pquery($query, $params);

		$parent_account_info = array();
		$depth = 0;
		$immediate_parentid = $adb->query_result($res, 0, 'parentid');
		if (isset($parent_accounts[$immediate_parentid])) {
			$depth = $parent_accounts[$immediate_parentid]['depth'] + 1;
		}
		$parent_account_info['depth'] = $depth;
		foreach($this->list_fields_name as $fieldname=>$columnname) {
			if ($columnname == 'assigned_user_id') {
				$parent_account_info[$columnname] = $adb->query_result($res, 0, 'user_name');
			} else {
				$parent_account_info[$columnname] = $adb->query_result($res, 0, $columnname);
			}
		}
		$parent_accounts[$id] = $parent_account_info;
        $log->debug("Exiting __getParentAccounts method ...");
		return $parent_accounts;
	}

	/**
	* Function to Recursively get all the child accounts of a given Account
	* @param  integer   $id      		- accountid
	* @param  array   $child_accounts   - Array of all the child accounts
	* @param  integer   $depth          - Depth at which the particular account has to be placed in the hierarchy
	* returns All the child accounts of the given accountid in array format
	*/
	function __getChildAccounts($id, &$child_accounts, $depth) {
		global $log, $adb;
        $log->debug("Entering __getChildAccounts(".$id.",".$child_accounts.",".$depth.") method ...");

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');
		$query = "SELECT nectarcrm_account.*, nectarcrm_accountbillads.*," .
				" CASE when (nectarcrm_users.user_name not like '') THEN $userNameSql ELSE nectarcrm_groups.groupname END as user_name " .
				" FROM nectarcrm_account" .
				" INNER JOIN nectarcrm_crmentity " .
				" ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid" .
				" INNER JOIN nectarcrm_accountbillads" .
				" ON nectarcrm_account.accountid = nectarcrm_accountbillads.accountaddressid " .
				" LEFT JOIN nectarcrm_groups" .
				" ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid" .
				" LEFT JOIN nectarcrm_users" .
				" ON nectarcrm_users.id = nectarcrm_crmentity.smownerid" .
				" WHERE nectarcrm_crmentity.deleted = 0 and parentid = ?";
		$params = array($id);
		$res = $adb->pquery($query, $params);

		$num_rows = $adb->num_rows($res);

		if ($num_rows > 0) {
			$depth = $depth + 1;
			for($i=0;$i<$num_rows;$i++) {
				$child_acc_id = $adb->query_result($res, $i, 'accountid');
				if(array_key_exists($child_acc_id,$child_accounts)) {
					continue;
				}
				$child_account_info = array();
				$child_account_info['depth'] = $depth;
				foreach($this->list_fields_name as $fieldname=>$columnname) {
					if ($columnname == 'assigned_user_id') {
						$child_account_info[$columnname] = $adb->query_result($res, $i, 'user_name');
					} else {
						$child_account_info[$columnname] = $adb->query_result($res, $i, $columnname);
					}
				}
				$child_accounts[$child_acc_id] = $child_account_info;
				$this->__getChildAccounts($child_acc_id, $child_accounts, $depth);
			}
		}
        $log->debug("Exiting __getChildAccounts method ...");
		return $child_accounts;
	}

	// Function to unlink the dependent records of the given record by id
	function unlinkDependencies($module, $id) {
		global $log;

		//Deleting Account related Potentials.
		$pot_q = 'SELECT nectarcrm_crmentity.crmid FROM nectarcrm_crmentity
			INNER JOIN nectarcrm_potential ON nectarcrm_crmentity.crmid=nectarcrm_potential.potentialid
			LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid=nectarcrm_potential.related_to
			WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_potential.related_to=?';
		$pot_res = $this->db->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for($k=0;$k < $this->db->num_rows($pot_res);$k++)
		{
			$pot_id = $this->db->query_result($pot_res,$k,"crmid");
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE nectarcrm_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($pot_id));
		}
		//Backup deleted Account related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_crmentity', 'deleted', 'crmid', implode(",", $pot_ids_list));
		$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Deleting Account related Quotes.
		$quo_q = 'SELECT nectarcrm_crmentity.crmid FROM nectarcrm_crmentity
			INNER JOIN nectarcrm_quotes ON nectarcrm_crmentity.crmid=nectarcrm_quotes.quoteid
			INNER JOIN nectarcrm_account ON nectarcrm_account.accountid=nectarcrm_quotes.accountid
			WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_quotes.accountid=?';
		$quo_res = $this->db->pquery($quo_q, array($id));
		$quo_ids_list = array();
		for($k=0;$k < $this->db->num_rows($quo_res);$k++)
		{
			$quo_id = $this->db->query_result($quo_res,$k,"crmid");
			$quo_ids_list[] = $quo_id;
			$sql = 'UPDATE nectarcrm_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($quo_id));
		}
		//Backup deleted Account related Quotes.
		$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_crmentity', 'deleted', 'crmid', implode(",", $quo_ids_list));
		$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Account Relation
		$con_q = 'SELECT contactid FROM nectarcrm_contactdetails WHERE accountid = ?';
		$con_res = $this->db->pquery($con_q, array($id));
		if ($this->db->num_rows($con_res) > 0) {
			$con_ids_list = array();
			for($k=0;$k < $this->db->num_rows($con_res);$k++)
			{
				$con_ids_list[] = $this->db->query_result($con_res,$k,"contactid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_contactdetails', 'accountid', 'contactid', implode(",", $con_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);
		}
		//Deleting Contact-Account Relation.
		$con_q = 'UPDATE nectarcrm_contactdetails SET accountid = 0 WHERE accountid = ?';
		$this->db->pquery($con_q, array($id));

		//Backup Trouble Tickets-Account Relation
		$tkt_q = 'SELECT ticketid FROM nectarcrm_troubletickets WHERE parent_id = ?';
		$tkt_res = $this->db->pquery($tkt_q, array($id));
		if ($this->db->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for($k=0;$k < $this->db->num_rows($tkt_res);$k++)
			{
				$tkt_ids_list[] = $this->db->query_result($tkt_res,$k,"ticketid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'nectarcrm_troubletickets', 'parent_id', 'ticketid', implode(",", $tkt_ids_list));
			$this->db->pquery('INSERT INTO nectarcrm_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);
		}
		//Deleting Trouble Tickets-Account Relation.
		$tt_q = 'UPDATE nectarcrm_troubletickets SET parent_id = 0 WHERE parent_id = ?';
		$this->db->pquery($tt_q, array($id));

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Campaigns') {
			$sql = 'DELETE FROM nectarcrm_campaignaccountrel WHERE accountid=? AND campaignid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else if($return_module == 'Products') {
			$sql = 'DELETE FROM nectarcrm_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Documents') {
			$sql = 'DELETE FROM nectarcrm_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array()) {
		$adb = $this->db;

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Products')
				$adb->pquery('INSERT INTO nectarcrm_seproductsrel VALUES(?,?,?,?)', array($crmid, $with_crmid, $module, 1));
			elseif($with_module == 'Campaigns') {
				$checkResult = $adb->pquery('SELECT 1 FROM nectarcrm_campaignaccountrel WHERE campaignid = ? AND accountid = ?',
												array($with_crmid, $crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$adb->pquery("insert into nectarcrm_campaignaccountrel values(?,?,1)", array($with_crmid, $crmid));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	function getListButtons($app_strings,$mod_strings = false) {
		$list_buttons = Array();

		if(isPermitted('Accounts','Delete','') == 'yes') {
			$list_buttons['del'] = $app_strings[LBL_MASS_DELETE];
		}
		if(isPermitted('Accounts','EditView','') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
			$list_buttons['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
		}
		if(isPermitted('Emails','EditView','') == 'yes') {
			$list_buttons['s_mail'] = $app_strings[LBL_SEND_MAIL_BUTTON];
		}
		// mailer export
		if(isPermitted('Accounts','Export','') == 'yes') {
			$list_buttons['mailer_exp'] = $mod_strings[LBL_MAILER_EXPORT];
		}
		// end of mailer export
		return $list_buttons;
	}

	/* Function to get attachments in the related list of accounts module */
	function get_attachments($id, $cur_tab_id, $rel_tab_id, $actions = false) {

		global $currentModule, $app_strings, $singlepane_view;
		$this_module = $currentModule;
		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = vtlib_toSingular($related_module);
		$button = '';
		if ($actions) {
			if (is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true'){
			$returnset = "&return_module=$this_module&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$this_module&return_action=CallRelatedList&return_id=$id";
		}

		$entityIds = $this->getRelatedContactsIds();
		array_push($entityIds, $id);
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,
				'Documents' ActivityType,nectarcrm_attachments.type  FileType,crm2.modifiedtime lastmodified,nectarcrm_crmentity.modifiedtime,
				nectarcrm_seattachmentsrel.attachmentsid attachmentsid, nectarcrm_notes.notesid crmid, nectarcrm_notes.notecontent description,nectarcrm_notes.*
				from nectarcrm_notes
				INNER JOIN nectarcrm_senotesrel ON nectarcrm_senotesrel.notesid= nectarcrm_notes.notesid
				LEFT JOIN nectarcrm_notescf ON nectarcrm_notescf.notesid= nectarcrm_notes.notesid
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid= nectarcrm_notes.notesid and nectarcrm_crmentity.deleted=0
				INNER JOIN nectarcrm_crmentity crm2 ON crm2.crmid=nectarcrm_senotesrel.crmid
				LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				LEFT JOIN nectarcrm_seattachmentsrel ON nectarcrm_seattachmentsrel.crmid =nectarcrm_notes.notesid
				LEFT JOIN nectarcrm_attachments ON nectarcrm_seattachmentsrel.attachmentsid = nectarcrm_attachments.attachmentsid
				LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid= nectarcrm_users.id
				WHERE crm2.crmid IN (".$entityIds.")";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		return $return_value;
	}

	/**
	 * Function to handle the merged list for the module.
	 * NOTE: UI type '10' is used to stored the references to other modules for a given record.
	 * These dependent records can be retrieved through this function.
	 * For eg: A trouble ticket can be related to an Account or a Contact.
	 * From a given Contact/Account if we need to fetch all such dependent trouble tickets, get_dependents_list function can be used.
	 */
	function get_merged_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {

		global $currentModule, $app_strings, $singlepane_view, $current_user;

		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = 'SINGLE_' . $related_module;
		$button = '';

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true')
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		else
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

		$return_value = null;
		$dependentFieldSql = $this->db->pquery("SELECT tabid, fieldname, columnname FROM nectarcrm_field WHERE uitype='10' AND" .
				" fieldid IN (SELECT fieldid FROM nectarcrm_fieldmodulerel WHERE relmodule=? AND module=?)", array($currentModule, $related_module));
		$numOfFields = $this->db->num_rows($dependentFieldSql);

		if ($numOfFields > 0) {
			$dependentColumn = $this->db->query_result($dependentFieldSql, 0, 'columnname');
			$dependentField = $this->db->query_result($dependentFieldSql, 0, 'fieldname');

			$button .= '<input type="hidden" name="' . $dependentColumn . '" id="' . $dependentColumn . '" value="' . $id . '">';
			$button .= '<input type="hidden" name="' . $dependentColumn . '_type" id="' . $dependentColumn . '_type" value="' . $currentModule . '">';
			if ($actions) {
				if (is_string($actions))
					$actions = explode(',', strtoupper($actions));
				if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes'
						&& getFieldVisibilityPermission($related_module, $current_user->id, $dependentField, 'readwrite') == '0') {
					$button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "' class='crmbutton small create'" .
							" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
							" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
				}
			}

			$entityIds = $this->getRelatedContactsIds();
			$entityIds = implode(',', $entityIds);

			$contactColumn = null;
			$contactTableName = null;
			$otherModuleModel = nectarcrm_Module_Model::getInstance($related_module);
			$referenceFields = $otherModuleModel->getFieldsByType('reference');
			foreach ($referenceFields as $referenceFieldName => $fieldModel) {
				$referenceList = $fieldModel->getReferenceList();
				foreach($referenceList as $referencedModule) {
					if($referencedModule == 'Contacts' && (!$fieldModel->isCustomField())){
						$contactColumn = $fieldModel->get('column');
						$contactTableName = $fieldModel->get('table');
					}
				}
			}

			$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name','last_name' => 'nectarcrm_users.last_name'), 'Users');

			$query = "SELECT nectarcrm_crmentity.*, $other->table_name.*";
			$query .= ", CASE WHEN (nectarcrm_users.user_name NOT LIKE '') THEN $userNameSql ELSE nectarcrm_groups.groupname END AS user_name";

			$more_relation = '';
			if (!empty($other->related_tables)) {
				foreach ($other->related_tables as $tname => $relmap) {
					$query .= ", $tname.*";

					// Setup the default JOIN conditions if not specified
					if (empty($relmap[1]))
						$relmap[1] = $other->table_name;
					if (empty($relmap[2]))
						$relmap[2] = $relmap[0];
					$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
				}
			}

			$query .= " FROM $other->table_name";
			$query .= " INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $other->table_name.$other->table_index";
			$query .= $more_relation;
			$query .= " LEFT JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= " LEFT JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid";
			$query .= " WHERE nectarcrm_crmentity.deleted = 0 AND ( ($other->table_name.$dependentColumn = $id) ";

			if(!empty($entityIds)) {
				$query .= " OR ($contactTableName.$contactColumn  IN (".$entityIds."))";
			}
			$query .= " )";
			$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
		}
		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Function to handle the related list for the module.
	 * NOTE: nectarcrm_Module::setRelatedList sets reference to this function in nectarcrm_relatedlists table
	 * if function name is not explicitly specified.
	 */
	function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {

		global $currentModule, $app_strings, $singlepane_view;

		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = 'SINGLE_' . $related_module;

		$button = '';
		if ($actions) {
			if (is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
						" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
						" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$more_relation = '';
		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";

				// Setup the default JOIN conditions if not specified
				if (empty($relmap[1]))
					$relmap[1] = $other->table_name;
				if (empty($relmap[2]))
					$relmap[2] = $relmap[0];
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}

		$entityIds = $this->getRelatedContactsIds();
		array_push($entityIds, $id);
		$entityIds = implode(',', $entityIds);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'nectarcrm_users.first_name', 'last_name' => 'nectarcrm_users.last_name'), 'Users');

		$query = "SELECT nectarcrm_crmentity.*, $other->table_name.*,
				CASE WHEN (nectarcrm_users.user_name NOT LIKE '') THEN $userNameSql ELSE nectarcrm_groups.groupname END AS user_name FROM $other->table_name
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $other->table_name.$other->table_index
				INNER JOIN nectarcrm_crmentityrel ON (nectarcrm_crmentityrel.relcrmid = nectarcrm_crmentity.crmid OR nectarcrm_crmentityrel.crmid = nectarcrm_crmentity.crmid)
				$more_relation
				LEFT  JOIN nectarcrm_users ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
				LEFT  JOIN nectarcrm_groups ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
				WHERE nectarcrm_crmentity.deleted = 0 AND (nectarcrm_crmentityrel.crmid IN (" .$entityIds. ") OR nectarcrm_crmentityrel.relcrmid IN (". $entityIds . "))";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/* Function to get related contact ids for an account record*/
	function getRelatedContactsIds($id = null) {
		global $adb;
		if($id ==null)
		$id = $this->id;
		$entityIds = array();
		$query = 'SELECT contactid FROM nectarcrm_contactdetails
				INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
				WHERE nectarcrm_contactdetails.accountid = ? AND nectarcrm_crmentity.deleted = 0';
		$accountContacts = $adb->pquery($query, array($id));
		$numOfContacts = $adb->num_rows($accountContacts);
		if($accountContacts && $numOfContacts > 0) {
			for($i=0; $i < $numOfContacts; ++$i) {
				array_push($entityIds, $adb->query_result($accountContacts, $i, 'contactid'));
			}
		}
		return $entityIds;
	}

	function getRelatedPotentialIds($id) {
		$relatedIds = array();
		$db = PearDatabase::getInstance();
		$query = "SELECT DISTINCT nectarcrm_crmentity.crmid FROM nectarcrm_potential INNER JOIN nectarcrm_crmentity ON 
					nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid LEFT JOIN nectarcrm_account ON nectarcrm_account.accountid = 
					nectarcrm_potential.related_to WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_potential.related_to = ?";
		$result = $db->pquery($query, array($id));
		for ($i = 0; $i < $db->num_rows($result); $i++) {
			$relatedIds[] = $db->query_result($result, $i, 'crmid');
		}
		return $relatedIds;
	}

	function getRelatedTicketIds($id) {
		$relatedIds = array();
		$db = PearDatabase::getInstance();
		$query = "SELECT DISTINCT nectarcrm_crmentity.crmid FROM nectarcrm_troubletickets INNER JOIN nectarcrm_crmentity ON 
					nectarcrm_crmentity.crmid = nectarcrm_troubletickets.ticketid WHERE nectarcrm_crmentity.deleted = 0 AND 
					nectarcrm_troubletickets.parent_id = ?";
		$result = $db->pquery($query, array($id));
		for ($i = 0; $i < $db->num_rows($result); $i++) {
			$relatedIds[] = $db->query_result($result, $i, 'crmid');
		}
		return $relatedIds;
	}
}

?>