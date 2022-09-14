<?php /* Smarty version Smarty-3.1.7, created on 2022-08-16 11:05:24
         compiled from "C:\xampp\htdocs\CRM\crmuatbkup10-08-2022\crmuat\includes\runtime/../../layouts/v7\modules\nectarcrm\Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:67020709362fb79f400f5c2-04867227%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '54acfd1beede2f595f912740e5da56750d04af7c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\CRM\\crmuatbkup10-08-2022\\crmuat\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\Footer.tpl',
      1 => 1660294243,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67020709362fb79f400f5c2-04867227',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_62fb79f401783',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62fb79f401783')) {function content_62fb79f401783($_smarty_tpl) {?>


</div>
<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement), 
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['LANGUAGE_STRINGS']->value);?>
</div>
<div class="modal myModal fade"></div>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('JSResources.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>

</html><?php }} ?>