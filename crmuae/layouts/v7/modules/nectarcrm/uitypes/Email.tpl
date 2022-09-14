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
{assign var="FIELD_INFO" value=$FIELD_MODEL->getFieldInfo()}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if (!$FIELD_NAME)}
  {assign var="FIELD_NAME" value=$FIELD_MODEL->getFieldName()}
{/if}

{***** Edited by Nilesh (Email Duplicate validation rule)****}

<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" class="inputElement" name="{$FIELD_NAME}" type="text"
 value="{$FIELD_MODEL->get('fieldvalue')}" {if $MODE eq 'edit' && $FIELD_MODEL->get('uitype') eq '106'} readonly {/if} {if !empty($SPECIAL_VALIDATOR)}data-validator="{Zend_Json::encode($SPECIAL_VALIDATOR)}"{/if}
{if $FIELD_INFO["mandatory"] eq true} data-rule-required="true" {/if} 
{if $FIELD_NAME eq "secondaryemail"} data-rule-notEqualTo="#Leads_editView_fieldName_email" {/if}
data-rule-email="true" data-rule-illegal="true"
{if count($FIELD_INFO['validator'])}
    data-specific-rules='{ZEND_JSON::encode($FIELD_INFO["validator"])}'
{/if}
 />

{/strip}
