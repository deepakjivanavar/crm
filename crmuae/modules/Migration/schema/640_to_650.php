<?php
/*+********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *********************************************************************************/

if(defined('NECTARCRM_UPGRADE')) {

//Start add new currency - 'CFP Franc or Pacific Franc' 
global $adb;

nectarcrm_Utils::AddColumn('nectarcrm_portalinfo', 'cryptmode', 'varchar(20)');
$adb->pquery("ALTER TABLE nectarcrm_portalinfo MODIFY COLUMN user_password varchar(255)", array());

//Updating existing users password to thier md5 hash
$portalinfo_hasmore = true;
do {
	$result = $adb->pquery('SELECT id, user_password FROM nectarcrm_portalinfo WHERE cryptmode is null limit 1000', array());
	
	$portalinfo_hasmore = false; // assume we are done.
	while ($row = $adb->fetch_array($result)) {
		$portalinfo_hasmore = true; // we found at-least one so there could be more.
		
		$enc_password = nectarcrm_Functions::generateEncryptedPassword(decode_html($row['user_password']));
		$adb->pquery('UPDATE nectarcrm_portalinfo SET user_password=?, cryptmode = ? WHERE id=?', array($enc_password, 'CRYPT', $row['id']));
	}
	
} while ($portalinfo_hasmore);

//Change column type of inventory line-item comment.
$adb->pquery("ALTER TABLE nectarcrm_inventoryproductrel MODIFY COLUMN comment TEXT", array());


// Initlize mailer_queue tables.
include_once 'vtlib/nectarcrm/Mailer.php';
$mailer = new nectarcrm_Mailer();
$mailer->__initializeQueue();

//set settings links, fixes translation issue on migrations from 5.x
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Users&parent=Settings&view=List' where name='LBL_USERS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Roles&parent=Settings&view=Index' where name='LBL_ROLES'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Profiles&parent=Settings&view=List' where name='LBL_PROFILES'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Groups&parent=Settings&view=List' where name='USERGROUPLIST'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=SharingAccess&parent=Settings&view=Index' where name='LBL_SHARING_ACCESS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=FieldAccess&parent=Settings&view=Index' where name='LBL_FIELDS_ACCESS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=LoginHistory&parent=Settings&view=List' where name='LBL_LOGIN_HISTORY_DETAILS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=ModuleManager&parent=Settings&view=List' where name='VTLIB_LBL_MODULE_MANAGER'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=Picklist&view=Index' where name='LBL_PICKLIST_EDITOR'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=PickListDependency&view=List' where name='LBL_PICKLIST_DEPENDENCY_SETUP'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=MenuEditor&parent=Settings&view=Index' where name='LBL_MENU_EDITOR'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Settings&view=listnotificationschedulers&parenttab=Settings' where name='NOTIFICATIONSCHEDULERS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Settings&view=listinventorynotifications&parenttab=Settings' where name='INVENTORYNOTIFICATION'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=nectarcrm&view=CompanyDetails' where name='LBL_COMPANY_DETAILS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=nectarcrm&view=OutgoingServerDetail' where name='LBL_MAIL_SERVER_SETTINGS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=Currency&view=List' where name='LBL_CURRENCY_SETTINGS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=nectarcrm&parent=Settings&view=TaxIndex' where name='LBL_TAX_SETTINGS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Settings&submodule=Server&view=ProxyConfig' where name='LBL_SYSTEM_INFO'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=nectarcrm&view=AnnouncementEdit' where name='LBL_ANNOUNCEMENT'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Settings&action=DefModuleView&parenttab=Settings' where name='LBL_DEFAULT_MODULE_VIEW'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=nectarcrm&view=TermsAndConditionsEdit' where name='INVENTORYTERMSANDCONDITIONS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=nectarcrm&parent=Settings&view=CustomRecordNumbering' where name='LBL_CUSTOMIZE_MODENT_NUMBER'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?parent=Settings&module=MailConverter&view=List' where name='LBL_MAIL_SCANNER'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=Workflows&parent=Settings&view=List' where name='LBL_LIST_WORKFLOWS'", array());
$adb->pquery("Update nectarcrm_settings_field set linkto='index.php?module=nectarcrm&parent=Settings&view=ConfigEditorDetail' where name='LBL_CONFIG_EDITOR'", array());

// Extend description data-type (eg. allow large emails to be stored)
$adb->pquery("ALTER TABLE nectarcrm_crmentity MODIFY COLUMN description MEDIUMTEXT", array());

}

