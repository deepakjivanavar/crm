<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 12:26:18
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Profiles/ListViewRecordActions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9742329225de7a5eaaa5084-41048681%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c96abd7d49755d7f80b944c4c76a8a01c1480a81' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Profiles/ListViewRecordActions.tpl',
      1 => 1574851728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9742329225de7a5eaaa5084-41048681',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_ENTRY' => 0,
    'key' => 0,
    'RECORD_LINK' => 0,
    'RECORD_LINK_URL' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de7a5eaabfed',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de7a5eaabfed')) {function content_5de7a5eaabfed($_smarty_tpl) {?>
<!--LIST VIEW RECORD ACTIONS--><div class="table-actions"><?php $_smarty_tpl->tpl_vars['RECORD_LINKS'] = new Smarty_variable($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getRecordLinks(), null, 0);?><?php $_smarty_tpl->tpl_vars['RECORD_LINK_URL'] = new Smarty_variable(array(), null, 0);?><?php  $_smarty_tpl->tpl_vars['RECORD_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD_LINK']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getRecordLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD_LINK']->key => $_smarty_tpl->tpl_vars['RECORD_LINK']->value){
$_smarty_tpl->tpl_vars['RECORD_LINK']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['RECORD_LINK']->key;
?><?php $_smarty_tpl->createLocalArrayVariable('RECORD_LINK_URL', null, 0);
$_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value[$_smarty_tpl->tpl_vars['key']->value] = $_smarty_tpl->tpl_vars['RECORD_LINK']->value->getUrl();?><?php } ?><span><a href="<?php echo $_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value[1];?>
" title="<?php echo vtranslate('LBL_DUPLICATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><i class="fa fa-copy"></i></a></span><span class="more dropdown action"><span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-v icon"></i></span><ul class="dropdown-menu"><li><a data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" href="<?php echo $_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value[0];?>
" title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><li> <a data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" href="javascript:void(0)" onclick="<?php echo $_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value[2];?>
" title="<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li></ul></span></div><?php }} ?>