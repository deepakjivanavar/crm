{*+***********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
*************************************************************************************}
{strip}
	<div class="container-fluid">
		<br>
		<h3 class="themeTextColor">{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
		<hr>

		<form name="EditRole" action="index.php" method="post" id="EditView" class="form-horizontal">
			<input type="hidden" name="module" value="Roles">
			<input type="hidden" name="action" value="Save">
			<input type="hidden" name="parent" value="Settings">
			{assign var=RECORD_ID value=$RECORD_MODEL->getId()}
			<input type="hidden" name="record" value="{$RECORD_ID}" />
			<input type="hidden" name="mode" value="{$MODE}">
			<input type="hidden" name="profile_directly_related_to_role_id" value="{$PROFILE_ID}" />
			{assign var=HAS_PARENT value="{if $RECORD_MODEL->getParent()}true{/if}"}
			{if $HAS_PARENT}
				<input type="hidden" name="parent_roleid" value="{$RECORD_MODEL->getParent()->getId()}">
			{/if}

			<div style="padding:20px;">
				<div class="row-fluid">
					<label class="span3"><strong>{vtranslate('LBL_NAME', $QUALIFIED_MODULE)}<span class="redColor">*</span>: </strong></label>
					<input type="text" class="fieldValue span7" name="rolename" id="profilename" value="{$RECORD_MODEL->getName()}" data-validation-engine='validate[required]'  />
				</div><br>
				<div class="row-fluid">
					<label class="span3"><strong>{vtranslate('LBL_REPORTS_TO', $QUALIFIED_MODULE)}: </strong></label>
					<div class="span8 fieldValue">
						<input type="hidden" name="parent_roleid" {if $HAS_PARENT}value="{$RECORD_MODEL->getParent()->getId()}"{/if}>
						<input type="text" class="input-large" name="parent_roleid_display" {if $HAS_PARENT}value="{$RECORD_MODEL->getParent()->getName()}"{/if} readonly>
					</div>
				</div><br>
                <div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_CAN_ASSIGN_RECORDS_TO', $QUALIFIED_MODULE)}: </strong></label>
					<div class="row-fluid span9 fieldValue">
						<div class="span">
							<input type="radio" value="1"{if !$RECORD_MODEL->get('allowassignedrecordsto')} checked=""{/if} {if $RECORD_MODEL->get('allowassignedrecordsto') eq '1'} checked="" {/if} name="allowassignedrecordsto" data-handler="new" class="alignTop"/>&nbsp;<span>{vtranslate('LBL_ALL_USERS',$QUALIFIED_MODULE)}</span>
						</div>
                        <div class="span1">&nbsp;</div>
						<div class="span">
							<input type="radio" value="2" {if $RECORD_MODEL->get('allowassignedrecordsto') eq '2'} checked="" {/if} name="allowassignedrecordsto" data-handler="new" class="alignTop"/>&nbsp;<span>{vtranslate('LBL_USERS_WITH_SAME_OR_LOWER_LEVEL',$QUALIFIED_MODULE)}</span>
						</div>
                        <div class="span1">&nbsp;</div>
						<div class="span">
							<input type="radio" value="3" {if $RECORD_MODEL->get('allowassignedrecordsto') eq '3'} checked="" {/if} name="allowassignedrecordsto" data-handler="new" class="alignTop"/>&nbsp;<span>{vtranslate('LBL_USERS_WITH_LOWER_LEVEL',$QUALIFIED_MODULE)}</span>
						</div>
				</div>
                </div><br>
				<div class="row-fluid">
					<label class="span3"><strong>{vtranslate('LBL_PRIVILEGES',$QUALIFIED_MODULE)}:</strong></label>
					<div class="row-fluid span8 fieldValue">
						<div class="span">
							<input type="radio" value="1" {if $PROFILE_DIRECTLY_RELATED_TO_ROLE} checked="" {/if} name="profile_directly_related_to_role" data-handler="new" class="alignTop"/>&nbsp;<span>{vtranslate('LBL_ASSIGN_NEW_PRIVILEGES',$QUALIFIED_MODULE)}</span>
						</div>
                        <div class="span1">&nbsp;</div>
						<div class="span">
							<input type="radio" value="0" {if $PROFILE_DIRECTLY_RELATED_TO_ROLE eq false} checked="" {/if} name="profile_directly_related_to_role" data-handler="existing" class="alignTop"/>&nbsp;<span>{vtranslate('LBL_ASSIGN_EXISTING_PRIVILEGES',$QUALIFIED_MODULE)}</span>
						</div>
					</div>
				</div>
				<br>
				<div class="row-fluid hide padding20px boxSizingBorderBox contentsBackground" data-content="new">
					<div class="fieldValue span12">
					</div>
				</div>
				<div class="row-fluid hide" data-content="existing">
					<div class="fieldValue row-fluid">
						{assign var="ROLE_PROFILES" value=$RECORD_MODEL->getProfiles()}
						<select class="select2" multiple="true" id="profilesList" name="profiles[]" data-placeholder="{vtranslate('LBL_CHOOSE_PROFILES',$QUALIFIED_MODULE)}" style="width: 800px">
							{foreach from=$ALL_PROFILES item=PROFILE}
								{if $PROFILE->isDirectlyRelated() eq false}
									<option value="{$PROFILE->getId()}" {if isset($ROLE_PROFILES[$PROFILE->getId()])}selected="true"{/if}>{$PROFILE->getName()}</option>
								{/if}
							{/foreach}
						</select>
					</div>
				</div>
			</div>

			<div class="textAlignCenter">
				<button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE',$MODULE)}</button>
				<a class="cancelLink" onclick="javascript:window.history.back();" type="reset">{vtranslate('LBL_CANCEL',$MODULE)}</a>
			</div>
			<br>
		</form>
	</div>
{/strip}