{*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Inventory/views/SubProductsPopupAjax.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE}
<div class="row">
    {include file='PopupNavigation.tpl'|vtemplate_path:$MODULE}
</div>
<div id='popupContentsDiv'>
<div class="row">
    <div class="col-md-12">
	{include file="PopupEntries.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>
</div>
{/strip}

