{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  nectarcrm CRM Open Source
   * The Initial Developer of the Original Code is nectarcrm.
   * Portions created by nectarcrm are Copyright (C) nectarcrm.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
{include file="Header.tpl"|vtemplate_path:$MODULE_NAME}
{include file="BasicHeader.tpl"|vtemplate_path:$MODULE_NAME}

<div class="bodyContents">
	<div class="mainContainer row-fluid">
		<div class="span2 row-fluid">
			{include file="SideBar.tpl"|vtemplate_path:$MODULE_NAME}
		</div>
		<input type="hidden" class="isRecordCreatable" value="{$IS_RECORD_CREATABLE}">
		<input type="hidden" class="isModuleEditable" value="{$IS_MODULE_EDITABLE}">
		<input type="hidden" class="isModuleDeletable" value="{$IS_MODULE_DELETABLE}">
		<div class="contentsDiv span10 marginLeftZero">
{/strip}