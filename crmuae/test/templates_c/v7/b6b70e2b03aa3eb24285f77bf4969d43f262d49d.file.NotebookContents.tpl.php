<?php /* Smarty version Smarty-3.1.7, created on 2019-12-04 11:49:36
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/NotebookContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8367128675de79d5073dcf6-21388114%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b6b70e2b03aa3eb24285f77bf4969d43f262d49d' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/dashboards/NotebookContents.tpl',
      1 => 1574851710,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8367128675de79d5073dcf6-21388114',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'WIDGET' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5de79d5074bd6',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5de79d5074bd6')) {function content_5de79d5074bd6($_smarty_tpl) {?>
<div style='padding:5px'><div class="row"><div class="dashboard_notebookWidget_view" style="word-break: break-all"><div class=""><span class="col-lg-10"><i><?php echo vtranslate('LBL_LAST_SAVED_ON',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</i> <?php echo nectarcrm_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['WIDGET']->value->getLastSavedDate());?>
</span><span class="col-lg-2"><span class="pull-right"><button class="btn btn-default btn-sm pull-right dashboard_notebookWidget_edit"><strong><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span></span></div><br><br><div class="pushDown2per col-lg-12"><div class="dashboard_notebookWidget_viewarea boxSizingBorderBox"><?php echo nl2br($_smarty_tpl->tpl_vars['WIDGET']->value->getContent());?>
</div></div></div><div class="dashboard_notebookWidget_text" style="display:none;"><div class=""><span class="col-lg-10"><i><?php echo vtranslate('LBL_LAST_SAVED_ON',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</i> <?php echo nectarcrm_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['WIDGET']->value->getLastSavedDate());?>
</span><span class="col-lg-2"><span class="pull-right"><button class="btn btn-mini btn-success pull-right dashboard_notebookWidget_save"><strong><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span></span></div><br><br><div class=""><span class="col-lg-12"><textarea class="dashboard_notebookWidget_textarea boxSizingBorderBox" data-note-book-id="<?php echo $_smarty_tpl->tpl_vars['WIDGET']->value->get('id');?>
"><?php echo $_smarty_tpl->tpl_vars['WIDGET']->value->getContent();?>
</textarea></span></div></div></div></div>
<?php }} ?>