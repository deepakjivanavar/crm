<?php /* Smarty version Smarty-3.1.7, created on 2019-10-18 06:35:25
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\EmailTemplates\ListViewPreProcess.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21104379665da95d2d7fab61-54838108%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '745635a4fe6b5387eb15a887a5783e0fafede6d7' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\EmailTemplates\\ListViewPreProcess.tpl',
      1 => 1571119994,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21104379665da95d2d7fab61-54838108',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5da95d2d82940',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5da95d2d82940')) {function content_5da95d2d82940($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate ("modules/nectarcrm/partials/Topbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<div class="container-fluid app-nav"><div class="row"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("partials/SidebarHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModuleHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div></div></nav><div id='overlayPageContent' class='fade modal overlayPageContent content-area overlay-container-60' tabindex='-1' role='dialog' aria-hidden='true'><div class="data"></div><div class="modal-dialog"></div></div><div class="main-container main-container-<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"><div id="modnavigator" class="module-nav"><div class="hidden-xs hidden-sm mod-switcher-container"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("partials/Menubar.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div></div><div class="listViewPageDiv content-area full-width" id="listViewContent"><?php }} ?>