<?php /* Smarty version Smarty-3.1.7, created on 2019-12-18 09:20:20
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/EmailTemplates/DefaultContentForTemplates.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7237401025def6c34264682-48737607%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b07d5c2176102d1d598cd7395b32198dc6c1d17' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/EmailTemplates/DefaultContentForTemplates.tpl',
      1 => 1576660806,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7237401025def6c34264682-48737607',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5def6c34279a6',
  'variables' => 
  array (
    'COMPANY_MODEL' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5def6c34279a6')) {function content_5def6c34279a6($_smarty_tpl) {?>

<?php $_smarty_tpl->tpl_vars["VIEW_IN_BROWSER_TAG"] = new Smarty_variable(EmailTemplates_Module_Model::$BROWSER_MERGE_TAG, null, 0);?>
<?php $_smarty_tpl->tpl_vars["WEBSITE_URL"] = new Smarty_variable($_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get('website'), null, 0);?>
<?php $_smarty_tpl->tpl_vars["FACEBOOK_URL"] = new Smarty_variable($_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get('facebook'), null, 0);?>
<?php $_smarty_tpl->tpl_vars["TWITTER_URL"] = new Smarty_variable($_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get('twitter'), null, 0);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
	</head>
	<body class="scayt-enabled">
		<div>
			<center>
				
			</center>
		</div>
	</body>
</html>
<?php }} ?>