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

<div style='padding:5px;'>
{if $HISTORIES neq false}
	{foreach key=$index item=HISTORY from=$HISTORIES}
		{assign var=MODELNAME value=get_class($HISTORY)}
		{if $MODELNAME == 'ModTracker_Record_Model'}
			{assign var=USER value=$HISTORY->getModifiedBy()}
			{assign var=TIME value=$HISTORY->getActivityTime()}
			{assign var=PARENT value=$HISTORY->getParent()}
			{assign var=MOD_NAME value=$HISTORY->getParent()->getModule()->getName()}
			{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MOD_NAME}
			{assign var=TRANSLATED_MODULE_NAME value = vtranslate($SINGLE_MODULE_NAME ,$MOD_NAME)}
			{assign var=PROCEED value= TRUE}
			{if ($HISTORY->isRelationLink()) or ($HISTORY->isRelationUnLink())}
				{assign var=RELATION value=$HISTORY->getRelationInstance()}
				{if !($RELATION->getLinkedRecord())}
					{assign var=PROCEED value= FALSE}
				{/if}
			{/if}
			{if $PROCEED}
				<div class="row-fluid">
					<div class='span1'>
						<img width='24px' src="{vimage_path($MOD_NAME|cat:'.png')}" alt="{$TRANSLATED_MODULE_NAME}" title="{$TRANSLATED_MODULE_NAME}" />&nbsp;&nbsp;
					</div>
					<div class="span11">
					<p class="pull-right muted" style="padding-right:5px;"><small title="{nectarcrm_Util_Helper::formatDateTimeIntoDayString("$TIME")}">{nectarcrm_Util_Helper::formatDateDiffInStrings("$TIME")}</small></p>
					{assign var=DETAILVIEW_URL value=$PARENT->getDetailViewUrl()}
					{if $HISTORY->isUpdate()}
						{assign var=FIELDS value=$HISTORY->getFieldInstances()}
						<div class="">
							<div><b>{$USER->getName()}</b> {vtranslate('LBL_UPDATED')} <a class="cursorPointer" {if stripos($DETAILVIEW_URL, 'javascript:')===0}
								onclick='{$DETAILVIEW_URL|substr:strlen("javascript:")}' {else} onclick='window.location.href="{$DETAILVIEW_URL}"' {/if}>
								{$PARENT->getName()}</a>
							</div>
							{foreach from=$FIELDS key=INDEX item=FIELD}
							{if $INDEX lt 2}
								{if $FIELD && $FIELD->getFieldInstance() && $FIELD->getFieldInstance()->isViewableInDetailView()}
								<div class='font-x-small'>
									<i>{vtranslate($FIELD->getName(), $FIELD->getModuleName())}</i>
									{if $FIELD->get('prevalue') neq '' && $FIELD->get('postvalue') neq '' && !($FIELD->getFieldInstance()->getFieldDataType() eq 'reference' && ($FIELD->get('postvalue') eq '0' || $FIELD->get('prevalue') eq '0'))}
										&nbsp;{vtranslate('LBL_FROM')} <b>{nectarcrm_Util_Helper::tonectarcrm6SafeHTML($FIELD->getDisplayValue(decode_html($FIELD->get('prevalue'))))}</b>
									{else if $FIELD->get('postvalue') eq '' || ($FIELD->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELD->get('postvalue') eq '0')}
	                                    &nbsp; <b> {vtranslate('LBL_DELETED')} </b> ( <del>{nectarcrm_Util_Helper::tonectarcrm6SafeHTML($FIELD->getDisplayValue(decode_html($FIELD->get('prevalue'))))}</del> )
	                                {else}
										&nbsp;{vtranslate('LBL_CHANGED')}
									{/if}
	                                {if $FIELD->get('postvalue') neq '' && !($FIELD->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELD->get('postvalue') eq '0')}
										{vtranslate('LBL_TO')} <b>{nectarcrm_Util_Helper::tonectarcrm6SafeHTML($FIELD->getDisplayValue(decode_html($FIELD->get('postvalue'))))}</b>
	                                {/if}    
								</div>
								{/if}
							{else}
								<a href="{$PARENT->getUpdatesUrl()}">{vtranslate('LBL_MORE')}</a>
								{break}
							{/if}
							{/foreach}
						</div>
					{else if $HISTORY->isCreate()}
						<div class=''  style='margin-top:5px'>
							<b>{$USER->getName()}</b> {vtranslate('LBL_ADDED')} <a class="cursorPointer" {if stripos($DETAILVIEW_URL, 'javascript:')===0}
								onclick='{$DETAILVIEW_URL|substr:strlen("javascript:")}' {else} onclick='window.location.href="{$DETAILVIEW_URL}"' {/if}>
								{$PARENT->getName()}</a>
						</div>
					{else if ($HISTORY->isRelationLink() || $HISTORY->isRelationUnLink())}
	
	
	
	
	
	
	
	
	
	
	
	
						{assign var=RELATION value=$HISTORY->getRelationInstance()}
						{assign var=LINKED_RECORD_DETAIL_URL value=$RELATION->getLinkedRecord()->getDetailViewUrl()}
						{assign var=PARENT_DETAIL_URL value=$RELATION->getParent()->getParent()->getDetailViewUrl()}
						<div class='' style='margin-top:5px'>
							<b>{$USER->getName()}</b>
								{if $HISTORY->isRelationLink()}
									{vtranslate('LBL_ADDED', $MODULE_NAME)}
								{else}
									{vtranslate('LBL_REMOVED', $MODULE_NAME)}
								{/if}
								{if $RELATION->getLinkedRecord()->getModuleName() eq 'Calendar'}
									{if isPermitted('Calendar', 'DetailView', $RELATION->getLinkedRecord()->getId()) eq 'yes'}
										<a class="cursorPointer" {if stripos($LINKED_RECORD_DETAIL_URL, 'javascript:')===0} onclick='{$LINKED_RECORD_DETAIL_URL|substr:strlen("javascript:")}'
											{else} onclick='window.location.href="{$LINKED_RECORD_DETAIL_URL}"' {/if}>{$RELATION->getLinkedRecord()->getName()}</a>
									{else}
										{vtranslate($RELATION->getLinkedRecord()->getModuleName(), $RELATION->getLinkedRecord()->getModuleName())}
									{/if}
								{else}
								 <a class="cursorPointer" {if stripos($LINKED_RECORD_DETAIL_URL, 'javascript:')===0} onclick='{$LINKED_RECORD_DETAIL_URL|substr:strlen("javascript:")}'
									{else} onclick='window.location.href="{$LINKED_RECORD_DETAIL_URL}"' {/if}>{$RELATION->getLinkedRecord()->getName()}</a>
								{/if}{vtranslate('LBL_FOR')} <a class="cursorPointer" {if stripos($PARENT_DETAIL_URL, 'javascript:')===0}
								onclick='{$PARENT_DETAIL_URL|substr:strlen("javascript:")}' {else} onclick='window.location.href="{$PARENT_DETAIL_URL}"' {/if}>
								{$RELATION->getParent()->getParent()->getName()}</a>
						</div>
					{else if $HISTORY->isRestore()}
						<div class=''  style='margin-top:5px'>
							<b>{$USER->getName()}</b> {vtranslate('LBL_RESTORED')} <a class="cursorPointer" {if stripos($DETAILVIEW_URL, 'javascript:')===0}
								onclick='{$DETAILVIEW_URL|substr:strlen("javascript:")}' {else} onclick='window.location.href="{$DETAILVIEW_URL}"' {/if}>
								{$PARENT->getName()}</a>
						</div>
					{else if $HISTORY->isDelete()}
						<div class=''  style='margin-top:5px'>
							<b>{$USER->getName()}</b> {vtranslate('LBL_DELETED')} <a class="cursorPointer" {if stripos($DETAILVIEW_URL, 'javascript:')===0}
								onclick='{$DETAILVIEW_URL|substr:strlen("javascript:")}' {else} onclick='window.location.href="{$DETAILVIEW_URL}"' {/if}>
								{$PARENT->getName()}</a>
						</div>
					{/if}
					</div>
				</div>
			{/if}
			{else if $MODELNAME == 'ModComments_Record_Model'}
			<div class="row-fluid">
				<div class="span1">
					<image width='24px' src="{vimage_path('Comments.png')}"/>&nbsp;&nbsp;
				</div>
				<div class="span11">
					{assign var=COMMENT_TIME value=$HISTORY->getCommentedTime()}
					<p class="pull-right muted" style="padding-right:5px;"><small title="{nectarcrm_Util_Helper::formatDateTimeIntoDayString("$COMMENT_TIME")}">{nectarcrm_Util_Helper::formatDateDiffInStrings("$COMMENT_TIME")}</small></p>
					<div>
						<b>{$HISTORY->getCommentedByModel()->getName()}</b> {vtranslate('LBL_COMMENTED')} {vtranslate('LBL_ON')} <a class="textOverflowEllipsis" href="{$HISTORY->getParentRecordModel()->getDetailViewUrl()}">{$HISTORY->getParentRecordModel()->getName()}</a>
					</div>
					<div class='font-x-small'><i>"{nl2br($HISTORY->get('commentcontent'))}"</i></div>
				</div>
			</div>
		{/if}
	{/foreach}

	{if $NEXTPAGE}
	<div class="row-fluid">
		<div class="span12">
			<a href="javascript:;" class="load-more" data-page="{$PAGE}" data-nextpage="{$NEXTPAGE}">{vtranslate('LBL_MORE')}...</a>
		</div>
	</div>
	{/if}

{else}
	<span class="noDataMsg">
		{vtranslate('LBL_NO_UPDATES_OR_COMMENTS', $MODULE_NAME)}
	</span>
{/if}
</div>
