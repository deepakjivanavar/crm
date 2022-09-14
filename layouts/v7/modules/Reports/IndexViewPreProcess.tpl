{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}
{strip}
    {include file="modules/nectarcrm/partials/Topbar.tpl"}

    <div class="container-fluid app-nav">
        <div class="row">
            {include file="modules/Reports/partials/SidebarHeader.tpl"}
            {include file="ModuleHeader.tpl"|vtemplate_path:$MODULE}
        </div>
    </div>
</nav>
<div class="clearfix main-container">
    <div>
        <div class="editViewPageDiv viewContent">
            <div class="reports-content-area">
{/strip}