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
<div class="relatedContacts container-fluid">
	{foreach item=RELATED_RECORD from=$RELATED_RECORDS}
		<div class="recentActivitiesContainer row">
			<ul class="unstyled">
				<li>
					<div class="">
						<div class="textOverflowEllipsis">
							<a href="{$RELATED_RECORD->getDetailViewUrl()}" id="{$MODULE}_{$RELATED_MODULE}_Related_Record_{$RELATED_RECORD->get('id')}" title="{nectarcrm_Util_Helper::getRecordName($RELATED_RECORD->get('id'))}">
                                {nectarcrm_Util_Helper::getRecordName($RELATED_RECORD->get('id'))}
							</a>
						</div>
						<div>{$RELATED_RECORD->getDisplayValue('email')}</div>
						<div class="textOverflowEllipsis" title="{$RELATED_RECORD->getDisplayValue('phone')}">{$RELATED_RECORD->getDisplayValue('phone')}</div>
					</div>
				</li>
			</ul>
		</div>
	{/foreach}
	{assign var=NUMBER_OF_RECORDS value=count($RELATED_RECORDS)}
	{if $NUMBER_OF_RECORDS eq 5}
		<div class="row">
			<div class="pull-right">
				<a href="javascript:void(0)" class="moreRecentContacts cursorPointer">{vtranslate('LBL_MORE',$MODULE_NAME)}</a>
			</div>
		</div>
	{/if}
</div>
{/strip}
