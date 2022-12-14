{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}
{* modules/Settings/Roles/views/Index.php *}

{strip}
    <div class="listViewPageDiv " id="listViewContent">
        <div class="col-sm-12 col-xs-12 ">
            <br>
            <div class="clearfix treeView">
                <ul>
                    <li data-role="{$ROOT_ROLE->getParentRoleString()}" data-roleid="{$ROOT_ROLE->getId()}">
                        <div class="toolbar-handle">
                            <a href="javascript:;" class="btn app-MARKETING droppable">{$ROOT_ROLE->getName()}</a>
                            <div class="toolbar" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}">
                                &nbsp;<a href="{$ROOT_ROLE->getCreateChildUrl()}" data-url="{$ROOT_ROLE->getCreateChildUrl()}" data-action="modal"><span class="icon-plus-sign"></span></a>
                            </div>
                        </div>
                        {assign var="ROLE" value=$ROOT_ROLE}
                        {include file=vtemplate_path("RoleTree.tpl", "Settings:Roles")}
                    </li>
                </ul>
            </div>
        </div>
    </div>
{/strip}