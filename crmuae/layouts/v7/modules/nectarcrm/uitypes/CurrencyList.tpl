{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
*************************************************************************************}

{strip}
	{assign var=CURRENCY_LIST value=$FIELD_MODEL->getCurrencyList()}
	<select class="select2 inputElement" name="{$FIELD_MODEL->getFieldName()}">
		{foreach item=CURRENCY_NAME key=CURRENCY_ID from=$CURRENCY_LIST}
			<option value="{$CURRENCY_ID}" data-picklistvalue= '{$CURRENCY_ID}' {if $FIELD_MODEL->get('fieldvalue') eq $CURRENCY_ID} selected {/if}>{vtranslate($CURRENCY_NAME, $MODULE)}</option>
		{/foreach}
	</select>
{/strip}