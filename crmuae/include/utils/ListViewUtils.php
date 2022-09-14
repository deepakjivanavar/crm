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
 * $Header: /cvsroot/nectarcrmcrm/nectarcrm_crm/include/utils/ListViewUtils.php,v 1.32 2006/02/03 06:53:08 mangai Exp $
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
require_once('include/utils/UserInfoUtil.php');
require_once('include/Zend/Json.php');

/** Function to get the list query for a module
 * @param $module -- module name:: Type string
 * @param $where -- where:: Type string
 * @returns $query -- query:: Type query
 */
function getListQuery($module, $where = '') {
	global $log;
	$log->debug("Entering getListQuery(" . $module . "," . $where . ") method ...");

	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');
	$tab_id = getTabid($module);
	$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'nectarcrm_users.first_name', 'last_name' =>
				'nectarcrm_users.last_name'), 'Users');
	switch ($module) {
		Case "HelpDesk":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_troubletickets.title, nectarcrm_troubletickets.status,
			nectarcrm_troubletickets.priority, nectarcrm_troubletickets.parent_id,
			nectarcrm_contactdetails.contactid, nectarcrm_contactdetails.firstname,
			nectarcrm_contactdetails.lastname, nectarcrm_account.accountid,
			nectarcrm_account.accountname, nectarcrm_ticketcf.*, nectarcrm_troubletickets.ticket_no
			FROM nectarcrm_troubletickets
			INNER JOIN nectarcrm_ticketcf
				ON nectarcrm_ticketcf.ticketid = nectarcrm_troubletickets.ticketid
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_troubletickets.ticketid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_troubletickets.parent_id = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_troubletickets.parent_id
			LEFT JOIN nectarcrm_users
				ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
			LEFT JOIN nectarcrm_products
				ON nectarcrm_products.productid = nectarcrm_troubletickets.product_id";
			$query .= ' ' . getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;

		Case "Accounts":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_account.accountname, nectarcrm_account.email1,
			nectarcrm_account.email2, nectarcrm_account.website, nectarcrm_account.phone,
			nectarcrm_accountbillads.bill_city,
			nectarcrm_accountscf.*
			FROM nectarcrm_account
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_account.accountid
			INNER JOIN nectarcrm_accountbillads
				ON nectarcrm_account.accountid = nectarcrm_accountbillads.accountaddressid
			INNER JOIN nectarcrm_accountshipads
				ON nectarcrm_account.accountid = nectarcrm_accountshipads.accountaddressid
			INNER JOIN nectarcrm_accountscf
				ON nectarcrm_account.accountid = nectarcrm_accountscf.accountid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_account nectarcrm_account2
				ON nectarcrm_account.parentid = nectarcrm_account2.accountid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;

		Case "Potentials":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_account.accountname,
			nectarcrm_potential.related_to, nectarcrm_potential.potentialname,
			nectarcrm_potential.sales_stage, nectarcrm_potential.amount,
			nectarcrm_potential.currency, nectarcrm_potential.closingdate,
			nectarcrm_potential.typeofrevenue, nectarcrm_potential.contact_id,
			nectarcrm_potentialscf.*
			FROM nectarcrm_potential
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_potential.potentialid
			INNER JOIN nectarcrm_potentialscf
				ON nectarcrm_potentialscf.potentialid = nectarcrm_potential.potentialid
			LEFT JOIN nectarcrm_account
				ON nectarcrm_potential.related_to = nectarcrm_account.accountid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_potential.contact_id = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_campaign
				ON nectarcrm_campaign.campaignid = nectarcrm_potential.campaignid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;

		Case "Leads":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_leaddetails.firstname, nectarcrm_leaddetails.lastname,
			nectarcrm_leaddetails.company, nectarcrm_leadaddress.phone,
			nectarcrm_leadsubdetails.website, nectarcrm_leaddetails.email,
			nectarcrm_leadscf.*
			FROM nectarcrm_leaddetails
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_leadsubdetails
				ON nectarcrm_leadsubdetails.leadsubscriptionid = nectarcrm_leaddetails.leadid
			INNER JOIN nectarcrm_leadaddress
				ON nectarcrm_leadaddress.leadaddressid = nectarcrm_leadsubdetails.leadsubscriptionid
			INNER JOIN nectarcrm_leadscf
				ON nectarcrm_leaddetails.leadid = nectarcrm_leadscf.leadid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 AND nectarcrm_leaddetails.converted = 0 " . $where;
			break;
		Case "Products":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.description, nectarcrm_products.*, nectarcrm_productcf.*
			FROM nectarcrm_products
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_products.productid
			INNER JOIN nectarcrm_productcf
				ON nectarcrm_products.productid = nectarcrm_productcf.productid
			LEFT JOIN nectarcrm_vendor
				ON nectarcrm_vendor.vendorid = nectarcrm_products.vendor_id
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			if ((isset($_REQUEST["from_dashboard"]) && $_REQUEST["from_dashboard"] == true) && (isset($_REQUEST["type"]) && $_REQUEST["type"] == "dbrd"))
				$query .= " INNER JOIN nectarcrm_inventoryproductrel on nectarcrm_inventoryproductrel.productid = nectarcrm_products.productid";

			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= " WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Documents":
			$query = "SELECT case when (nectarcrm_users.user_name not like '') then $userNameSql else nectarcrm_groups.groupname end as user_name,nectarcrm_crmentity.crmid, nectarcrm_crmentity.modifiedtime,
			nectarcrm_crmentity.smownerid,nectarcrm_attachmentsfolder.*,nectarcrm_notes.*
			FROM nectarcrm_notes
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_notes.notesid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_attachmentsfolder
				ON nectarcrm_notes.folderid = nectarcrm_attachmentsfolder.folderid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Contacts":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_contactdetails.firstname, nectarcrm_contactdetails.lastname,
			nectarcrm_contactdetails.title, nectarcrm_contactdetails.accountid,
			nectarcrm_contactdetails.email, nectarcrm_contactdetails.phone,
			nectarcrm_crmentity.smownerid, nectarcrm_crmentity.crmid
			FROM nectarcrm_contactdetails
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_contactaddress
				ON nectarcrm_contactaddress.contactaddressid = nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_contactsubdetails
				ON nectarcrm_contactsubdetails.contactsubscriptionid = nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_contactscf
				ON nectarcrm_contactscf.contactid = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_contactdetails nectarcrm_contactdetails2
				ON nectarcrm_contactdetails.reportsto = nectarcrm_contactdetails2.contactid
			LEFT JOIN nectarcrm_customerdetails
				ON nectarcrm_customerdetails.customerid = nectarcrm_contactdetails.contactid";
			if ((isset($_REQUEST["from_dashboard"]) && $_REQUEST["from_dashboard"] == true) &&
					(isset($_REQUEST["type"]) && $_REQUEST["type"] == "dbrd")) {
				$query .= " INNER JOIN nectarcrm_campaigncontrel on nectarcrm_campaigncontrel.contactid = " .
						"nectarcrm_contactdetails.contactid";
			}
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Calendar":

			$query = "SELECT nectarcrm_activity.activityid as act_id,nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid, nectarcrm_crmentity.setype,
		nectarcrm_activity.*,
		nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname,
		nectarcrm_contactdetails.contactid,
		nectarcrm_account.accountid, nectarcrm_account.accountname
		FROM nectarcrm_activity
		LEFT JOIN nectarcrm_activitycf
			ON nectarcrm_activitycf.activityid = nectarcrm_activity.activityid
		LEFT JOIN nectarcrm_cntactivityrel
			ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
		LEFT JOIN nectarcrm_contactdetails
			ON nectarcrm_contactdetails.contactid = nectarcrm_cntactivityrel.contactid
		LEFT JOIN nectarcrm_seactivityrel
			ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
		LEFT OUTER JOIN nectarcrm_activity_reminder
			ON nectarcrm_activity_reminder.activity_id = nectarcrm_activity.activityid
		LEFT JOIN nectarcrm_crmentity
			ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
		LEFT JOIN nectarcrm_users
			ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
		LEFT JOIN nectarcrm_groups
			ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
		LEFT JOIN nectarcrm_users nectarcrm_users2
			ON nectarcrm_crmentity.modifiedby = nectarcrm_users2.id
		LEFT JOIN nectarcrm_groups nectarcrm_groups2
			ON nectarcrm_crmentity.modifiedby = nectarcrm_groups2.groupid
		LEFT OUTER JOIN nectarcrm_account
			ON nectarcrm_account.accountid = nectarcrm_contactdetails.accountid
		LEFT OUTER JOIN nectarcrm_leaddetails
	       		ON nectarcrm_leaddetails.leadid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_account nectarcrm_account2
	        	ON nectarcrm_account2.accountid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_potential
	       		ON nectarcrm_potential.potentialid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_troubletickets
	       		ON nectarcrm_troubletickets.ticketid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_salesorder
			ON nectarcrm_salesorder.salesorderid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_purchaseorder
			ON nectarcrm_purchaseorder.purchaseorderid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_quotes
			ON nectarcrm_quotes.quoteid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_invoice
	                ON nectarcrm_invoice.invoiceid = nectarcrm_seactivityrel.crmid
		LEFT OUTER JOIN nectarcrm_campaign
		ON nectarcrm_campaign.campaignid = nectarcrm_seactivityrel.crmid";

			//added to fix #5135
			if (isset($_REQUEST['from_homepage']) && ($_REQUEST['from_homepage'] ==
					"upcoming_activities" || $_REQUEST['from_homepage'] == "pending_activities")) {
				$query.=" LEFT OUTER JOIN nectarcrm_recurringevents
			             ON nectarcrm_recurringevents.activityid=nectarcrm_activity.activityid";
			}
			//end

			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query.=" WHERE nectarcrm_crmentity.deleted = 0 AND activitytype != 'Emails' " . $where;
			break;
		Case "Emails":
			$query = "SELECT DISTINCT nectarcrm_crmentity.crmid, nectarcrm_crmentity.smownerid,
			nectarcrm_activity.activityid, nectarcrm_activity.subject,
			nectarcrm_activity.date_start,
			nectarcrm_contactdetails.lastname, nectarcrm_contactdetails.firstname,
			nectarcrm_contactdetails.contactid
			FROM nectarcrm_activity
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_seactivityrel
				ON nectarcrm_seactivityrel.activityid = nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_contactdetails.contactid = nectarcrm_seactivityrel.crmid
			LEFT JOIN nectarcrm_cntactivityrel
				ON nectarcrm_cntactivityrel.activityid = nectarcrm_activity.activityid
				AND nectarcrm_cntactivityrel.contactid = nectarcrm_cntactivityrel.contactid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_salesmanactivityrel
				ON nectarcrm_salesmanactivityrel.activityid = nectarcrm_activity.activityid
			LEFT JOIN nectarcrm_emaildetails
				ON nectarcrm_emaildetails.emailid = nectarcrm_activity.activityid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_activity.activitytype = 'Emails'";
			$query .= "AND nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Faq":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_crmentity.createdtime, nectarcrm_crmentity.modifiedtime,
			nectarcrm_faq.*
			FROM nectarcrm_faq
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_faq.id
			LEFT JOIN nectarcrm_products
				ON nectarcrm_faq.product_id = nectarcrm_products.productid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;

		Case "Vendors":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_vendor.*
			FROM nectarcrm_vendor
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_vendor.vendorid
			INNER JOIN nectarcrm_vendorcf
				ON nectarcrm_vendor.vendorid = nectarcrm_vendorcf.vendorid
			WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "PriceBooks":
			$query = "SELECT nectarcrm_crmentity.crmid, nectarcrm_pricebook.*, nectarcrm_currency_info.currency_name
			FROM nectarcrm_pricebook
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_pricebook.pricebookid
			INNER JOIN nectarcrm_pricebookcf
				ON nectarcrm_pricebook.pricebookid = nectarcrm_pricebookcf.pricebookid
			LEFT JOIN nectarcrm_currency_info
				ON nectarcrm_pricebook.currency_id = nectarcrm_currency_info.id
			WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Quotes":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_quotes.*,
			nectarcrm_quotesbillads.*,
			nectarcrm_quotesshipads.*,
			nectarcrm_potential.potentialname,
			nectarcrm_account.accountname,
			nectarcrm_currency_info.currency_name
			FROM nectarcrm_quotes
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_quotes.quoteid
			INNER JOIN nectarcrm_quotesbillads
				ON nectarcrm_quotes.quoteid = nectarcrm_quotesbillads.quotebilladdressid
			INNER JOIN nectarcrm_quotesshipads
				ON nectarcrm_quotes.quoteid = nectarcrm_quotesshipads.quoteshipaddressid
			LEFT JOIN nectarcrm_quotescf
				ON nectarcrm_quotes.quoteid = nectarcrm_quotescf.quoteid
			LEFT JOIN nectarcrm_currency_info
				ON nectarcrm_quotes.currency_id = nectarcrm_currency_info.id
			LEFT OUTER JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_quotes.accountid
			LEFT OUTER JOIN nectarcrm_potential
				ON nectarcrm_potential.potentialid = nectarcrm_quotes.potentialid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_contactdetails.contactid = nectarcrm_quotes.contactid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users as nectarcrm_usersQuotes
			        ON nectarcrm_usersQuotes.id = nectarcrm_quotes.inventorymanager";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "PurchaseOrder":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_purchaseorder.*,
			nectarcrm_pobillads.*,
			nectarcrm_poshipads.*,
			nectarcrm_vendor.vendorname,
			nectarcrm_currency_info.currency_name
			FROM nectarcrm_purchaseorder
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_purchaseorder.purchaseorderid
			LEFT OUTER JOIN nectarcrm_vendor
				ON nectarcrm_purchaseorder.vendorid = nectarcrm_vendor.vendorid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_purchaseorder.contactid = nectarcrm_contactdetails.contactid
			INNER JOIN nectarcrm_pobillads
				ON nectarcrm_purchaseorder.purchaseorderid = nectarcrm_pobillads.pobilladdressid
			INNER JOIN nectarcrm_poshipads
				ON nectarcrm_purchaseorder.purchaseorderid = nectarcrm_poshipads.poshipaddressid
			LEFT JOIN nectarcrm_purchaseordercf
				ON nectarcrm_purchaseordercf.purchaseorderid = nectarcrm_purchaseorder.purchaseorderid
			LEFT JOIN nectarcrm_currency_info
				ON nectarcrm_purchaseorder.currency_id = nectarcrm_currency_info.id
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "SalesOrder":
			//Query modified to sort by assigned to
			$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_salesorder.*,
			nectarcrm_sobillads.*,
			nectarcrm_soshipads.*,
			nectarcrm_quotes.subject AS quotename,
			nectarcrm_account.accountname,
			nectarcrm_currency_info.currency_name
			FROM nectarcrm_salesorder
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_salesorder.salesorderid
			INNER JOIN nectarcrm_sobillads
				ON nectarcrm_salesorder.salesorderid = nectarcrm_sobillads.sobilladdressid
			INNER JOIN nectarcrm_soshipads
				ON nectarcrm_salesorder.salesorderid = nectarcrm_soshipads.soshipaddressid
			LEFT JOIN nectarcrm_salesordercf
				ON nectarcrm_salesordercf.salesorderid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_currency_info
				ON nectarcrm_salesorder.currency_id = nectarcrm_currency_info.id
			LEFT OUTER JOIN nectarcrm_quotes
				ON nectarcrm_quotes.quoteid = nectarcrm_salesorder.quoteid
			LEFT OUTER JOIN nectarcrm_account
				ON nectarcrm_account.accountid = nectarcrm_salesorder.accountid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_salesorder.contactid = nectarcrm_contactdetails.contactid
			LEFT JOIN nectarcrm_potential
				ON nectarcrm_potential.potentialid = nectarcrm_salesorder.potentialid
			LEFT JOIN nectarcrm_invoice_recurring_info
				ON nectarcrm_invoice_recurring_info.salesorderid = nectarcrm_salesorder.salesorderid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Invoice":
			//Query modified to sort by assigned to
			//query modified -Code contribute by Geoff(http://forums.nectarcrm.com/viewtopic.php?t=3376)
			$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_invoice.*,
			nectarcrm_invoicebillads.*,
			nectarcrm_invoiceshipads.*,
			nectarcrm_salesorder.subject AS salessubject,
			nectarcrm_account.accountname,
			nectarcrm_currency_info.currency_name
			FROM nectarcrm_invoice
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_invoice.invoiceid
			INNER JOIN nectarcrm_invoicebillads
				ON nectarcrm_invoice.invoiceid = nectarcrm_invoicebillads.invoicebilladdressid
			INNER JOIN nectarcrm_invoiceshipads
				ON nectarcrm_invoice.invoiceid = nectarcrm_invoiceshipads.invoiceshipaddressid
			LEFT JOIN nectarcrm_currency_info
				ON nectarcrm_invoice.currency_id = nectarcrm_currency_info.id
			LEFT OUTER JOIN nectarcrm_salesorder
				ON nectarcrm_salesorder.salesorderid = nectarcrm_invoice.salesorderid
			LEFT OUTER JOIN nectarcrm_account
			        ON nectarcrm_account.accountid = nectarcrm_invoice.accountid
			LEFT JOIN nectarcrm_contactdetails
				ON nectarcrm_contactdetails.contactid = nectarcrm_invoice.contactid
			INNER JOIN nectarcrm_invoicecf
				ON nectarcrm_invoice.invoiceid = nectarcrm_invoicecf.invoiceid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Campaigns":
			//Query modified to sort by assigned to
			//query modified -Code contribute by Geoff(http://forums.nectarcrm.com/viewtopic.php?t=3376)
			$query = "SELECT nectarcrm_crmentity.*,
			nectarcrm_campaign.*
			FROM nectarcrm_campaign
			INNER JOIN nectarcrm_crmentity
				ON nectarcrm_crmentity.crmid = nectarcrm_campaign.campaignid
			INNER JOIN nectarcrm_campaignscf
			        ON nectarcrm_campaign.campaignid = nectarcrm_campaignscf.campaignid
			LEFT JOIN nectarcrm_groups
				ON nectarcrm_groups.groupid = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_users
				ON nectarcrm_users.id = nectarcrm_crmentity.smownerid
			LEFT JOIN nectarcrm_products
				ON nectarcrm_products.productid = nectarcrm_campaign.product_id";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE nectarcrm_crmentity.deleted = 0 " . $where;
			break;
		Case "Users":
			$query = "SELECT id,user_name,first_name,last_name,email1,phone_mobile,phone_work,is_admin,status,email2,
					nectarcrm_user2role.roleid as roleid,nectarcrm_role.depth as depth
				 	FROM nectarcrm_users
				 	INNER JOIN nectarcrm_user2role ON nectarcrm_users.id = nectarcrm_user2role.userid
				 	INNER JOIN nectarcrm_role ON nectarcrm_user2role.roleid = nectarcrm_role.roleid
					WHERE deleted=0 AND status <> 'Inactive'" . $where;
			break;
		default:
			// vtlib customization: Include the module file
			$focus = CRMEntity::getInstance($module);
			$query = $focus->getListQuery($module, $where);
		// END
	}

	if ($module != 'Users') {
		$query = listQueryNonAdminChange($query, $module);
	}
	$log->debug("Exiting getListQuery method ...");
	return $query;
}

