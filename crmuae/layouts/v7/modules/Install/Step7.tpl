{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}

<center>{'LBL_LOADING_PLEASE_WAIT'|vtranslate}...</center>

<form class="form-horizontal" name="step7" method="post" action="?module=Users&action=Login">
	<img src="//stats.nectarcrm.com/stats.php?uid={$APPUNIQUEKEY}&v={$CURRENT_VERSION}&type=I&industry={$INDUSTRY|urlencode}" alt='' title='' border=0 width='1px' height='1px'>
	<input type=hidden name="username" value="admin" >
	<input type=hidden name="password" value="{$PASSWORD}" >
</form>
<script type="text/javascript">
	jQuery(function () { /* Delay to let page load complete */
		setTimeout(function () {
			jQuery('form[name="step7"]').submit();
		}, 150);
	});
</script>
