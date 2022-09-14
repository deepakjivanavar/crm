<?php /* Smarty version Smarty-3.1.7, created on 2019-10-15 06:21:13
         compiled from "C:\xampp\htdocs\nectarcrmcrm\includes\runtime/../../layouts/v7\modules\nectarcrm\Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20293263745da5655918aad2-30115795%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e5653015ab2399c742f74864be71a963f2b9f15' => 
    array (
      0 => 'C:\\xampp\\htdocs\\nectarcrmcrm\\includes\\runtime/../../layouts/v7\\modules\\nectarcrm\\Footer.tpl',
      1 => 1520586670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20293263745da5655918aad2-30115795',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'NECTARCRM_VERSION' => 0,
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5da56559192d2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5da56559192d2')) {function content_5da56559192d2($_smarty_tpl) {?>

<footer class="app-footer">
	<p>
		Powered by nectarcrm CRM - <?php echo $_smarty_tpl->tpl_vars['NECTARCRM_VERSION']->value;?>
&nbsp;&nbsp;© 2004 - <?php echo date('Y');?>
&nbsp;&nbsp;
		<a href="//www.nectarcrm.com" target="_blank">nectarcrm</a>&nbsp;|&nbsp;
		<a href="https://www.nectarcrm.com/privacy-policy" target="_blank">Privacy Policy</a>
	</p>
</footer>
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