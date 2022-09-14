<?php /* Smarty version Smarty-3.1.7, created on 2019-12-11 11:01:42
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/NotAccessible.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18015694945df0cc960e5da0-37968677%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76dc8c463619fbd633a8d270dd748d38ccd4aa3d' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/NotAccessible.tpl',
      1 => 1574851712,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18015694945df0cc960e5da0-37968677',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'TITLE' => 0,
    'BODY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5df0cc9610901',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5df0cc9610901')) {function content_5df0cc9610901($_smarty_tpl) {?>
<div id="sendSmsContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['TITLE']->value), 0);?>


        <div class="modal-body">
        	<?php echo $_smarty_tpl->tpl_vars['BODY']->value;?>

    	</div>

    	<div class="modal-footer">
    	</div>
    </div>
</div><?php }} ?>