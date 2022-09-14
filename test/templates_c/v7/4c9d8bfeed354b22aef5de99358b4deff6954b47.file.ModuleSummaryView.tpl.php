<?php /* Smarty version Smarty-3.1.7, created on 2019-12-03 11:35:11
         compiled from "D:\xamp\htdocs\crm\includes\runtime/../../layouts/v7\modules\nectarcrm\ModuleSummaryView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17002303915de6486f530f78-12706896%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c9d8bfeed354b22aef5de99358b4deff6954b47' => 
    array (
      0 => 'D:\\xamp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\ModuleSummaryView.tpl',
      1 => 1574851712,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17002303915de6486f530f78-12706896',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
    'SUMMARY_RECORD_STRUCTURE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de6486f55397',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de6486f55397')) {function content_5de6486f55397($_smarty_tpl) {?>



<div class="recordDetails">
    <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DetailViewBlockView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['SUMMARY_RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>

</div><?php }} ?>