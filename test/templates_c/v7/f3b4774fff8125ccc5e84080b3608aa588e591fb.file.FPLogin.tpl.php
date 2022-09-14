<?php /* Smarty version Smarty-3.1.7, created on 2020-01-24 09:03:40
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Users/FPLogin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3127906545e2ab2ec0ea5a8-59087382%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f3b4774fff8125ccc5e84080b3608aa588e591fb' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Users/FPLogin.tpl',
      1 => 1574851730,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3127906545e2ab2ec0ea5a8-59087382',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ERROR' => 0,
    'USERNAME' => 0,
    'PASSWORD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5e2ab2ec122d5',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5e2ab2ec122d5')) {function content_5e2ab2ec122d5($_smarty_tpl) {?>





<?php if ($_smarty_tpl->tpl_vars['ERROR']->value){?>
	Error, please retry setting the password!!
<?php }else{ ?>
	<h4>Loading .... </h4>
	<form class="form-horizontal" name="login" id="login" method="post" action="../../../index.php?module=Users&action=Login">
		<input type=hidden name="username" value="<?php echo $_smarty_tpl->tpl_vars['USERNAME']->value;?>
" >
		<input type=hidden name="password" value="<?php echo $_smarty_tpl->tpl_vars['PASSWORD']->value;?>
" >
	</form>
	<script type="text/javascript">
		function autoLogin () {
			var form = document.getElementById("login");
			form.submit();
		}
		window.onload = autoLogin;
	</script>
<?php }?><?php }} ?>