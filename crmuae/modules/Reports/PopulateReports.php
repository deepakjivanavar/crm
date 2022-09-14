<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
//require_once('modules/Reports/CannedReports.php');
global $adb;

$rptfolder = Array(Array('Account and Contact Reports'=>'Account and Contact Reports'),
		   Array('Lead Reports'=>'Lead Reports'),
	           Array('Potential Reports'=>'Potential Reports'),
		   Array('Activity Reports'=>'Activity Reports'),
		   Array('HelpDesk Reports'=>'HelpDesk Reports'),
		   Array('Product Reports'=>'Product Reports'),
		   Array('Quote Reports' =>'Quote Reports'),
		   Array('PurchaseOrder Reports'=>'PurchaseOrder Reports'),
		   Array('Invoice Reports'=>'Invoice Reports'),
		   Array('SalesOrder Reports'=>'SalesOrder Reports'),
		   Array('Campaign Reports'=>'Campaign Reports')
                  );

$reportmodules = Array(Array('primarymodule'=>'Contacts','secondarymodule'=>'Accounts'),
		       Array('primarymodule'=>'Contacts','secondarymodule'=>'Accounts'),
		       Array('primarymodule'=>'Contacts','secondarymodule'=>'Potentials'),
		       Array('primarymodule'=>'Leads','secondarymodule'=>''),
		       Array('primarymodule'=>'Leads','secondarymodule'=>''),
		       Array('primarymodule'=>'Potentials','secondarymodule'=>''),
		       Array('primarymodule'=>'Potentials','secondarymodule'=>''),
		       Array('primarymodule'=>'Calendar','secondarymodule'=>''),
		       Array('primarymodule'=>'Calendar','secondarymodule'=>''),
		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>'Products'),
		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>''),
  		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>''),
		       Array('primarymodule'=>'Products','secondarymodule'=>''),
		       Array('primarymodule'=>'Products','secondarymodule'=>'Contacts'),
		       Array('primarymodule'=>'Quotes','secondarymodule'=>''),
		       Array('primarymodule'=>'Quotes','secondarymodule'=>''),
		       Array('primarymodule'=>'PurchaseOrder','secondarymodule'=>'Contacts'),
		       Array('primarymodule'=>'PurchaseOrder','secondarymodule'=>''),
		       Array('primarymodule'=>'Invoice','secondarymodule'=>''),
		       Array('primarymodule'=>'SalesOrder','secondarymodule'=>''),
		       Array('primarymodule'=>'Campaigns','secondarymodule'=>'')
		      );

