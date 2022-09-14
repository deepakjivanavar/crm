<?php /* Smarty version Smarty-3.1.7, created on 2019-12-17 07:27:27
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/nectarcrm/ListViewRecordActions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20681830965df8835f4fc475-76462134%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fe0c74d226df2f761bce1c9232bbf869291488d' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/nectarcrm/ListViewRecordActions.tpl',
      1 => 1574851726,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20681830965df8835f4fc475-76462134',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_ENTRY' => 0,
    'RECORD_LINK' => 0,
    'RECORD_LINK_URL' => 0,
    'QUALIFIED_MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5df8835f62473',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5df8835f62473')) {function content_5df8835f62473($_smarty_tpl) {?>



<div class="table-actions" style = "width:60px">
    <?php  $_smarty_tpl->tpl_vars['RECORD_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getRecordLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['RECORD_LINK']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['RECORD_LINK']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD_LINK']->key => $_smarty_tpl->tpl_vars['RECORD_LINK']->value){
$_smarty_tpl->tpl_vars['RECORD_LINK']->_loop = true;
 $_smarty_tpl->tpl_vars['RECORD_LINK']->iteration++;
 $_smarty_tpl->tpl_vars['RECORD_LINK']->last = $_smarty_tpl->tpl_vars['RECORD_LINK']->iteration === $_smarty_tpl->tpl_vars['RECORD_LINK']->total;
?>
        <?php $_smarty_tpl->tpl_vars["RECORD_LINK_URL"] = new Smarty_variable($_smarty_tpl->tpl_vars['RECORD_LINK']->value->getUrl(), null, 0);?>
        <a <?php if (stripos($_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value,'javascript:')===0){?> onclick="<?php echo substr($_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value,strlen("javascript:"));?>
;
                if (event.stopPropagation){
        event.stopPropagation();} else{
        event.cancelBubble = true;}" <?php }else{ ?> href='<?php echo $_smarty_tpl->tpl_vars['RECORD_LINK_URL']->value;?>
' <?php }?>>
            <i class="<?php if ($_smarty_tpl->tpl_vars['RECORD_LINK']->value->getLabel()=='LBL_EDIT'){?>fa fa-pencil<?php }elseif($_smarty_tpl->tpl_vars['RECORD_LINK']->value->getLabel()=='LBL_DELETE'){?>fa fa-trash<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['RECORD_LINK']->value->getIcon();?>
<?php }?>" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['RECORD_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"></i>
        </a>
        <?php if (!$_smarty_tpl->tpl_vars['RECORD_LINK']->last){?>
            &nbsp;&nbsp;
        <?php }?>
    <?php } ?>
</div>
<?php }} ?>