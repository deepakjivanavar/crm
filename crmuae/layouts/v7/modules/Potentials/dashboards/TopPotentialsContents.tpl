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
<div style='padding:5px'>
{if count($MODELS) > 0}
	<div>
        <div class='row'>
            <div class='col-lg-4'>
                <b>{vtranslate('Potential Name', $MODULE_NAME)}</b>
            </div>
            <div class='col-lg-4'>
                <b>{vtranslate('Amount', $MODULE_NAME)}</b>
            </div>
            <div class='col-lg-4'>
                <b>{vtranslate('Related To', $MODULE_NAME)}</b>
            </div>
        </div>
		<hr>
		{foreach item=MODEL from=$MODELS}
		<div class='row'>
			<div class='col-lg-4'>
				<a href="{$MODEL->getDetailViewUrl()}">{$MODEL->getName()}</a>
			</div>
			<div class='col-lg-4'>
				{CurrencyField::appendCurrencySymbol($MODEL->getDisplayValue('amount'), $USER_CURRENCY_SYMBOL)}
			</div>
			<div class='col-lg-4'>
				{$MODEL->getDisplayValue('related_to')}
			</div>
		</div>
		{/foreach}
	</div>
{else}
	<span class="noDataMsg">
		{vtranslate('LBL_NO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
	</span>
{/if}
</div>