/* * This function stores the variables in session sent in list view url string.
 * Param $lv_array - list view session array
 * Param $noofrows - no of rows
 * Param $max_ent - maximum entires
 * Param $module - module name
 * Param $related - related module
 * Return type void.
 */

function setSessionVar($lv_array, $noofrows, $max_ent, $module = '', $related = '') {
	$start = '';
	if ($noofrows >= 1) {
		$lv_array['start'] = 1;
		$start = 1;
	} elseif ($related != '' && $noofrows == 0) {
		$lv_array['start'] = 1;
		$start = 1;
	} else {
		$lv_array['start'] = 0;
		$start = 0;
	}

	if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
		$lv_array['start'] = ListViewSession::getRequestStartPage();
		$start = ListViewSession::getRequestStartPage();
	} elseif ($_SESSION['rlvs'][$module][$related]['start'] != '') {

		if ($related != '') {
			$lv_array['start'] = $_SESSION['rlvs'][$module][$related]['start'];
			$start = $_SESSION['rlvs'][$module][$related]['start'];
		}
	}
	if (isset($_REQUEST['viewname']) && $_REQUEST['viewname'] != '')
		$lv_array['viewname'] = vtlib_purify($_REQUEST['viewname']);

	if ($related == '')
		$_SESSION['lvs'][$_REQUEST['module']] = $lv_array;
	else
		$_SESSION['rlvs'][$module][$related] = $lv_array;

	if ($start < ceil($noofrows / $max_ent) && $start != '') {
		$start = ceil($noofrows / $max_ent);
		if ($related == '')
			$_SESSION['lvs'][$currentModule]['start'] = $start;
	}
}

