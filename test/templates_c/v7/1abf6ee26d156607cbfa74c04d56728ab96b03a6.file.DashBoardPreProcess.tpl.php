<?php /* Smarty version Smarty-3.1.7, created on 2019-11-27 12:29:39
         compiled from "D:\xamp\htdocs\crm\includes\runtime/../../layouts/v7\modules\nectarcrm\dashboards\DashBoardPreProcess.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4935670385dde40695b2f58-97893137%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1abf6ee26d156607cbfa74c04d56728ab96b03a6' => 
    array (
      0 => 'D:\\xamp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\dashboards\\DashBoardPreProcess.tpl',
      1 => 1574851710,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4935670385dde40695b2f58-97893137',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dde40695f5b8',
  'variables' => 
  array (
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dde40695f5b8')) {function content_5dde40695f5b8($_smarty_tpl) {?>



<?php echo $_smarty_tpl->getSubTemplate ("modules/nectarcrm/partials/Topbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="container-fluid app-nav">
    <div class="row">
        <?php echo $_smarty_tpl->getSubTemplate ("modules/nectarcrm/partials/SidebarHeader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModuleHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</div>
</nav>
 <div id='overlayPageContent' class='fade modal content-area overlayPageContent overlay-container-60' tabindex='-1' role='dialog' aria-hidden='true'>
        <div class="data">
        </div>
        <div class="modal-dialog">
        </div>
    </div>

<?php }} ?>