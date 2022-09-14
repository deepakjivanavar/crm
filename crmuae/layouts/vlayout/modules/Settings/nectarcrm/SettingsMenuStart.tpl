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
{include file="Header.tpl"|vtemplate_path:$MODULE}
{include file="BasicHeader.tpl"|vtemplate_path:$MODULE}

{strip}
{if $LOAD_OLD}
    <div class="bodyContents">
        <div class="mainContainer row-fluid">
            <div class="span12">
                <iframe id="ui5frame" src="{$UI5_URL}" width="100%" height="650px" style="border:0;"></iframe>
            </div>

{else}
    <div class="bodyContents">
        <div class="mainContainer row-fluid">
            <div class="span2 row-fluid">
				<div>{include file='Sidebar.tpl'|@vtemplate_path:$QUALIFIED_MODULE}</div>
            </div>
    		<div class="contentsDiv span10 marginLeftZero">
{/if}
{/strip}