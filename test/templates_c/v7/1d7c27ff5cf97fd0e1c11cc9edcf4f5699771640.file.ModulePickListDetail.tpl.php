<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 12:18:25
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Picklist/ModulePickListDetail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1431170515de7a4115540f1-75980528%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d7c27ff5cf97fd0e1c11cc9edcf4f5699771640' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Picklist/ModulePickListDetail.tpl',
      1 => 1574851728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1431170515de7a4115540f1-75980528',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'NO_PICKLIST_FIELDS' => 0,
    'SELECTED_MODULE_NAME' => 0,
    'QUALIFIED_NAME' => 0,
    'CREATE_PICKLIST_URL' => 0,
    'QUALIFIED_MODULE' => 0,
    'PICKLIST_FIELDS' => 0,
    'FIELD_MODEL' => 0,
    'DEFAULT_FIELD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de7a41156958',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de7a41156958')) {function content_5de7a41156958($_smarty_tpl) {?>



<?php if (!empty($_smarty_tpl->tpl_vars['NO_PICKLIST_FIELDS']->value)){?><label style="padding-top: 40px;"> <b><?php echo vtranslate($_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
 <?php echo vtranslate('NO_PICKLIST_FIELDS',$_smarty_tpl->tpl_vars['QUALIFIED_NAME']->value);?>
. &nbsp;<?php if (!empty($_smarty_tpl->tpl_vars['CREATE_PICKLIST_URL']->value)){?><a href="<?php echo $_smarty_tpl->tpl_vars['CREATE_PICKLIST_URL']->value;?>
"><?php echo vtranslate('LBL_CREATE_NEW',$_smarty_tpl->tpl_vars['QUALIFIED_NAME']->value);?>
</a><?php }?></b></label><?php }else{ ?><div class="row form-group"><div class="col-lg-3 col-md-3 col-sm-3 control-label fieldLabel"><label class="fieldLabel"><strong><?php echo vtranslate('LBL_SELECT_PICKLIST_IN',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></label></div><div class="col-sm-3 col-xs-3 fieldValue"><select class="select2 inputElement" id="modulePickList" name="modulePickList"><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_FIELD'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_FIELD']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['DEFAULT_FIELD']->value==$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()){?> selected <?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
</option><?php } ?></select></div></div><br><?php }?>
<?php }} ?>