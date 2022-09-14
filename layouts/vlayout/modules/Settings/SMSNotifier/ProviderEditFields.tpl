{************************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="controls-group">
		<span class="control-label">
			<strong>
				{vtranslate('username', $QUALIFIED_MODULE_NAME)}
			</strong>
		</span>
		<div class="controls">
			<input type="text" name="username" class="span3" data-validation-engine="validate[required]" value="{$RECORD_MODEL->get('username')}" />
		</div>
	</div>
	<br>
	<div class="controls-group">
		<span class="control-label">
			<strong>
				{vtranslate('password', $QUALIFIED_MODULE_NAME)}
			</strong>
		</span>
		<div class="controls">
			<input type="password" name="password" class="span3" data-validation-engine="validate[required]" value="{$RECORD_MODEL->get('password')}" />
		</div>
	</div>
	<br>
	{include file='BaseProviderEditFields.tpl'|@vtemplate_path:$QUALIFIED_MODULE_NAME RECORD_MODEL=$RECORD_MODEL}
{/strip}