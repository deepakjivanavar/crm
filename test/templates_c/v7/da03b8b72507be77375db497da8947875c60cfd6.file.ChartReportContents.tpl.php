<?php /* Smarty version Smarty-3.1.7, created on 2020-04-02 08:20:10
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Reports/ChartReportContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16729285195e85a03a266b46-45603228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'da03b8b72507be77375db497da8947875c60cfd6' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Reports/ChartReportContents.tpl',
      1 => 1574851720,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16729285195e85a03a266b46-45603228',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'CHART_TYPE' => 0,
    'DATA' => 0,
    'CLICK_THROUGH' => 0,
    'YAXIS_FIELD_TYPE' => 0,
    'MODULE' => 0,
    'REPORT_MODEL' => 0,
    'BASE_CURRENCY_INFO' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5e85a03a28fc2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5e85a03a28fc2')) {function content_5e85a03a28fc2($_smarty_tpl) {?>

<input type='hidden' name='charttype' value="<?php echo $_smarty_tpl->tpl_vars['CHART_TYPE']->value;?>
" />
<input type='hidden' name='data' value='<?php echo nectarcrm_Functions::jsonEncode($_smarty_tpl->tpl_vars['DATA']->value);?>
' />
<input type='hidden' name='clickthrough' value="<?php echo $_smarty_tpl->tpl_vars['CLICK_THROUGH']->value;?>
" />

<br>
<div class="dashboardWidgetContent">
    <input type="hidden" class="yAxisFieldType" value="<?php echo $_smarty_tpl->tpl_vars['YAXIS_FIELD_TYPE']->value;?>
" />
    <div class='border1px filterConditionContainer' style="padding:30px;">
        <div id='chartcontent' name='chartcontent' style="min-height:400px;" data-mode='Reports'></div>
        <br>
        <?php if ($_smarty_tpl->tpl_vars['CLICK_THROUGH']->value!='true'){?>
            <div class='row-fluid alert-info'>
                <span class='col-lg-4 offset4'> &nbsp;</span>
                <span class='span alert-info'>
                    <i class="icon-info-sign"></i>
                    <?php echo vtranslate('LBL_CLICK_THROUGH_NOT_AVAILABLE',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                </span>
            </div>
            <br>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['REPORT_MODEL']->value->isInventoryModuleSelected()){?>
            <div class="alert alert-info">
                <?php $_smarty_tpl->tpl_vars['BASE_CURRENCY_INFO'] = new Smarty_variable(nectarcrm_Util_Helper::getUserCurrencyInfo(), null, 0);?>
                <i class="icon-info-sign" style="margin-top: 1px;"></i>&nbsp;&nbsp;
                <?php echo vtranslate('LBL_CALCULATION_CONVERSION_MESSAGE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 - <?php echo $_smarty_tpl->tpl_vars['BASE_CURRENCY_INFO']->value['currency_name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['BASE_CURRENCY_INFO']->value['currency_code'];?>
)
            </div>
        <?php }?>
    </div>
</div>
<br>

<?php }} ?>