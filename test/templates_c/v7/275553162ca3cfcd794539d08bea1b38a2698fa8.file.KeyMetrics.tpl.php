<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 11:49:36
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/KeyMetrics.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4640109205de79d50c6c269-67115581%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '275553162ca3cfcd794539d08bea1b38a2698fa8' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/KeyMetrics.tpl',
      1 => 1574851710,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4640109205de79d50c6c269-67115581',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de79d50c7991',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de79d50c7991')) {function content_5de79d50c7991($_smarty_tpl) {?>
<div class="dashboardWidgetHeader">
	<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/WidgetHeader.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>

<div class="dashboardWidgetContent">
	<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/KeyMetricsContents.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>

<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/DashboardFooterIcons.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</div><?php }} ?>