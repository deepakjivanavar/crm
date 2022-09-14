{************************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<div>
	{foreach item=KEYMETRIC from=$KEYMETRICS}
	<div style="padding-bottom:6px;">
		<span class="pull-right">{$KEYMETRIC.count}</span>
		<a href="?module={$KEYMETRIC.module}&view=List&viewname={$KEYMETRIC.id}">{$KEYMETRIC.name}</a>
	</div>	
	{/foreach}
</div>
{/strip}
