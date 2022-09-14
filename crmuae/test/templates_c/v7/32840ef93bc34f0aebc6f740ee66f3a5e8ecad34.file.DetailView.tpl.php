<?php /* Smarty version Smarty-3.1.7, created on 2019-12-10 09:58:27
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Portal/DetailView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13924419205def6c43e6fff8-57478345%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32840ef93bc34f0aebc6f740ee66f3a5e8ecad34' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/Portal/DetailView.tpl',
      1 => 1574851716,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13924419205def6c43e6fff8-57478345',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'RECORDS_LIST' => 0,
    'RECORD' => 0,
    'RECORD_ID' => 0,
    'URL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5def6c43ea838',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5def6c43ea838')) {function content_5def6c43ea838($_smarty_tpl) {?>



<div class="listViewPageDiv"><div class="container-fluid"><div class="row"><div class="col-lg-7"></div><div class="col-lg-2" style="padding-top: 14px"><div class="pull-right"><label><?php echo vtranslate('LBL_BOOKMARKS_LIST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label></div></div><div class="col-lg-3" style="padding-top: 10px"><select class="inputElement select2" id="bookmarksDropdown" name="bookmarksList"><?php  $_smarty_tpl->tpl_vars['RECORD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECORDS_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD']->key => $_smarty_tpl->tpl_vars['RECORD']->value){
$_smarty_tpl->tpl_vars['RECORD']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['RECORD']->value['id']==$_smarty_tpl->tpl_vars['RECORD_ID']->value){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['RECORD']->value['portalname'];?>
</option><?php } ?></select></div></div><div class="row"><span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal"><img class="listViewLoadingImage" src="<?php echo vimage_path('loading.gif');?>
" alt="no-image" title="<?php echo vtranslate('LBL_LOADING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"/><p class="listViewLoadingMsg"><?php echo vtranslate('LBL_LOADING_LISTVIEW_CONTENTS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
........</p></span><br><?php if (substr($_smarty_tpl->tpl_vars['URL']->value,0,8)!='https://'){?><div id="portalDetailViewHttpError" class=""><div class="col-lg-12"><?php echo vtranslate('HTTP_ERROR',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div></div><?php }?><br></div><div class="row"><div class="col-lg-12"><iframe src="<?php if (substr($_smarty_tpl->tpl_vars['URL']->value,0,4)!='http'){?>//<?php }?><?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
" frameborder="1" height="600" scrolling="auto" width="100%" style="border: solid 2px; border-color: #dddddd;"></iframe></div></div></div></div>
<?php }} ?>