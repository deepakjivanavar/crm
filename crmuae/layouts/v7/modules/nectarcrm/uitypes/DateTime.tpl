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
	<input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" class="inputElement dateTimeField" name="{$FIELD_MODEL->getFieldName()}"
    type="text" value="{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}" {if !empty($SPECIAL_VALIDATOR)} data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}' {/if} 
    {if $FIELD_INFO["mandatory"] eq true} data-rule-required="true" {/if}
    {if count($FIELD_INFO['validator'])} 
        data-specific-rules='{ZEND_JSON::encode($FIELD_INFO["validator"])}'
    {/if}
    />
{/strip}