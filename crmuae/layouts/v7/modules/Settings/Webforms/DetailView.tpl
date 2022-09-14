{*<!--
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
<div class="detailViewContainer">
    <div class="col-sm-12">
    {include file="DetailViewHeader.tpl"|vtemplate_path:nectarcrm MODULE_NAME=$MODULE_NAME}
    {include file='DetailViewBlockView.tpl'|@vtemplate_path:nectarcrm RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    {include file='FieldsDetailView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    </div>
</div>
</div></div>
{/strip}