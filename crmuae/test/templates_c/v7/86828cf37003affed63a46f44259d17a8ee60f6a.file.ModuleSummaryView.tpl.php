<?php /* Smarty version Smarty-3.1.7, created on 2019-11-21 11:55:52
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\Project\ModuleSummaryView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14816975005dd67b48651cd9-74741974%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '86828cf37003affed63a46f44259d17a8ee60f6a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\Project\\ModuleSummaryView.tpl',
      1 => 1571120085,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14816975005dd67b48651cd9-74741974',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dd67b48683cc',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dd67b48683cc')) {function content_5dd67b48683cc($_smarty_tpl) {?>
<div class="recordDetails"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SummaryViewContents.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div>
<?php }} ?>