<?php /* Smarty version Smarty-3.1.7, created on 2019-12-11 04:10:35
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/SMSNotifier/EditView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2620542095df06c3b6f6868-05660612%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '626ea73a9ac30198726deca60c1bb5a0e59d21e0' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/SMSNotifier/EditView.tpl',
      1 => 1574851728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2620542095df06c3b6f6868-05660612',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RECORD_ID' => 0,
    'QUALIFIED_MODULE_NAME' => 0,
    'MODULE' => 0,
    'EDITABLE_FIELDS' => 0,
    'FIELD_MODEL' => 0,
    'FIELD_NAME' => 0,
    'RECORD_MODEL' => 0,
    'FIELD_TYPE' => 0,
    'FIELD_VALUE' => 0,
    'PROVIDERS' => 0,
    'PROVIDER_MODEL' => 0,
    'PROVIDER_NAME' => 0,
    'PROVIDERS_FIELD_MODELS' => 0,
    'TEMPLATE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5df06c3b77f5e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5df06c3b77f5e')) {function content_5df06c3b77f5e($_smarty_tpl) {?>
<div class="modal-dialog modal-xs"><div class="modal-content"><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value){?><?php ob_start();?><?php echo vtranslate('LBL_EDIT_CONFIGURATION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['TITLE'] = new Smarty_variable($_tmp1, null, 0);?><?php }else{ ?><?php ob_start();?><?php echo vtranslate('LBL_ADD_CONFIGURATION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
<?php $_tmp2=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['TITLE'] = new Smarty_variable($_tmp2, null, 0);?><?php }?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<form class="form-horizontal" id="smsConfig" method="POST"><div class="modal-body configContent"><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value){?><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
" name="record" id="recordId"/><?php }?><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['EDITABLE_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
?><div class="col-lg-12"><?php $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name'), null, 0);?><div class="form-group"><div class = "col-lg-4"><label for="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
</label></div><div class = "col-lg-6"><?php $_smarty_tpl->tpl_vars['FIELD_TYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType(), null, 0);?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_TYPE']->value=='picklist'){?><select <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value&&$_smarty_tpl->tpl_vars['FIELD_NAME']->value=='providertype'){?> disabled="disabled" <?php }?> class="select2 providerType form-control" id="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" placeholder="<?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
"><option></option><?php  $_smarty_tpl->tpl_vars['PROVIDER_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PROVIDERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->key => $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->value){
$_smarty_tpl->tpl_vars['PROVIDER_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['PROVIDER_NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['PROVIDER_MODEL']->value->getName(), null, 0);?><option value="<?php echo $_smarty_tpl->tpl_vars['PROVIDER_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['PROVIDER_NAME']->value){?> selected <?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
</option><?php } ?></select><?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value&&$_smarty_tpl->tpl_vars['FIELD_NAME']->value=='providertype'){?><input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
" /><?php }?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_TYPE']->value=='radio'){?><input type="radio" id="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" value='1' <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value){?> checked="checked" <?php }?> />&nbsp;<?php echo vtranslate('LBL_YES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
&nbsp;&nbsp;&nbsp;<input type="radio" id="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" value='0' <?php if (!$_smarty_tpl->tpl_vars['FIELD_VALUE']->value){?> checked="checked" <?php }?>/>&nbsp;<?php echo vtranslate('LBL_NO',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value);?>
<?php }elseif($_smarty_tpl->tpl_vars['FIELD_TYPE']->value=='password'){?><input type="password" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" class="form-control" id="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
" /><?php }else{ ?><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" class="form-control" id="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='username'){?> <?php }?> value="<?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
" /><?php }?></div></div></div><?php } ?><div id="provider"><?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('providertype')!=''){?><?php  $_smarty_tpl->tpl_vars['PROVIDER_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['PROVIDER_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PROVIDERS_FIELD_MODELS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->key => $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->value){
$_smarty_tpl->tpl_vars['PROVIDER_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['PROVIDER_NAME']->value = $_smarty_tpl->tpl_vars['PROVIDER_MODEL']->key;
?><?php if ($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value==$_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('providertype')){?><div id="<?php echo $_smarty_tpl->tpl_vars['PROVIDER_NAME']->value;?>
_container" class="providerFields"><?php $_smarty_tpl->tpl_vars['TEMPLATE_NAME'] = new Smarty_variable(Settings_SMSNotifier_ProviderField_Model::getEditFieldTemplateName($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value), null, 0);?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_MODEL'=>$_smarty_tpl->tpl_vars['RECORD_MODEL']->value), 0);?>
</div><?php }?><?php } ?><?php }?></div><div><span id='phoneFormatWarning'><i data-trigger="hover" data-toggle ="popover" data-placement="right" id="phoneFormatWarningPop" class="glyphicon glyphicon-info-sign" style="padding-right : 5px; padding-left : 5px" data-original-title="<?php echo vtranslate('LBL_WARNING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-trigger="hover" data-content="<?php echo vtranslate('LBL_PHONEFORMAT_WARNING_CONTENT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></i><?php echo vtranslate('LBL_PHONE_FORMAT_WARNING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></div></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ModalFooter.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</form></div></div>
<?php }} ?>