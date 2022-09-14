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

{assign var="_DefaultLoginTemplate" value=vtemplate_path('Login.Default.tpl', 'Users')}
{assign var="_CustomLoginTemplate" value=vtemplate_path('Login.Custom.tpl', 'Users')}
{assign var="_CustomLoginTemplateFullPath" value="layouts/vlayout/$_CustomLoginTemplate"}

{if file_exists($_CustomLoginTemplateFullPath)}
	{include file=$_CustomLoginTemplate}
{else}
	{include file=$_DefaultLoginTemplate}
{/if}