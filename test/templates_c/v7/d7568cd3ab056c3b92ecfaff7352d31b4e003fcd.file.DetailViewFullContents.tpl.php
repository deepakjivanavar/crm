<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 11:50:33
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/DetailViewFullContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17017476035de79d8983bf79-07747123%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd7568cd3ab056c3b92ecfaff7352d31b4e003fcd' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/DetailViewFullContents.tpl',
      1 => 1574851710,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17017476035de79d8983bf79-07747123',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
    'RECORD_STRUCTURE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de79d8984366',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de79d8984366')) {function content_5de79d8984366($_smarty_tpl) {?>



<form id="detailView" method="POST"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DetailViewBlockView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>
</form>
<?php }} ?>