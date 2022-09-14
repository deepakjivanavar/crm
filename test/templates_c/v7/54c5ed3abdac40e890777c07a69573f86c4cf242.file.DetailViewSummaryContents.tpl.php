<?php /* Smarty version Smarty-3.1.7, created on 2019-11-21 11:54:52
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\HelpDesk\DetailViewSummaryContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13127658845dd67b0c1c8cd5-63778894%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '54c5ed3abdac40e890777c07a69573f86c4cf242' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\HelpDesk\\DetailViewSummaryContents.tpl',
      1 => 1520586670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13127658845dd67b0c1c8cd5-63778894',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dd67b0c1cf9f',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dd67b0c1cf9f')) {function content_5dd67b0c1cf9f($_smarty_tpl) {?>
<form id="detailView" method="POST"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SummaryViewWidgets.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</form><?php }} ?>