/* * Function to get the table headers for related listview
 * Param $navigation_arrray - navigation values in array
 * Param $url_qry - url string
 * Param $module - module name
 * Param $action- action file name
 * Param $viewid - view id
 * Returns an string value
 */

function getRelatedTableHeaderNavigation($navigation_array, $url_qry, $module, $related_module, $recordid) {
	global $log, $app_strings, $adb;
	$log->debug("Entering getTableHeaderNavigation(" . $navigation_array . "," . $url_qry . "," . $module . "," . $action_val . "," . $viewid . ") method ...");
	global $theme;
	$relatedTabId = getTabid($related_module);
	$tabid = getTabid($module);

	$relatedListResult = $adb->pquery('SELECT * FROM nectarcrm_relatedlists WHERE tabid=? AND
		related_tabid=?', array($tabid, $relatedTabId));
	if (empty($relatedListResult))
		return;
	$relatedListRow = $adb->fetch_row($relatedListResult);
	$header = $relatedListRow['label'];
	$actions = $relatedListRow['actions'];
	$functionName = $relatedListRow['name'];

	$urldata = "module=$module&action={$module}Ajax&file=DetailViewAjax&record={$recordid}&" .
			"ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$relatedListRow['relation_id']}" .
			"&actions={$actions}&{$url_qry}";

	$formattedHeader = str_replace(' ', '', $header);
	$target = 'tbl_' . $module . '_' . $formattedHeader;
	$imagesuffix = $module . '_' . $formattedHeader;

	$output = '<td align="right" style="padding="5px;">';
	if (($navigation_array['prev']) != 0) {
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=1\',\'' . $target . '\',\'' . $imagesuffix . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . nectarcrm_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['prev'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . nectarcrm_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
	} else {
		$output .= '<img src="' . nectarcrm_imageurl('start_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . nectarcrm_imageurl('previous_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}

	$jsHandler = "return VT_disableFormSubmit(event);";
	$output .= "<input class='small' name='pagenum' type='text' value='{$navigation_array['current']}'
		style='width: 3em;margin-right: 0.7em;' onchange=\"loadRelatedListBlock('{$urldata}&start='+this.value+'','{$target}','{$imagesuffix}');\"
		onkeypress=\"$jsHandler\">";
	$output .= "<span name='listViewCountContainerName' class='small' style='white-space: nowrap;'>";
	$computeCount = $_REQUEST['withCount'];
	if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true
			|| ((boolean) $computeCount) == true) {
		$output .= $app_strings['LBL_LIST_OF'] . ' ' . $navigation_array['verylast'];
	} else {
		$output .= "<img src='" . nectarcrm_imageurl('windowRefresh.gif', $theme) . "' alt='" . $app_strings['LBL_HOME_COUNT'] . "'
			onclick=\"loadRelatedListBlock('{$urldata}&withCount=true&start={$navigation_array['current']}','{$target}','{$imagesuffix}');\"
			align='absmiddle' name='" . $module . "_listViewCountRefreshIcon'/>
			<img name='" . $module . "_listViewCountContainerBusy' src='" . nectarcrm_imageurl('vtbusy.gif', $theme) . "' style='display: none;'
			align='absmiddle' alt='" . $app_strings['LBL_LOADING'] . "'>";
	}
	$output .= '</span>';

	if (($navigation_array['next']) != 0) {
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['next'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');"><img src="' . nectarcrm_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['verylast'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');"><img src="' . nectarcrm_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
	} else {
		$output .= '<img src="' . nectarcrm_imageurl('next_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . nectarcrm_imageurl('end_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}
	$output .= '</td>';
	$log->debug("Exiting getTableHeaderNavigation method ...");
	if ($navigation_array['first'] == '')
		return;
	else
		return $output;
}

/* Function to get the Entity Id of a given Entity Name */

function getEntityId($module, $entityName) {
	global $log, $adb;
	$log->info("in getEntityId " . $entityName);

	$query = "select fieldname,tablename,entityidfield from nectarcrm_entityname where modulename = ?";
	$result = $adb->pquery($query, array($module));
	$fieldsname = $adb->query_result($result, 0, 'fieldname');
	$tablename = $adb->query_result($result, 0, 'tablename');
	$entityidfield = $adb->query_result($result, 0, 'entityidfield');
	if (!(strpos($fieldsname, ',') === false)) {
		$fieldlists = explode(',', $fieldsname);
		$fieldsname = "trim(concat(";
		$fieldsname = $fieldsname . implode(",' ',", $fieldlists);
		$fieldsname = $fieldsname . "))";
		$entityName = trim($entityName);
	}

	if ($entityName != '') {
		$sql = "select $entityidfield from $tablename INNER JOIN nectarcrm_crmentity ON nectarcrm_crmentity.crmid = $tablename.$entityidfield " .
				" WHERE nectarcrm_crmentity.deleted = 0 and $fieldsname=?";
		$result = $adb->pquery($sql, array($entityName));
		if ($adb->num_rows($result) > 0) {
			$entityId = $adb->query_result($result, 0, $entityidfield);
		}
	}
	if (!empty($entityId))
		return $entityId;
	else
		return 0;
}

function decode_emptyspace_html($str){
	$str = str_replace("&nbsp;", "*#chr*#",$str); // (*#chr*#) used as jargan to replace it back with &nbsp;
	$str = str_replace("\xc2", "*#chr*#",$str); // Ã (for special chrtr)
	$str = decode_html($str);
	return str_replace("*#chr*#", "&nbsp;", $str);
	
}

function decode_html($str) {
	global $default_charset;
	// Direct Popup action or Ajax Popup action should be treated the same.
	if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'Popup') || (isset($_REQUEST['file']) && $_REQUEST['file'] == 'Popup'))
		return html_entity_decode($str);
	else
		return html_entity_decode($str, ENT_QUOTES, $default_charset);
}

function popup_decode_html($str) {
	global $default_charset;
	$slashes_str = popup_from_html($str);
	$slashes_str = htmlspecialchars($slashes_str, ENT_QUOTES, $default_charset);
	return decode_html(br2nl($slashes_str));
}

//function added to check the text length in the listview.
function textlength_check($field_val) {
	global $listview_max_textlength, $default_charset;
	if ($listview_max_textlength && $listview_max_textlength > 0) {
		$temp_val = preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $field_val);
		if (function_exists('mb_strlen')) {
			if (mb_strlen(decode_html($temp_val)) > $listview_max_textlength) {
				$temp_val = mb_substr(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", decode_html($field_val)), 0, $listview_max_textlength, $default_charset) . '...';
			}
		} elseif (strlen(html_entity_decode($field_val)) > $listview_max_textlength) {
			$temp_val = substr(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $field_val), 0, $listview_max_textlength) . '...';
		}
	} else {
		$temp_val = $field_val;
	}
	return $temp_val;
}

/**
 * this function accepts a modulename and a fieldname and returns the first related module for it
 * it expects the uitype of the field to be 10
 * @param string $module - the modulename
 * @param string $fieldname - the field name
 * @return string $data - the first related module
 */
function getFirstModule($module, $fieldname) {
	global $adb;
	$sql = "select fieldid, uitype from nectarcrm_field where tabid=? and fieldname=?";
	$result = $adb->pquery($sql, array(getTabid($module), $fieldname));

	if ($adb->num_rows($result) > 0) {
		$uitype = $adb->query_result($result, 0, "uitype");

		if ($uitype == 10) {
			$fieldid = $adb->query_result($result, 0, "fieldid");
			$sql = "select * from nectarcrm_fieldmodulerel where fieldid=?";
			$result = $adb->pquery($sql, array($fieldid));
			$count = $adb->num_rows($result);

			if ($count > 0) {
				$data = $adb->query_result($result, 0, "relmodule");
			}
		}
	}
	return $data;
}

function VT_getSimpleNavigationValues($start, $size, $total) {
	$prev = $start - 1;
	if ($prev < 0) {
		$prev = 0;
	}
	if ($total === null) {
		return array('start' => $start, 'first' => $start, 'current' => $start, 'end' => $start, 'end_val' => $size, 'allflag' => 'All',
			'prev' => $prev, 'next' => $start + 1, 'verylast' => 'last');
	}
	if (empty($total)) {
		$lastPage = 1;
	} else {
		$lastPage = ceil($total / $size);
	}

	$next = $start + 1;
	if ($next > $lastPage) {
		$next = 0;
	}
	return array('start' => $start, 'first' => $start, 'current' => $start, 'end' => $start, 'end_val' => $size, 'allflag' => 'All',
		'prev' => $prev, 'next' => $next, 'verylast' => $lastPage);
}

function getRecordRangeMessage($listResult, $limitStartRecord, $totalRows = '') {
	global $adb, $app_strings;
	$numRows = $adb->num_rows($listResult);
	$recordListRangeMsg = '';
	if ($numRows > 0) {
		$recordListRangeMsg = $app_strings['LBL_SHOWING'] . ' ' . $app_strings['LBL_RECORDS'] .
				' ' . ($limitStartRecord + 1) . ' - ' . ($limitStartRecord + $numRows);
		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true) {
			$recordListRangeMsg .= ' ' . $app_strings['LBL_LIST_OF'] . " $totalRows";
		}
	}
	return $recordListRangeMsg;
}

function listQueryNonAdminChange($query, $module, $scope = '') {
	$instance = CRMEntity::getInstance($module);
	return $instance->listQueryNonAdminChange($query, $scope);
}

function html_strlen($str) {
	$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	return count($chars);
}

function html_substr($str, $start, $length = NULL) {
	if ($length === 0)
		return "";
	//check if we can simply use the built-in functions
	if (strpos($str, '&') === false) { //No entities. Use built-in functions
		if ($length === NULL)
			return substr($str, $start);
		else
			return substr($str, $start, $length);
	}

	// create our array of characters and html entities
	$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
	$html_length = count($chars);
	// check if we can predict the return value and save some processing time
	if (($html_length === 0) or ($start >= $html_length) or (isset($length) and ($length <= -$html_length)))
		return "";

	//calculate start position
	if ($start >= 0) {
		$real_start = $chars[$start][1];
	} else { //start'th character from the end of string
		$start = max($start, -$html_length);
		$real_start = $chars[$html_length + $start][1];
	}
	if (!isset($length)) // no $length argument passed, return all remaining characters
		return substr($str, $real_start);
	else if ($length > 0) { // copy $length chars
		if ($start + $length >= $html_length) { // return all remaining characters
			return substr($str, $real_start);
		} else { //return $length characters
			return substr($str, $real_start, $chars[max($start, 0) + $length][1] - $real_start);
		}
	} else { //negative $length. Omit $length characters from end
		return substr($str, $real_start, $chars[$html_length + $length][1] - $real_start);
	}
}

function counterValue() {
	static $counter = 0;
	$counter = $counter + 1;
	return $counter;
}

function getUsersPasswordInfo(){
	global $adb;
	$sql = "SELECT user_name, user_hash FROM nectarcrm_users WHERE deleted=?";
	$result = $adb->pquery($sql, array(0));
	$usersList = array();
	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$userList['name'] = $adb->query_result($result, $i, "user_name");
		$userList['hash'] = $adb->query_result($result, $i, "user_hash");
		$usersList[] = $userList;
	}
	return $usersList;
}

?>
