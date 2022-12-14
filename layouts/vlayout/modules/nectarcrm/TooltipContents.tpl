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
<div class="detailViewInfo">
	<table class="table table-bordered equalSplit detailview-table" style="table-layout:fixed">
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$RECORD_STRUCTURE['TOOLTIP_FIELDS'] name=fieldsCount}
			{if $smarty.foreach.fieldsCount.index < 7}
				<tr>
					<td class="fieldLabel narrowWidthType" nowrap>
						<label class="muted">{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
					</td>
					<td class="fieldValue narrowWidthType">
						<span class="value">
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
						</span>
					</td>
				</tr>
			{/if}
		{/foreach}
	</table>
</div>
{/strip}
