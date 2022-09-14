<?php /* Smarty version Smarty-3.1.7, created on 2019-11-07 12:15:34
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\Google\ContactsSyncSettings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20506643305dc40ae655ff19-76459534%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a2bb68167311530dafa27ec736864a6b0c88327' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\Google\\ContactsSyncSettings.tpl',
      1 => 1571119996,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20506643305dc40ae655ff19-76459534',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'HEADER_TITLE' => 0,
    'MODULENAME' => 0,
    'SOURCE_MODULE' => 0,
    'GOOGLE_FIELDS' => 0,
    'NECTARCRM_EMAIL_FIELDS' => 0,
    'NECTARCRM_PHONE_FIELDS' => 0,
    'NECTARCRM_URL_FIELDS' => 0,
    'NECTARCRM_OTHER_FIELDS' => 0,
    'FLDNAME' => 0,
    'GOOGLE_TYPES' => 0,
    'TYPE' => 0,
    'FIELD_MAPPING' => 0,
    'CUSTOM_FIELD_MAPPING' => 0,
    'CUSTOM_FIELD_MAP' => 0,
    'EMAIL_FIELD_NAME' => 0,
    'NECTARCRM_FIELD_NAME' => 0,
    'EMAIL_FIELD_LABEL' => 0,
    'PHONE_FIELD_NAME' => 0,
    'PHONE_FIELD_LABEL' => 0,
    'OTHER_FIELD_NAME' => 0,
    'OTHER_FIELD_LABEL' => 0,
    'URL_FIELD_NAME' => 0,
    'URL_FIELD_LABEL' => 0,
    'BUTTON_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dc40ae6728ff',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dc40ae6728ff')) {function content_5dc40ae6728ff($_smarty_tpl) {?>
<div class="modal-dialog modal-lg googleSettings" style="min-width: 800px;"><div class="modal-content" ><?php ob_start();?><?php echo vtranslate('LBL_FIELD_MAPPING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['HEADER_TITLE'] = new Smarty_variable($_tmp1, null, 0);?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['HEADER_TITLE']->value), 0);?>
<form class="form-horizontal" name="contactsyncsettings"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULENAME']->value;?>
" /><input type="hidden" name="action" value="SaveSettings" /><input type="hidden" name="sourcemodule" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" /><input id="user_field_mapping" type="hidden" name="fieldmapping" value="fieldmappings" /><input id="google_fields" type="hidden" value='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value);?>
' /><div class="modal-body"><div class="row"><div class="col-sm-12 col-xs-12"><div class="pull-right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class="btn-group pull-right"><button id="googlesync_addcustommapping" class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret"></span>&nbsp;<?php echo vtranslate('LBL_ADD_CUSTOM_FIELD_MAPPING',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</button><ul class="dropdown-menu dropdown-menu-left" role="menu"><li class="addCustomFieldMapping" data-type="email" data-nectarcrmfields='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['NECTARCRM_EMAIL_FIELDS']->value);?>
'><a><?php echo vtranslate('LBL_EMAIL',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</a></li><li class="addCustomFieldMapping" data-type="phone" data-nectarcrmfields='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['NECTARCRM_PHONE_FIELDS']->value);?>
'><a><?php echo vtranslate('LBL_PHONE',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</a></li><li class="addCustomFieldMapping" data-type="url" data-nectarcrmfields='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['NECTARCRM_URL_FIELDS']->value);?>
'><a><?php echo vtranslate('LBL_URL',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</a></li><li class="divider"></li><li class="addCustomFieldMapping" data-type="custom" data-nectarcrmfields='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['NECTARCRM_OTHER_FIELDS']->value);?>
'><a><?php echo vtranslate('LBL_CUSTOM',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</a></li></ul></div></div></div><div id="googlesyncfieldmapping" style="margin:15px;"><table  class="table table-bordered"><thead><tr><td><b><?php echo vtranslate('APPTITLE',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</b></td><td><b><?php echo vtranslate('EXTENTIONNAME',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</b></td></tr></thead><tbody><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("salutationtype", null, 0);?><td><?php echo vtranslate('Salutation',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('Name Prefix',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("firstname", null, 0);?><td><?php echo vtranslate('First Name',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('First Name',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("lastname", null, 0);?><td><?php echo vtranslate('Last Name',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('Last Name',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("title", null, 0);?><td><?php echo vtranslate('Title',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('Job Title',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("account_id", null, 0);?><td><?php echo vtranslate('Organization Name',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('Company',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['organizationname']['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("birthday", null, 0);?><td><?php echo vtranslate('Date of Birth',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><?php echo vtranslate('Birthday',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("email", null, 0);?><td><?php echo vtranslate('Email',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['email']['name'];?>
" /><?php $_smarty_tpl->tpl_vars["GOOGLE_TYPES"] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['types'], null, 0);?><select class="select2 google-type col-sm-5" data-category="email"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
<?php $_tmp2=ob_get_clean();?><?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_tmp2]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Email',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true" /></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("secondaryemail", null, 0);?><td><?php echo vtranslate('Secondary Email',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['email']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['email']['types'], null, 0);?><select class="select2 google-type col-sm-5" data-category="email"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value['secondaryemail']['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Email',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("mobile", null, 0);?><td><?php echo vtranslate('Mobile Phone',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['types'], null, 0);?><select class="select2 stretched google-type col-sm-5" data-category="phone"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Phone',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("phone", null, 0);?><td><?php echo vtranslate('Office Phone',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['types'], null, 0);?><select class="select2 stretched google-type col-sm-5" data-category="phone"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Phone',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("homephone", null, 0);?><td><?php echo vtranslate('Home Phone',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
" /></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['types'], null, 0);?><select class="select2 stretched google-type col-sm-5" data-category="phone"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Phone',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("mailingaddress", null, 0);?><td><?php echo vtranslate('Mailing Address',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
"></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['address']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['address']['types'], null, 0);?><select class="select2 stretched google-type col-sm-5" data-category="address"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Address',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("otheraddress", null, 0);?><td><?php echo vtranslate('Other Address',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
"></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['address']['name'];?>
" /><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['address']['types'], null, 0);?><select class="select2 stretched google-type col-sm-5" data-category="address"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Address',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['FIELD_MAPPING']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['google_custom_label'];?>
<?php }?>"data-rule-required="true"/></td></tr><tr><?php $_smarty_tpl->tpl_vars['FLDNAME'] = new Smarty_variable("description", null, 0);?><td><?php echo vtranslate('Description',$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<input type="hidden" class="nectarcrm_field_name" value="<?php echo $_smarty_tpl->tpl_vars['FLDNAME']->value;?>
"></td><td><?php echo vtranslate('Note',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
<input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value[$_smarty_tpl->tpl_vars['FLDNAME']->value]['name'];?>
" /></td></tr><?php  $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->_loop = false;
 $_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAPPING']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->key => $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value){
$_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->_loop = true;
 $_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->key;
?><tr><td><?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gd:email'){?><select class="select2 stretched nectarcrm_field_name col-sm-12" data-category="email"><?php  $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['NECTARCRM_EMAIL_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME']->value==$_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value){?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></select><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gd:phoneNumber'){?><select class="select2 stretched nectarcrm_field_name col-sm-12" data-category="phone"><?php  $_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['PHONE_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['NECTARCRM_PHONE_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['PHONE_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PHONE_FIELD_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME']->value==$_smarty_tpl->tpl_vars['PHONE_FIELD_NAME']->value){?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['PHONE_FIELD_LABEL']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></select><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gContact:userDefinedField'){?><select class="select2 stretched nectarcrm_field_name col-sm-12" data-category="custom"><?php  $_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['OTHER_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['NECTARCRM_OTHER_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['OTHER_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['OTHER_FIELD_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME']->value==$_smarty_tpl->tpl_vars['OTHER_FIELD_NAME']->value){?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['OTHER_FIELD_LABEL']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></select><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gContact:website'){?><select class="select2 stretched nectarcrm_field_name col-sm-12" data-category="url"><?php  $_smarty_tpl->tpl_vars['URL_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['URL_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['NECTARCRM_URL_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['URL_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['URL_FIELD_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['NECTARCRM_FIELD_NAME']->value==$_smarty_tpl->tpl_vars['URL_FIELD_NAME']->value){?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['URL_FIELD_LABEL']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></select><?php }?></td><td><input type="hidden" class="google_field_name" value="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name'];?>
" /><?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gd:email'){?><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['email']['types'], null, 0);?><select class="select2 google-type col-sm-5" data-category="email"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Email',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_custom_label'];?>
<?php }?>" data-rule-required="true"/><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gd:phoneNumber'){?><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['phone']['types'], null, 0);?><select class="select2 google-type col-sm-5" data-category="phone"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('Phone',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_custom_label'];?>
<?php }?>" data-rule-required="true"/><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gContact:userDefinedField'){?><input type="hidden" class="google-type" value="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type'];?>
"><input type="text" class="google-custom-label inputElement" value="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_custom_label'];?>
" style="width:40%;" data-rule-required="true"/><?php }elseif($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_name']=='gContact:website'){?><?php $_smarty_tpl->tpl_vars['GOOGLE_TYPES'] = new Smarty_variable($_smarty_tpl->tpl_vars['GOOGLE_FIELDS']->value['url']['types'], null, 0);?><select class="select2 google-type col-sm-5" data-category="url"><?php  $_smarty_tpl->tpl_vars['TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TYPE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GOOGLE_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TYPE']->key => $_smarty_tpl->tpl_vars['TYPE']->value){
$_smarty_tpl->tpl_vars['TYPE']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']==$_smarty_tpl->tpl_vars['TYPE']->value){?>selected<?php }?>><?php echo vtranslate('URL',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
 (<?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
)</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="google-custom-label inputElement" style="visibility:<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']!='custom'){?>hidden<?php }else{ ?>visible<?php }?>;width:40%;"value="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_field_type']=='custom'){?><?php echo $_smarty_tpl->tpl_vars['CUSTOM_FIELD_MAP']->value['google_custom_label'];?>
<?php }?>" data-rule-required="true"/><?php }?><a class="deleteCustomMapping marginTop7px pull-right"><i title="Delete" class="fa fa-trash"></i></a></td></tr><?php } ?></tbody></table><br><br><br></div><div id="scroller_wrapper" class="bottom-fixed-scroll"><div id="scroller" class="scroller-div"></div></div></div></form><div class="modal-footer "><center><?php if ($_smarty_tpl->tpl_vars['BUTTON_NAME']->value!=null){?><?php $_smarty_tpl->tpl_vars['BUTTON_LABEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['BUTTON_NAME']->value, null, 0);?><?php }else{ ?><?php ob_start();?><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp3=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['BUTTON_LABEL'] = new Smarty_variable($_tmp3, null, 0);?><?php }?><button id="save_syncsetting" class="btn btn-success" name="saveButton"><strong><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULENAME']->value);?>
</strong></button><a href="#" class="cancelLink" type="reset" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></center></div></div></div><?php }} ?>