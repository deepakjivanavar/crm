{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}

{assign var=APP_IMAGE_MAP value=nectarcrm_MenuStructure_Model::getAppIcons()}
<div class="col-sm-12 col-xs-12 app-indicator-icon-container app-{$SELECTED_MENU_CATEGORY}">
    <div class="row" title="{vtranslate("LBL_SETTINGS",$MODULE)}">
        <span class="app-indicator-icon fa fa-cog"></span>
    </div>
</div>
    
{include file="modules/nectarcrm/partials/SidebarAppMenu.tpl"}