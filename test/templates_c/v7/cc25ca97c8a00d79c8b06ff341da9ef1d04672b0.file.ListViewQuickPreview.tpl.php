<?php /* Smarty version Smarty-3.1.7, created on 2020-10-29 04:29:56
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Reports/ListViewQuickPreview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7319066945f9a4544b426d9-33104776%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cc25ca97c8a00d79c8b06ff341da9ef1d04672b0' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Reports/ListViewQuickPreview.tpl',
      1 => 1574851720,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7319066945f9a4544b426d9-33104776',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'REPORT_MODEL' => 0,
    'CHART_TYPE' => 0,
    'DATA' => 0,
    'CLICK_THROUGH' => 0,
    'CLASS_NAME' => 0,
    'RECORD_ID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5f9a4544bee43',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f9a4544bee43')) {function content_5f9a4544bee43($_smarty_tpl) {?>
<div class = "quickPreview">
    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class="quickPreviewModuleHeader row">
                <div class = "col-lg-10">
                    <div class="row qp-heading">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="record-header clearfix">
                                <div class="hidden-sm hidden-xs recordImage">
                                    <div class="name"><span class='fa fa-bar-chart'></span></div>
                                </div>
                                <div class="recordBasicInfo">
                                    <div class="info-row">
                                        <h4>
                                            <span class="recordLabel pushDown" title="">
                                                <?php echo $_smarty_tpl->tpl_vars['REPORT_MODEL']->value->get('reportname');?>

                                            </span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class = "col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="<?php echo vtranslate('LBL_CLOSE');?>
">x</button>
                </div>
            </div>
            <div class="quickPreviewActions clearfix">
                <div class="btn-group pull-left">
                </div>
            </div>
            <div class="quickPreviewSummary">
                <input type='hidden' name='charttype' value="<?php echo $_smarty_tpl->tpl_vars['CHART_TYPE']->value;?>
" />
                <input type='hidden' name='data' value='<?php echo nectarcrm_Functions::jsonEncode($_smarty_tpl->tpl_vars['DATA']->value);?>
' />
                <input type='hidden' name='clickthrough' value="<?php echo $_smarty_tpl->tpl_vars['CLICK_THROUGH']->value;?>
" />
                <br>
                <div style="margin:0px 20px;">
                    <div class='border1px' style="padding:30px;">
                        <div id='chartcontent' name='chartcontent' style="min-height:400px;" data-mode='Reports'></div>
                        <br>
                    </div>
                </div>
                <br>
            </div>
            <br>
        </div>
    </div>
</div>
<?php if ($_smarty_tpl->tpl_vars['CHART_TYPE']->value=='pieChart'){?>
    <?php $_smarty_tpl->tpl_vars['CLASS_NAME'] = new Smarty_variable('Report_Piechart_Js', null, 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['CHART_TYPE']->value=='verticalbarChart'){?>
    <?php $_smarty_tpl->tpl_vars['CLASS_NAME'] = new Smarty_variable('Report_Verticalbarchart_Js', null, 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['CHART_TYPE']->value=='horizontalbarChart'){?>
    <?php $_smarty_tpl->tpl_vars['CLASS_NAME'] = new Smarty_variable('Report_Horizontalbarchart_Js', null, 0);?>
<?php }else{ ?>
    <?php $_smarty_tpl->tpl_vars['CLASS_NAME'] = new Smarty_variable('Report_Linechart_Js', null, 0);?>
<?php }?>

<script type="text/javascript">
    <?php echo $_smarty_tpl->tpl_vars['CLASS_NAME']->value;?>
('nectarcrm_ChartReportWidget_<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
',{}, {
        init: function () {
            this._super(jQuery(".quickPreviewSummary"));
        }
    });

    var i = new nectarcrm_ChartReportWidget_<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
();
    jQuery('.quickPreviewSummary').trigger(nectarcrm_Widget_Js.widgetPostLoadEvent);
</script><?php }} ?>