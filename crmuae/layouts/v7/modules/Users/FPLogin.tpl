{*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Users/actions/ForgotPassword.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{*<DIV>TEMPLATE: layout/modules/Users/FPLogin.tpl</DIV>*}

{if $ERROR}
	Error, please retry setting the password!!
{else}
	<h4>Loading .... </h4>
	<form class="form-horizontal" name="login" id="login" method="post" action="../../../index.php?module=Users&action=Login">
		<input type=hidden name="username" value="{$USERNAME}" >
		<input type=hidden name="password" value="{$PASSWORD}" >
	</form>
	<script type="text/javascript">
		function autoLogin () {
			var form = document.getElementById("login");
			form.submit();
		}
		window.onload = autoLogin;
	</script>
{/if}