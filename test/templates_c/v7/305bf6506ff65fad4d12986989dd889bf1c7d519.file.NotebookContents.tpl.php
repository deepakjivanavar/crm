<?php /* Smarty version Smarty-3.1.7, created on 2022-08-16 13:30:37
         compiled from "C:\xampp\htdocs\CRM\crmuatbkup10-08-2022\crmuat\includes\runtime/../../layouts/v7\modules\nectarcrm\dashboards\NotebookContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:179980113662fb9bfd7227d4-77440088%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '305bf6506ff65fad4d12986989dd889bf1c7d519' => 
    array (
      0 => 'C:\\xampp\\htdocs\\CRM\\crmuatbkup10-08-2022\\crmuat\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\dashboards\\NotebookContents.tpl',
      1 => 1660294246,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '179980113662fb9bfd7227d4-77440088',
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
  'unifunc' => 'content_62fb9bfd72d85',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62fb9bfd72d85')) {function content_62fb9bfd72d85($_smarty_tpl) {?>
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