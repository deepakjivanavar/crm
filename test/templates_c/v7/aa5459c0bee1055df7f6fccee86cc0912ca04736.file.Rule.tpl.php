<?php /* Smarty version Smarty-3.1.7, created on 2020-04-09 09:34:13
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/MailConverter/Rule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13314473915e8eec15c3f8e6-40838599%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa5459c0bee1055df7f6fccee86cc0912ca04736' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Settings/MailConverter/Rule.tpl',
      1 => 1574851724,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13314473915e8eec15c3f8e6-40838599',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'RULE_COUNT' => 0,
    'RULE_MODEL' => 0,
    'ACTION_LINK' => 0,
    'MODULE' => 0,
    'FIELDS' => 0,
    'FIELD_MODEL' => 0,
    'FIELD_NAME' => 0,
    'FIELD_VALUE' => 0,
    'ASSIGNED_TO_RULES_ARRAY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5e8eec15c903e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5e8eec15c903e')) {function content_5e8eec15c903e($_smarty_tpl) {?>

<div class="mailConverterRuleBlock"><div class="details border1px"><div class="ruleHead modal-header" style="cursor: move; min-height: 30px; padding: 10px 0px;"><strong><img class="alignMiddle" src="<?php echo vimage_path('white-drag.png');?>
" style="margin-left: 10px;" />&nbsp;&nbsp;<?php echo vtranslate('LBL_RULE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<span class="sequenceNumber"><?php echo $_smarty_tpl->tpl_vars['RULE_COUNT']->value;?>
</span>&nbsp;:&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('action'),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<div class="pull-right" style="padding-right: 10px;"><?php  $_smarty_tpl->tpl_vars['ACTION_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ACTION_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RULE_MODEL']->value->getRecordLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ACTION_LINK']->key => $_smarty_tpl->tpl_vars['ACTION_LINK']->value){
$_smarty_tpl->tpl_vars['ACTION_LINK']->_loop = true;
?><span <?php if (stripos($_smarty_tpl->tpl_vars['ACTION_LINK']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['ACTION_LINK']->value->getUrl(),strlen("javascript:"));?>
'<?php }else{ ?>onclick='window.location.href = "<?php echo $_smarty_tpl->tpl_vars['ACTION_LINK']->value->getUrl();?>
"'<?php }?>><i title="<?php echo vtranslate($_smarty_tpl->tpl_vars['ACTION_LINK']->value->get('linklabel'),$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="<?php echo $_smarty_tpl->tpl_vars['ACTION_LINK']->value->get('linkicon');?>
 alignMiddle cursorPointer"></i></span>&nbsp;&nbsp;<?php } ?></div></strong></div><fieldset class="marginTop10px"><strong class="marginLeft10px"><?php echo vtranslate('LBL_CONDITIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong><hr><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><div class="col-lg-12 padding10"><div class="col-lg-1"></div><div class="col-lg-3 fieldLabel"><label><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></div><div class="col-lg-7 fieldValue"><?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value!='action'&&$_smarty_tpl->tpl_vars['FIELD_NAME']->value!='assigned_to'){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='matchusing'){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable(vtranslate('LBL_ANY_CONDITIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('matchusing')=='AND'){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable(vtranslate('LBL_ALL_CONDITIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value), null, 0);?><?php }?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='subject'){?><?php echo vtranslate($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('subjectop'));?>
&nbsp;&nbsp;&nbsp;<?php }elseif($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='body'){?><?php echo vtranslate($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('bodyop'));?>
&nbsp;&nbsp;&nbsp;<?php }?><?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
<?php }?></div></div><?php } ?><?php $_smarty_tpl->tpl_vars['ASSIGNED_TO_RULES_ARRAY'] = new Smarty_variable(array('CREATE_HelpDesk_FROM','CREATE_Leads_SUBJECT','CREATE_Contacts_SUBJECT','CREATE_Accounts_SUBJECT'), null, 0);?><?php if (in_array($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('action'),$_smarty_tpl->tpl_vars['ASSIGNED_TO_RULES_ARRAY']->value)){?><div class="col-lg-12 padding10"><div class="col-lg-1"></div><div class="col-lg-3 fieldLabel"><label><?php echo vtranslate('Assigned To');?>
</label></div><div class="col-lg-7 fieldValue"><?php echo $_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('assigned_to');?>
</div></div><?php }?></fieldset><hr><fieldset class="marginTop10px"><strong class="marginLeft10px"><?php echo vtranslate('LBL_ACTIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong><hr><div class="col-lg-12 padding10" style="padding-bottom: 10px;"><div class="col-lg-1"></div><div class="col-lg-3 fieldLabel"><label><?php echo vtranslate('action',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></div><div class="col-lg-7 fieldValue"><?php echo vtranslate($_smarty_tpl->tpl_vars['RULE_MODEL']->value->get('action'),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</small></div></div></fieldset></div></div><br>
<?php }} ?>