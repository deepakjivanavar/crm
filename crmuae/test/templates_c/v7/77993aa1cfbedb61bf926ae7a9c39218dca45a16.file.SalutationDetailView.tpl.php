<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 11:30:56
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/uitypes/SalutationDetailView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9294572105de798f08ed322-71007416%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '77993aa1cfbedb61bf926ae7a9c39218dca45a16' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/uitypes/SalutationDetailView.tpl',
      1 => 1574851716,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9294572105de798f08ed322-71007416',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RECORD' => 0,
    'FIELD_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de798f08f6a4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de798f08f6a4')) {function content_5de798f08f6a4($_smarty_tpl) {?>


<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getDisplayValue('salutationtype');?>


<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);?>
<?php }} ?>