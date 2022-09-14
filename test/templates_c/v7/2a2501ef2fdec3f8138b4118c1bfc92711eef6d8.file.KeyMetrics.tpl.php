<?php /* Smarty version Smarty-3.1.7, created on 2022-08-16 11:05:37
         compiled from "C:\xampp\htdocs\CRM\crmuatbkup10-08-2022\crmuat\includes\runtime/../../layouts/v7\modules\nectarcrm\dashboards\KeyMetrics.tpl" */ ?>
<?php /*%%SmartyHeaderCode:198245067462fb7a0109c8b5-86185777%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a2501ef2fdec3f8138b4118c1bfc92711eef6d8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\CRM\\crmuatbkup10-08-2022\\crmuat\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\dashboards\\KeyMetrics.tpl',
      1 => 1660294246,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '198245067462fb7a0109c8b5-86185777',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_62fb7a010c1e4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62fb7a010c1e4')) {function content_62fb7a010c1e4($_smarty_tpl) {?>
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