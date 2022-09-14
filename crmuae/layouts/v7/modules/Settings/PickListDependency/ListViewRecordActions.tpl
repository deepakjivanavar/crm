{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}
{strip}
<div class="table-actions">
    {assign var=RECORD_SOURCE_MODULE value=$LISTVIEW_ENTRY->get('sourceModule')}
    {assign var=RECORD_SOURCE_FIELD value=$LISTVIEW_ENTRY->get('sourcefield')}
    {assign var=RECORD_TARGET_FIELD value=$LISTVIEW_ENTRY->get('targetfield')}
    <span class="fa fa-pencil" onclick="javascript:Settings_PickListDependency_Js.triggerEdit(event, '{$RECORD_SOURCE_MODULE}', '{$RECORD_SOURCE_FIELD}', '{$RECORD_TARGET_FIELD}')" title="{vtranslate('LBL_EDIT',$MODULE)}"></span>
    <span class="fa fa-trash-o" onclick="javascript:Settings_PickListDependency_Js.triggerDelete(event, '{$RECORD_SOURCE_MODULE}', '{$RECORD_SOURCE_FIELD}', '{$RECORD_TARGET_FIELD}')" title="{vtranslate('LBL_DELETE',$MODULE)}"></span>
</div>