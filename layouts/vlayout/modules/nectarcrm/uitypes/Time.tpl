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
{assign var=FIELD_VALUE value=$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}
{assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
<div class="input-append time">
    <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" data-format="{$TIME_FORMAT}" class="timepicker-default input-small" value="{$FIELD_VALUE}" name="{$FIELD_MODEL->getFieldName()}"
	data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[nectarcrm_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}' />
    <span class="add-on cursorPointer">
        <i class="icon-time"></i>
    </span>
</div>
{/strip}