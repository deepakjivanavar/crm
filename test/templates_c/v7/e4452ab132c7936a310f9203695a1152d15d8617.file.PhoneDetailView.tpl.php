<?php /* Smarty version Smarty-3.1.7, created on 2022-08-08 05:31:10
         compiled from "/home/crmotakuneeds/public_html/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/uitypes/PhoneDetailView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85040734562f09f9e2893d2-37208017%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e4452ab132c7936a310f9203695a1152d15d8617' => 
    array (
      0 => '/home/crmotakuneeds/public_html/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/uitypes/PhoneDetailView.tpl',
      1 => 1655151952,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85040734562f09f9e2893d2-37208017',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'FIELD_MODEL' => 0,
    'MODULEMODEL' => 0,
    'FIELD_VALUE' => 0,
    'PERMISSION' => 0,
    'PHONE_FIELD_VALUE' => 0,
    'PHONE_NUMBER' => 0,
    'RECORD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_62f09f9e29c9e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62f09f9e29c9e')) {function content_62f09f9e29c9e($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_regex_replace')) include '/home/crmotakuneeds/public_html/crmuat/libraries/Smarty/libs/plugins/modifier.regex_replace.php';
?>

<?php $_smarty_tpl->tpl_vars['MODULE'] = new Smarty_variable('PBXManager', null, 0);?>
<?php $_smarty_tpl->tpl_vars['MODULEMODEL'] = new Smarty_variable(nectarcrm_Module_Model::getInstance($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?>
<?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['MODULEMODEL']->value&&$_smarty_tpl->tpl_vars['MODULEMODEL']->value->isActive()&&$_smarty_tpl->tpl_vars['FIELD_VALUE']->value){?>
    <?php $_smarty_tpl->tpl_vars['PERMISSION'] = new Smarty_variable(PBXManager_Server_Model::checkPermissionForOutgoingCall(), null, 0);?>
    <?php if ($_smarty_tpl->tpl_vars['PERMISSION']->value){?>
        <?php $_smarty_tpl->tpl_vars['PHONE_FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_VALUE']->value, null, 0);?>
        <?php $_smarty_tpl->tpl_vars['PHONE_NUMBER'] = new Smarty_variable(smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['PHONE_FIELD_VALUE']->value,"/[-()\s]/",''), null, 0);?>
        <a class="phoneField" data-value="<?php echo $_smarty_tpl->tpl_vars['PHONE_NUMBER']->value;?>
" record="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
" onclick="nectarcrm_PBXManager_Js.registerPBXOutboundCall('<?php echo $_smarty_tpl->tpl_vars['PHONE_NUMBER']->value;?>
',<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
)"><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
</a>
    <?php }else{ ?>
        <?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);?>

    <?php }?>
<?php }else{ ?>
    <?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);?>

<?php }?>
<?php }} ?>