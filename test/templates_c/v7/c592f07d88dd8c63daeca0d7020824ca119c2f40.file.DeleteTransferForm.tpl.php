<?php /* Smarty version Smarty-3.1.7, created on 2021-01-08 05:51:15
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Profiles/DeleteTransferForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9593253415ff7f2d39228b2-99944354%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c592f07d88dd8c63daeca0d7020824ca119c2f40' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/Profiles/DeleteTransferForm.tpl',
      1 => 1574851728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9593253415ff7f2d39228b2-99944354',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'RECORD_MODEL' => 0,
    'MODULE' => 0,
    'HEADER_TITLE' => 0,
    'ALL_RECORDS' => 0,
    'PROFILE_MODEL' => 0,
    'PROFILE_ID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ff7f2d3a4b81',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ff7f2d3a4b81')) {function content_5ff7f2d3a4b81($_smarty_tpl) {?>


<div class="modal-dialog modelContainer"><?php ob_start();?><?php echo vtranslate('LBL_DELETE_PROFILE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getName();?>
<?php $_tmp2=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['HEADER_TITLE'] = new Smarty_variable((($_tmp1).(" - ")).($_tmp2), null, 0);?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['HEADER_TITLE']->value), 0);?>
<div class="modal-content"><form class="form-horizontal" id="DeleteModal" name="AddComment" method="post" action="index.php"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" /><input type="hidden" name="parent" value="Settings" /><input type="hidden" name="action" value="Delete" /><input type="hidden" name="record" id="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getId();?>
" /><div name='massEditContent'><div class="modal-body"><div class="form-group"><label class="control-label fieldLabel col-sm-5"><?php echo vtranslate('LBL_TRANSFER_ROLES_TO_PROFILE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label><div class="controls fieldValue col-xs-6"><select id="transfer_record" name="transfer_record" class="select2 col-xs-9"><optgroup label="<?php echo vtranslate('LBL_PROFILES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><?php  $_smarty_tpl->tpl_vars['PROFILE_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PROFILE_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ALL_RECORDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PROFILE_MODEL']->key => $_smarty_tpl->tpl_vars['PROFILE_MODEL']->value){
$_smarty_tpl->tpl_vars['PROFILE_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['PROFILE_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['PROFILE_MODEL']->value->get('profileid'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['PROFILE_ID']->value!=$_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getId()){?><option value="<?php echo $_smarty_tpl->tpl_vars['PROFILE_ID']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['PROFILE_MODEL']->value->get('profilename');?>
</option><?php }?><?php } ?></optgroup></select></div></div></div></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ModalFooter.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</form></div></div>


<?php }} ?>