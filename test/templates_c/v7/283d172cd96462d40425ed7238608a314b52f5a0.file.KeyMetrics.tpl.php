<?php /* Smarty version Smarty-3.1.7, created on 2022-08-08 05:27:29
         compiled from "/home/crmotakuneeds/public_html/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/KeyMetrics.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111319579562f09ec163d665-87259869%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '283d172cd96462d40425ed7238608a314b52f5a0' => 
    array (
      0 => '/home/crmotakuneeds/public_html/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/KeyMetrics.tpl',
      1 => 1655151956,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111319579562f09ec163d665-87259869',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_62f09ec165663',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62f09ec165663')) {function content_62f09ec165663($_smarty_tpl) {?>
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