$selectcolumns = Array(Array('nectarcrm_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'nectarcrm_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'nectarcrm_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
                             'nectarcrm_contactdetails:accountid:Contacts_Account_Name:account_id:V',
			     'nectarcrm_account:industry:Accounts_industry:industry:V',
			     'nectarcrm_contactdetails:email:Contacts_Email:email:E'),

		       Array('nectarcrm_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'nectarcrm_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'nectarcrm_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
                             'nectarcrm_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                             'nectarcrm_account:industry:Accounts_industry:industry:V',
                             'nectarcrm_contactdetails:email:Contacts_Email:email:E'),

		       Array('nectarcrm_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'nectarcrm_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'nectarcrm_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                             'nectarcrm_contactdetails:email:Contacts_Email:email:E',
                             'nectarcrm_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'nectarcrm_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('nectarcrm_leaddetails:firstname:Leads_First_Name:firstname:V',
			     'nectarcrm_leaddetails:lastname:Leads_Last_Name:lastname:V',
			     'nectarcrm_leaddetails:company:Leads_Company:company:V',
			     'nectarcrm_leaddetails:email:Leads_Email:email:E',
			     'nectarcrm_leaddetails:leadsource:Leads_Lead_Source:leadsource:V'),

		       Array('nectarcrm_leaddetails:firstname:Leads_First_Name:firstname:V',
                             'nectarcrm_leaddetails:lastname:Leads_Last_Name:lastname:V',
                             'nectarcrm_leaddetails:company:Leads_Company:company:V',
                             'nectarcrm_leaddetails:email:Leads_Email:email:E',
			     'nectarcrm_leaddetails:leadsource:Leads_Lead_Source:leadsource:V',
			     'nectarcrm_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V'),

		       Array('nectarcrm_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'nectarcrm_potential:amount:Potentials_Amount:amount:N',
                             'nectarcrm_potential:potentialtype:Potentials_Type:opportunity_type:V',
                             'nectarcrm_potential:leadsource:Potentials_Lead_Source:leadsource:V',
                             'nectarcrm_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('nectarcrm_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'nectarcrm_potential:amount:Potentials_Amount:amount:N',
                             'nectarcrm_potential:potentialtype:Potentials_Type:opportunity_type:V',
                             'nectarcrm_potential:leadsource:Potentials_Lead_Source:leadsource:V',
			     'nectarcrm_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('nectarcrm_activity:subject:Calendar_Subject:subject:V',
			     'nectarcrm_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I',
                             'nectarcrm_activity:status:Calendar_Status:taskstatus:V',
                             'nectarcrm_activity:priority:Calendar_Priority:taskpriority:V',
                             'nectarcrm_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),

		       Array('nectarcrm_activity:subject:Calendar_Subject:subject:V',
                             'nectarcrm_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I',
                             'nectarcrm_activity:status:Calendar_Status:taskstatus:V',
                             'nectarcrm_activity:priority:Calendar_Priority:taskpriority:V',
                             'nectarcrm_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),

        	       Array('nectarcrm_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'nectarcrm_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'nectarcrm_products:productname:Products_Product_Name:productname:V',
                             'nectarcrm_products:discontinued:Products_Product_Active:discontinued:V',
                             'nectarcrm_products:productcategory:Products_Product_Category:productcategory:V',
			     'nectarcrm_products:manufacturer:Products_Manufacturer:manufacturer:V'),

 		       Array('nectarcrm_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'nectarcrm_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                             'nectarcrm_troubletickets:severity:HelpDesk_Severity:ticketseverities:V',
                             'nectarcrm_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'nectarcrm_troubletickets:category:HelpDesk_Category:ticketcategories:V',
                             'nectarcrm_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),

		       Array('nectarcrm_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'nectarcrm_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                             'nectarcrm_troubletickets:severity:HelpDesk_Severity:ticketseverities:V',
                             'nectarcrm_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'nectarcrm_troubletickets:category:HelpDesk_Category:ticketcategories:V',
                             'nectarcrm_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),

 		       Array('nectarcrm_products:productname:Products_Product_Name:productname:V',
                             'nectarcrm_products:productcode:Products_Product_Code:productcode:V',
                             'nectarcrm_products:discontinued:Products_Product_Active:discontinued:V',
                             'nectarcrm_products:productcategory:Products_Product_Category:productcategory:V',
                             'nectarcrm_products:website:Products_Website:website:V',
			     'nectarcrm_vendorRelProducts:vendorname:Products_Vendor_Name:vendor_id:I',
			     'nectarcrm_products:mfr_part_no:Products_Mfr_PartNo:mfr_part_no:V'),

		       Array('nectarcrm_products:productname:Products_Product_Name:productname:V',
                             'nectarcrm_products:manufacturer:Products_Manufacturer:manufacturer:V',
                             'nectarcrm_products:productcategory:Products_Product_Category:productcategory:V',
                             'nectarcrm_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'nectarcrm_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'nectarcrm_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),

		       Array('nectarcrm_quotes:subject:Quotes_Subject:subject:V',
                             'nectarcrm_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I',
                             'nectarcrm_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                             'nectarcrm_quotes:contactid:Quotes_Contact_Name:contact_id:V',
                             'nectarcrm_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I',
                             'nectarcrm_accountQuotes:accountname:Quotes_Account_Name:account_id:I'),

		       Array('nectarcrm_quotes:subject:Quotes_Subject:subject:V',
                             'nectarcrm_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I',
                             'nectarcrm_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                             'nectarcrm_quotes:contactid:Quotes_Contact_Name:contact_id:V',
                             'nectarcrm_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I',
                             'nectarcrm_accountQuotes:accountname:Quotes_Account_Name:account_id:I',
			     'nectarcrm_quotes:carrier:Quotes_Carrier:carrier:V',
			     'nectarcrm_quotes:shipping:Quotes_Shipping:shipping:V'),

		       Array('nectarcrm_purchaseorder:subject:PurchaseOrder_Subject:subject:V',
			     'nectarcrm_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I',
			     'nectarcrm_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V',
			     'nectarcrm_contactdetails:firstname:Contacts_First_Name:firstname:V',
			     'nectarcrm_contactdetails:lastname:Contacts_Last_Name:lastname:V',
			     'nectarcrm_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
			     'nectarcrm_contactdetails:email:Contacts_Email:email:E'),

		       Array('nectarcrm_purchaseorder:subject:PurchaseOrder_Subject:subject:V',
			     'nectarcrm_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I',
			     'nectarcrm_purchaseorder:requisition_no:PurchaseOrder_Requisition_No:requisition_no:V',
                             'nectarcrm_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V',
			     'nectarcrm_contactdetailsPurchaseOrder:lastname:PurchaseOrder_Contact_Name:contact_id:I',
			     'nectarcrm_purchaseorder:carrier:PurchaseOrder_Carrier:carrier:V',
			     'nectarcrm_purchaseorder:salescommission:PurchaseOrder_Sales_Commission:salescommission:N',
			     'nectarcrm_purchaseorder:exciseduty:PurchaseOrder_Excise_Duty:exciseduty:N',
                             'nectarcrm_usersPurchaseOrder:user_name:PurchaseOrder_Assigned_To:assigned_user_id:V'),

		       Array('nectarcrm_invoice:subject:Invoice_Subject:subject:V',
			     'nectarcrm_invoice:salesorderid:Invoice_Sales_Order:salesorder_id:I',
			     'nectarcrm_invoice:customerno:Invoice_Customer_No:customerno:V',
			     'nectarcrm_invoice:exciseduty:Invoice_Excise_Duty:exciseduty:N',
			     'nectarcrm_invoice:salescommission:Invoice_Sales_Commission:salescommission:N',
			     'nectarcrm_accountInvoice:accountname:Invoice_Account_Name:account_id:I'),

		       Array('nectarcrm_salesorder:subject:SalesOrder_Subject:subject:V',
			     'nectarcrm_quotesSalesOrder:subject:SalesOrder_Quote_Name:quote_id:I',
			     'nectarcrm_contactdetailsSalesOrder:lastname:SalesOrder_Contact_Name:contact_id:I',
			     'nectarcrm_salesorder:duedate:SalesOrder_Due_Date:duedate:D',
			     'nectarcrm_salesorder:carrier:SalesOrder_Carrier:carrier:V',
			     'nectarcrm_salesorder:sostatus:SalesOrder_Status:sostatus:V',
			     'nectarcrm_accountSalesOrder:accountname:SalesOrder_Account_Name:account_id:I',
			     'nectarcrm_salesorder:salescommission:SalesOrder_Sales_Commission:salescommission:N',
			     'nectarcrm_salesorder:exciseduty:SalesOrder_Excise_Duty:exciseduty:N',
			     'nectarcrm_usersSalesOrder:user_name:SalesOrder_Assigned_To:assigned_user_id:V'),

		       Array('nectarcrm_campaign:campaignname:Campaigns_Campaign_Name:campaignname:V',
			     'nectarcrm_campaign:campaigntype:Campaigns_Campaign_Type:campaigntype:V',
			     'nectarcrm_campaign:targetaudience:Campaigns_Target_Audience:targetaudience:V',
			     'nectarcrm_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:I',
			     'nectarcrm_campaign:actualcost:Campaigns_Actual_Cost:actualcost:I',
			     'nectarcrm_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:I',
			     'nectarcrm_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:N',
			     'nectarcrm_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:N',
			     'nectarcrm_usersCampaigns:user_name:Campaigns_Assigned_To:assigned_user_id:V')
			);

$reports = Array(Array('reportname'=>'Contacts by Accounts',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts related to Accounts',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'0'),

		 Array('reportname'=>'Contacts without Accounts',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts not related to Accounts',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'1'),

		 Array('reportname'=>'Contacts by Potentials',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts related to Potentials',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'2'),

		 Array('reportname'=>'Lead by Source',
                       'reportfolder'=>'Lead Reports',
                       'description'=>'Lead by Source',
                       'reporttype'=>'summary',
		       'sortid'=>'0','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Lead Status Report',
                       'reportfolder'=>'Lead Reports',
                       'description'=>'Lead Status Report',
                       'reporttype'=>'summary',
                       'sortid'=>'1','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Potential Pipeline',
                       'reportfolder'=>'Potential Reports',
                       'description'=>'Potential Pipeline',
                       'reporttype'=>'summary',
                       'sortid'=>'2','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Closed Potentials',
                       'reportfolder'=>'Potential Reports',
                       'description'=>'Potential that have Won',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'3'),

		 Array('reportname'=>'Last Month Activities',
                       'reportfolder'=>'Activity Reports',
                       'description'=>'Last Month Activities',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'0','advfilterid'=>''),

		 Array('reportname'=>'This Month Activities',
                       'reportfolder'=>'Activity Reports',
                       'description'=>'This Month Activities',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'1','advfilterid'=>''),

		 Array('reportname'=>'Tickets by Products',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets related to Products',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Tickets by Priority',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets by Priority',
                       'reporttype'=>'summary',
                       'sortid'=>'3','stdfilterid'=>'','advfilterid'=>''),

 		 Array('reportname'=>'Open Tickets',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets that are Open',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'4'),

		 Array('reportname'=>'Product Details',
                       'reportfolder'=>'Product Reports',
                       'description'=>'Product Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Products by Contacts',
                       'reportfolder'=>'Product Reports',
                       'description'=>'Products related to Contacts',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Open Quotes',
                       'reportfolder'=>'Quote Reports',
                       'description'=>'Quotes that are Open',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'5'),

		 Array('reportname'=>'Quotes Detailed Report',
                       'reportfolder'=>'Quote Reports',
                       'description'=>'Quotes Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'PurchaseOrder by Contacts',
                       'reportfolder'=>'PurchaseOrder Reports',
                       'description'=>'PurchaseOrder related to Contacts',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'PurchaseOrder Detailed Report',
                       'reportfolder'=>'PurchaseOrder Reports',
                       'description'=>'PurchaseOrder Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Invoice Detailed Report',
                       'reportfolder'=>'Invoice Reports',
                       'description'=>'Invoice Detailed Report',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'SalesOrder Detailed Report',
                       'reportfolder'=>'SalesOrder Reports',
                       'description'=>'SalesOrder Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

	         Array('reportname'=>'Campaign Expectations and Actuals',
		       'reportfolder'=>'Campaign Reports',
		       'description'=>'Campaign Expectations and Actuals',
		       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'')

		);

$sortorder = Array(
                   Array(
                         Array('columnname'=>'nectarcrm_leaddetails:leadsource:Leads_Lead_Source:leadsource:V',
                               'sortorder'=>'Ascending'
                              )
			),
		   Array(
                         Array('columnname'=>'nectarcrm_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V',
                               'sortorder'=>'Ascending'
                              )
                        ),
		   Array(
                         Array('columnname'=>'nectarcrm_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V',
                               'sortorder'=>'Ascending'
                              )
                        ),
		   Array(
                         Array('columnname'=>'nectarcrm_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                               'sortorder'=>'Ascending'
                              )
                        )
                  );

$stdfilters = Array(Array('columnname'=>'nectarcrm_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time',
			  'datefilter'=>'lastmonth',
			  'startdate'=>'2005-05-01',
			  'enddate'=>'2005-05-31'),

		    Array('columnname'=>'nectarcrm_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time',
                          'datefilter'=>'thismonth',
                          'startdate'=>'2005-06-01',
                          'enddate'=>'2005-06-30')
		   );

$advfilters = Array(
                      Array(
                            Array('columnname'=>'nectarcrm_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                                  'comparator'=>'n',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'nectarcrm_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                                  'comparator'=>'e',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'nectarcrm_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                                  'comparator'=>'n',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'nectarcrm_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V',
                                  'comparator'=>'e',
                                  'value'=>'Closed Won'
                                 )
                           ),
		      Array(
                            Array('columnname'=>'nectarcrm_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                                  'comparator'=>'n',
                                  'value'=>'Closed'
                                 )
                           ),
		      Array(
                            Array('columnname'=>'nectarcrm_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                                  'comparator'=>'n',
                                  'value'=>'Accepted'
                                 ),
			    Array('columnname'=>'nectarcrm_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                                  'comparator'=>'n',
                                  'value'=>'Rejected'
                                 )
                           )
                     );
//quotes:quotestage:Quotes_Quote_Stage:quotestage:V
foreach($rptfolder as $key=>$rptarray)
{
        foreach($rptarray as $foldername=>$folderdescription)
        {
                PopulateReportFolder($foldername,$folderdescription);
                $reportid[$foldername] = $key+1;
        }
}

foreach($reports as $key=>$report)
{
        $queryid = insertSelectQuery();
        insertReports($queryid,$reportid[$report['reportfolder']],$report['reportname'],$report['description'],$report['reporttype']);
        insertSelectColumns($queryid,$selectcolumns[$key]);
        insertReportModules($queryid,$reportmodules[$key]['primarymodule'],$reportmodules[$key]['secondarymodule']);

	if(isset($stdfilters[$report['stdfilterid']]))
	{
		$i = $report['stdfilterid'];
		insertStdFilter($queryid,$stdfilters[$i]['columnname'],$stdfilters[$i]['datefilter'],$stdfilters[$i]['startdate'],$stdfilters[$i]['enddate']);
	}

	if(isset($advfilters[$report['advfilterid']]))
	{
		insertAdvFilter($queryid,$advfilters[$report['advfilterid']]);
	}

	if($report['reporttype'] == "summary")
	{
		insertSortColumns($queryid,$sortorder[$report['sortid']]);
	}
}
$adb->pquery("UPDATE nectarcrm_report SET sharingtype='Public'",array());
/** Function to store the foldername and folderdescription to database
 *  This function accepts the given folder name and description
 *  ans store it in db as SAVED
 */

function PopulateReportFolder($fldrname,$fldrdescription)
{
	global $adb;
	$sql = "INSERT INTO nectarcrm_reportfolder (FOLDERNAME,DESCRIPTION,STATE) VALUES(?,?,?)";
	$params = array($fldrname, $fldrdescription, 'SAVED');
	$result = $adb->pquery($sql, $params);
}

/** Function to add an entry in selestquery nectarcrm_table
 *
 */

function insertSelectQuery()
{
	global $adb;
	$genQueryId = $adb->getUniqueID("nectarcrm_selectquery");
        if($genQueryId != "")
        {
		$iquerysql = "insert into nectarcrm_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
		$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
	}

	return $genQueryId;
}

/** Function to store the nectarcrm_field names selected for a nectarcrm_report to a database
 *
 *
 */

function insertSelectColumns($queryid,$columnname)
{
	global $adb;
	if($queryid != "")
	{
		for($i=0;$i < count($columnname);$i++)
		{
			$icolumnsql = "insert into nectarcrm_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
			$icolumnsqlresult = $adb->pquery($icolumnsql, array($queryid, $i, $columnname[$i]));
		}
	}
}


/** Function to store the nectarcrm_report details to database
 *  This function accepts queryid,folderid,reportname,description,reporttype
 *  as arguments and store the informations in nectarcrm_report nectarcrm_table
 */

function insertReports($queryid,$folderid,$reportname,$description,$reporttype)
{
	global $adb;
	if($queryid != "")
	{
		$ireportsql = "insert into nectarcrm_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE) values (?,?,?,?,?,?,?)";
        $ireportparams = array($queryid, $folderid, $reportname, $description, $reporttype, $queryid, 'SAVED');
		$ireportresult = $adb->pquery($ireportsql, $ireportparams);
	}
}

/** Function to store the nectarcrm_report modules to database
 *  This function accepts queryid,primary module and secondary module
 *  as arguments and store the informations in nectarcrm_reportmodules nectarcrm_table
 */


function insertReportModules($queryid,$primarymodule,$secondarymodule)
{
	global $adb;
	if($queryid != "")
	{
		$ireportmodulesql = "insert into nectarcrm_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($queryid, $primarymodule, $secondarymodule));
	}
}


/** Function to store the nectarcrm_report sortorder to database
 *  This function accepts queryid,sortlists
 *  as arguments and store the informations sort columns and
 *  and sortorder in nectarcrm_reportsortcol nectarcrm_table
 */


function insertSortColumns($queryid,$sortlists)
{
	global $adb;
	if($queryid != "")
	{
		foreach($sortlists as $i=>$sort)
                {
			$sort_bysql = "insert into nectarcrm_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
			$sort_byresult = $adb->pquery($sort_bysql, array(($i+1), $queryid, $sort['columnname'], $sort['sortorder']));
		}
	}

}


/** Function to store the nectarcrm_report sort date details to database
 *  This function accepts queryid,filtercolumn,datefilter,startdate,enddate
 *  as arguments and store the informations in nectarcrm_reportdatefilter nectarcrm_table
 */


function insertStdFilter($queryid,$filtercolumn,$datefilter,$startdate,$enddate)
{
	global $adb;
	if($queryid != "")
	{
		$ireportmodulesql = "insert into nectarcrm_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($queryid, $filtercolumn, $datefilter, $startdate, $enddate));
	}

}

/** Function to store the nectarcrm_report conditions to database
 *  This function accepts queryid,filters
 *  as arguments and store the informations in nectarcrm_relcriteria nectarcrm_table
 */


function insertAdvFilter($queryid,$filters)
{
	global $adb;
	if($queryid != "")
	{
		$columnIndexArray = array();
		foreach($filters as $i=>$filter)
		{
			$irelcriteriasql = "insert into nectarcrm_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE) values (?,?,?,?,?)";
			$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($queryid,$i,$filter['columnname'],$filter['comparator'],$filter['value']));
			$columnIndexArray[] = $i;
		}
		$conditionExpression = implode(' and ', $columnIndexArray);
		$adb->pquery('INSERT INTO nectarcrm_relcriteria_grouping VALUES(?,?,?,?)', array(1, $queryid, '', $conditionExpression));
	}
}
?>