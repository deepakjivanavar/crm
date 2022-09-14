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
    {foreach item=CONTACT_INFO from=$RELATED_CONTACTS}
        <a href='{$CONTACT_INFO['_model']->getDetailViewUrl()}' title='{vtranslate("Contacts", "Contacts")}'> {nectarcrm_Util_Helper::getRecordName($CONTACT_INFO['id'])}</a>
        <br>
    {/foreach}
{/strip}