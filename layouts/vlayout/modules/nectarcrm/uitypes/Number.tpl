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
{assign var="FIELD_INFO" value=nectarcrm_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $MODULE eq 'HelpDesk' && ($FIELD_MODEL->get('name') eq 'days' || $FIELD_MODEL->get('name') eq 'hours')}
	{assign var="FIELD_VALUE" value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
{else}
	{assign var="FIELD_VALUE" value=$FIELD_MODEL->get('fieldvalue')}
{/if}
<input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" class="input-large" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[nectarcrm_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}"
value="{$FIELD_VALUE}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
{/strip}