<?php /* Smarty version Smarty-3.1.7, created on 2019-11-21 11:54:52
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\HelpDesk\ModuleSummaryView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7219522215dd67b0c0b3ee8-78172570%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8a4ec225416598b9065733036c03e31782eebde8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\HelpDesk\\ModuleSummaryView.tpl',
      1 => 1520586670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7219522215dd67b0c0b3ee8-78172570',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dd67b0c0f2ca',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dd67b0c0f2ca')) {function content_5dd67b0c0f2ca($_smarty_tpl) {?>
<div class="recordDetails"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SummaryViewContents.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div><?php }} ?>