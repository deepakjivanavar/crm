<?php /* Smarty version Smarty-3.1.7, created on 2019-10-21 07:17:24
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\Settings\nectarcrm\TaxRegions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18939739705dad5b84459e92-89939635%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb82b6807cffbf364b5437cca836d69a14ba75aa' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\Settings\\nectarcrm\\TaxRegions.tpl',
      1 => 1520586670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18939739705dad5b84459e92-89939635',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'CURRENT_USER_MODEL' => 0,
    'QUALIFIED_MODULE' => 0,
    'WIDTHTYPE' => 0,
    'TAX_REGIONS' => 0,
    'TAX_REGION_MODEL' => 0,
    'TAX_REGION_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dad5b8448b15',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dad5b8448b15')) {function content_5dad5b8448b15($_smarty_tpl) {?>


<div class="taxRegionsContainer"><div class="tab-pane active"><div class="tab-content overflowVisible"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><div class="col-lg-4 marginLeftZero textOverflowEllipsis"><div class="marginBottom10px"><button type="button" class="btn btn-default addRegion addButton module-buttons" data-url="?module=nectarcrm&parent=Settings&view=TaxAjax&mode=editTaxRegion" data-type="1"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo vtranslate('LBL_ADD_NEW_REGION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button></div><table class="table table-bordered taxRegionsTable" style="table-layout: fixed"><tr><th class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" colspan="2"><strong><?php echo vtranslate('LBL_AVAILABLE_REGIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></th><tr><?php  $_smarty_tpl->tpl_vars['TAX_REGION_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['TAX_REGIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->key => $_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->value){
$_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['TAX_REGION_NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->value->getName(), null, 0);?><tr class="opacity" data-key-name="<?php echo $_smarty_tpl->tpl_vars['TAX_REGION_NAME']->value;?>
" data-key="<?php echo $_smarty_tpl->tpl_vars['TAX_REGION_NAME']->value;?>
"><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" style="border-right:none;border-left:none;"><span class="taxRegionName"><?php echo $_smarty_tpl->tpl_vars['TAX_REGION_NAME']->value;?>
</span></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" style="border-right:none;border-left:none"><div class="pull-right actions"><a class="editRegion" data-url='<?php echo $_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->value->getEditRegionUrl();?>
'><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
" class="fa fa-pencil alignMiddle"></i></a>&nbsp;&nbsp;<a class="deleteRegion" data-url='<?php echo $_smarty_tpl->tpl_vars['TAX_REGION_MODEL']->value->getDeleteRegionUrl();?>
'><i title="<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
" class="fa fa-trash alignMiddle"></i></a></div></td></tr><?php } ?></table></div><div class="col-lg-2">&nbsp;</div><div class="col-lg-7"><br><br><br><div class=""><div class="col-lg-1"><i class="fa fa-info-circle"></i></div><div class="col-lg-11"><?php echo vtranslate('LBL_TAX_REGION_DESC',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</div></div></div></div></div></div><?php }} ?>