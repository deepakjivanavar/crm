<?php /* Smarty version Smarty-3.1.7, created on 2019-12-06 09:52:29
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Products/BundleCostView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:723310235dea24dd484654-40493429%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7ce772e7b8cc41361d372934e64e1c911402c1f1' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Products/BundleCostView.tpl',
      1 => 1574851718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '723310235dea24dd484654-40493429',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'USER_MODEL' => 0,
    'SUB_PRODUCTS_COSTS_INFO' => 0,
    'SUB_PRODUCT_COST_INFO' => 0,
    'SUB_PRODUCTS_TOTAL_COST' => 0,
    'CALCULATION_INFO' => 0,
    'PARENT_RECORD' => 0,
    'PRODUCT_ACTUAL_PRICE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dea24dd4ca2e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dea24dd4ca2e')) {function content_5dea24dd4ca2e($_smarty_tpl) {?>

<div class ="col-lg-12" style="margin-top:20px;">
    <?php ob_start();?><?php echo ucfirst(vtranslate('LBL_IN',$_smarty_tpl->tpl_vars['MODULE']->value));?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo getCurrencyName($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_id'),false);?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo vtranslate('LBL_PRODUCT_NAME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp3=ob_get_clean();?><?php ob_start();?><?php echo vtranslate('LBL_PRICE_QUANTITY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp4=ob_get_clean();?><?php ob_start();?><?php echo vtranslate('LBL_TOTAL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php  $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->_loop = false;
 $_smarty_tpl->tpl_vars['SUB_PRODUCT_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SUB_PRODUCTS_COSTS_INFO']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->key => $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value){
$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->_loop = true;
 $_smarty_tpl->tpl_vars['SUB_PRODUCT_ID']->value = $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->key;
?><?php echo "<tr><td>";?><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['productName'];?><?php echo "</td><td>";?><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['actualPrice'];?><?php echo " X ";?><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['quantityInBundle'];?><?php echo "</td> <td align='right'> ";?><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['priceInUserFormat'];?><?php echo "</td></tr>";?><?php } ?><?php $_tmp6=ob_get_clean();?><?php ob_start();?><?php echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp7=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['CALCULATION_INFO'] = new Smarty_variable($_tmp1." <strong>".$_tmp2."</strong><br /><br /> <table class = 'table table-bordered'><thead><th width='40%'>".$_tmp3." </th><th width='35%'>".$_tmp4."</th><th width = 25%>".$_tmp5."</th></thead>".$_tmp6."<tr></table><br /><div class = 'pull-right' style = 'padding-right:5px'><strong>".$_tmp7." : ".($_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value)."</strong></div>", null, 0);?>
    <label><?php echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;&nbsp;<a class ="totalCostCalculationInfo" role="button" data-trigger="focus" title = "<?php echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" tabindex="0" data-toggle="popover" data-content="<?php echo $_smarty_tpl->tpl_vars['CALCULATION_INFO']->value;?>
"><span class="subProductsTotalCost"><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value;?>
</span></a>
    </label>
    <?php ob_start();?><?php echo CurrencyField::convertToUserFormat((float)$_smarty_tpl->tpl_vars['PARENT_RECORD']->value->get('unit_price'),'',true,true);?>
<?php $_tmp8=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['PRODUCT_ACTUAL_PRICE'] = new Smarty_variable($_tmp8, null, 0);?>
    &nbsp;&nbsp;
    <button type="button" id ="updatePrice" class="btn btn-sm btn-dark btn-default"
            <?php if ($_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value==$_smarty_tpl->tpl_vars['PRODUCT_ACTUAL_PRICE']->value&&$_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_id')==$_smarty_tpl->tpl_vars['PARENT_RECORD']->value->get('currency_id')){?>
                disabled
            <?php }?>>
        <i class="fa fa-refresh"><strong>&nbsp;&nbsp;<?php echo vtranslate('LBL_UPDATE_BUNDLE_PRICE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></i>
    </button>
</div>
<?php }} ?>