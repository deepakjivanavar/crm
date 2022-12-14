{*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<ul>
{foreach from=$ROLE->getChildren() item=CHILD_ROLE}
	<li data-role="{$CHILD_ROLE->getParentRoleString()}" data-roleid="{$CHILD_ROLE->getId()}">
            {assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}
            {assign var=VIEW_TYPE value={getPurifiedSmartyParameters('type')}}
            <div {if $VIEW_NAME != 'Popup'}class="toolbar-handle"{/if}>
                {if $VIEW_TYPE == 'Transfer'}
				{assign var="SOURCE_ROLE_SUBPATTERN" value='::'|cat:$SOURCE_ROLE->getId()}
				{if strpos($CHILD_ROLE->getParentRoleString(), $SOURCE_ROLE_SUBPATTERN) !== false}
					{$CHILD_ROLE->getName()}
				{else}
					<a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn roleEle" rel="tooltip" >{$CHILD_ROLE->getName()}</a>
				{/if}
			{else}
					<a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn draggable droppable" rel="tooltip" title="{vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE',$QUALIFIED_MODULE)}">{$CHILD_ROLE->getName()}</a>
			{/if}
			{if $VIEW_NAME != 'Popup'}
			<div class="toolbar">
				&nbsp;<a href="{$CHILD_ROLE->getCreateChildUrl()}" data-url="{$CHILD_ROLE->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}"><span class="icon-plus-sign"></span></a>
				&nbsp;<a data-id="{$CHILD_ROLE->getId()}" href="javascript:;" data-url="{$CHILD_ROLE->getDeleteActionUrl()}" data-action="modal" title="{vtranslate('LBL_DELETE', $QUALIFIED_MODULE)}"><span class="icon-trash"></span></a>
			</div>
			{/if}
		</div>

		{assign var="ROLE" value=$CHILD_ROLE}
		{include file=vtemplate_path("RoleTree.tpl", "Settings:Roles")}
	</li>
{/foreach}
</ul>
{/strip}