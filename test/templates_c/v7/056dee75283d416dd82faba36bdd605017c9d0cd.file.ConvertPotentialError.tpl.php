<?php /* Smarty version Smarty-3.1.7, created on 2019-11-25 07:13:36
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\Potentials\ConvertPotentialError.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10946616805ddb7f203a7a66-09961852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '056dee75283d416dd82faba36bdd605017c9d0cd' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\Potentials\\ConvertPotentialError.tpl',
      1 => 1520586670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10946616805ddb7f203a7a66-09961852',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'IS_DUPICATES_FAILURE' => 0,
    'EXCEPTION' => 0,
    'SINGLE_MODULE' => 0,
    'MODULE' => 0,
    'CURRENT_USER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ddb7f2045cf0',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ddb7f2045cf0')) {function content_5ddb7f2045cf0($_smarty_tpl) {?>

<div class="row" style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255);position: relative; z-index: 10000000; padding: 10px; width: 80%; margin: 0 auto; margin-top: 5%;"><div class ="col-lg-1 col-sm-2 col-md-1" style="float: left;"><img src="<?php echo vimage_path('denied.gif');?>
" ></div><div class ="col-lg-11 col-sm-10 col-md-11" nowrap="nowrap"><span class="genHeaderSmall"><?php if ($_smarty_tpl->tpl_vars['IS_DUPICATES_FAILURE']->value){?><span><?php echo $_smarty_tpl->tpl_vars['EXCEPTION']->value;?>
</span><?php }else{ ?><?php $_smarty_tpl->tpl_vars['SINGLE_MODULE'] = new Smarty_variable("SINGLE_".($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><span class="genHeaderSmall"><?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate('CANNOT_CONVERT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<br><ul> <?php echo vtranslate('LBL_FOLLOWING_ARE_POSSIBLE_REASONS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
:<li><?php echo vtranslate('LBL_POTENTIALS_FIELD_MAPPING_INCOMPLETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li><li><?php echo vtranslate('LBL_MANDATORY_FIELDS_ARE_EMPTY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li><?php if ($_smarty_tpl->tpl_vars['EXCEPTION']->value){?><li><?php echo $_smarty_tpl->tpl_vars['EXCEPTION']->value;?>
</li><?php }?></ul></span><?php }?></span><hr><div class="small" align="right" nowrap="nowrap"><?php if (!$_smarty_tpl->tpl_vars['IS_DUPICATES_FAILURE']->value&&$_smarty_tpl->tpl_vars['CURRENT_USER']->value->isAdminUser()){?><a href="index.php?parent=Settings&module=Potentials&view=MappingDetail"><?php echo vtranslate('LBL_POTENTIALS_FIELD_MAPPING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><br><?php }?><a href="javascript:window.history.back();"><?php echo vtranslate('LBL_GO_BACK',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><br></div></div></div>

<?php }} ?>