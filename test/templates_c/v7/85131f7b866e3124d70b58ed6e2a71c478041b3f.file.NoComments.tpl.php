<?php /* Smarty version Smarty-3.1.7, created on 2019-12-05 09:13:19
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/NoComments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12120154515de8ca2f31c8d8-66771666%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85131f7b866e3124d70b58ed6e2a71c478041b3f' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/NoComments.tpl',
      1 => 1574851712,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12120154515de8ca2f31c8d8-66771666',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de8ca2f327ed',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de8ca2f327ed')) {function content_5de8ca2f327ed($_smarty_tpl) {?>
<div class="noCommentsMsgContainer noContent"><p class="textAlignCenter"> <?php echo vtranslate('LBL_NO_COMMENTS',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</p></div><?php }} ?>