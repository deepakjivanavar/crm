<?php /* Smarty version Smarty-3.1.7, created on 2019-12-16 13:40:42
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/SMSNotifier/TextAnyWhereEditField.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15578029825df7895aac1110-04222806%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5b648510d6595cb3aa4298053b6ae1c98127cab' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/SMSNotifier/TextAnyWhereEditField.tpl',
      1 => 1574851728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15578029825df7895aac1110-04222806',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5df7895aace3e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5df7895aace3e')) {function content_5df7895aace3e($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ProviderEditFields.tpl",$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<div class="alert-info alert col-lg-12"><div>In the Originator field, enter one of the following:<br /><br />1. The 11 characters to be sent with each SMS<br />2. The mobile number to be sent with each SMS<br />3. The email address to which any SMS replies will be sent<br /></div><br><div><div><a href="http://www.textanywhere.net/static/Products/nectarcrm_Capabilities.aspx" target="_blank">Help</a></div><div><a href="https://www.textapp.net/web/textanywhere/" target="_blank">Account Login</a></div><div><a href="https://www.textapp.net/web/textanywhere/Register/Register.aspx" target="_blank">Create Account</a></div></div></div><?php }} ?>