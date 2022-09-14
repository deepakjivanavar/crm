<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 11:50:33
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/ModuleSummaryView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16049630675de79d8972a417-49509912%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '227ced0d4cd55e0974bf3189683f4b579716e41e' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/ModuleSummaryView.tpl',
      1 => 1574851712,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16049630675de79d8972a417-49509912',
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
  'unifunc' => 'content_5de79d8973227',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de79d8973227')) {function content_5de79d8973227($_smarty_tpl) {?>



<div class="recordDetails">
    <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DetailViewBlockView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['SUMMARY_RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>

</div><?php }} ?>