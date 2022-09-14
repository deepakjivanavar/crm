<?php /* Smarty version Smarty-3.1.7, created on 2019-10-22 06:09:08
         compiled from "C:\xampp\htdocs\crm\includes\runtime/../../layouts/v7\modules\EmailTemplates\GridViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11879008495dae9d04436161-93794220%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '699ca481257e34ab421b6c1cdd69b46955158045' => 
    array (
      0 => 'C:\\xampp\\htdocs\\crm\\includes\\runtime/../../layouts/v7\\modules\\EmailTemplates\\GridViewContents.tpl',
      1 => 1571119994,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11879008495dae9d04436161-93794220',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'VIEW' => 0,
    'VIEWID' => 0,
    'PAGING_MODEL' => 0,
    'MODULE_MODEL' => 0,
    'OPERATOR' => 0,
    'LISTVIEW_COUNT' => 0,
    'PAGE_NUMBER' => 0,
    'LISTVIEW_ENTRIES_COUNT' => 0,
    'SEARCH_DETAILS' => 0,
    'NO_SEARCH_PARAMS_CACHE' => 0,
    'ORDER_BY' => 0,
    'SORT_ORDER' => 0,
    'LIST_HEADER_FIELDS' => 0,
    'CURRENT_TAG' => 0,
    'FOLDER_ID' => 0,
    'FOLDER_VALUE' => 0,
    'VIEWTYPE' => 0,
    'SEARCH_MODE_RESULTS' => 0,
    'LISTVIEW_ENTRIES' => 0,
    'LISTVIEW_ENTRY' => 0,
    'TEMPLATE_NAME' => 0,
    'TEMPLATE_ID' => 0,
    'TEMPLATE_PATH' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5dae9d044a9b5',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5dae9d044a9b5')) {function content_5dae9d044a9b5($_smarty_tpl) {?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("PicklistColorMap.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="col-sm-12 col-xs-12 ">

	<input type="hidden" name="view" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" />
	<input type="hidden" name="cvid" value="<?php echo $_smarty_tpl->tpl_vars['VIEWID']->value;?>
" />
	<input type="hidden" name="pageStartRange" id="pageStartRange" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getRecordStartRange();?>
" />
	<input type="hidden" name="pageEndRange" id="pageEndRange" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getRecordEndRange();?>
" />
	<input type="hidden" name="previousPageExist" id="previousPageExist" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isPrevPageExists();?>
" />
	<input type="hidden" name="nextPageExist" id="nextPageExist" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists();?>
" />
	<input type="hidden" name="alphabetSearchKey" id="alphabetSearchKey" value= "<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getAlphabetSearchField();?>
" />
	<input type="hidden" name="Operator" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" />
	<input type="hidden" name="totalCount" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_COUNT']->value;?>
" />
	<input type='hidden' name="pageNumber" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'>
	<input type='hidden' name="pageLimit" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'>
	<input type="hidden" name="noOfEntries" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES_COUNT']->value;?>
" id="noOfEntries">
	<input type="hidden" name="currentSearchParams" value="<?php echo nectarcrm_Util_Helper::toSafeHTML(Zend_JSON::encode($_smarty_tpl->tpl_vars['SEARCH_DETAILS']->value));?>
" id="currentSearchParams" />
	<input type="hidden" name="noFilterCache" value="<?php echo $_smarty_tpl->tpl_vars['NO_SEARCH_PARAMS_CACHE']->value;?>
" id="noFilterCache" >
	<input type="hidden" name="orderBy" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy">
	<input type="hidden" name="sortOrder" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder">
	<input type="hidden" name="list_headers" value='<?php echo $_smarty_tpl->tpl_vars['LIST_HEADER_FIELDS']->value;?>
'/>
	<input type="hidden" name="tag" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_TAG']->value;?>
" />
	<input type="hidden" name="folder_id" value="<?php echo $_smarty_tpl->tpl_vars['FOLDER_ID']->value;?>
" />
	<input type="hidden" name="folder_value" value="<?php echo $_smarty_tpl->tpl_vars['FOLDER_VALUE']->value;?>
" />
        <input type="hidden" name="viewType" value="<?php echo $_smarty_tpl->tpl_vars['VIEWTYPE']->value;?>
" />
	<?php if (!$_smarty_tpl->tpl_vars['SEARCH_MODE_RESULTS']->value){?>
		<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ListViewActions.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }?>

	<div class="table-container" style="padding:5px;border: 0px;">
            <ul class="thumbnails">
            <?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
?>
                <?php $_smarty_tpl->tpl_vars["IS_EDITABLE"] = new Smarty_variable($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('systemtemplate'), null, 0);?>
                <?php $_smarty_tpl->tpl_vars["TEMPLATE_PATH"] = new Smarty_variable($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('templatepath'), null, 0);?>
                <?php $_smarty_tpl->tpl_vars["TEMPLATE_NAME"] = new Smarty_variable($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('templatename'), null, 0);?>
                <?php $_smarty_tpl->tpl_vars["TEMPLATE_ID"] = new Smarty_variable($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('templateid'), null, 0);?>
                 <li class="positionRel textCenter">
                    <div class="templateName" title="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
" style="position: relative;margin-bottom: 5px;overflow: hidden;"><p class="ellipsis"><?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
</p></div>
                    <div class="thumbnail cursorPointer positionRel" data-value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_ID']->value;?>
" data-label="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
" style='border: 1px solid #ddd;'>
                            <div class="imageDiv">
                                <img src="test/template_imgfiles/<?php if (!empty($_smarty_tpl->tpl_vars['TEMPLATE_PATH']->value)){?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE_PATH']->value;?>
<?php }else{ ?>default.png<?php }?>"  data-value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_ID']->value;?>
" style="width:216px;height:277px;"/>
                            <div class="hide" style="display: block; width: 100%;position: absolute;bottom:0;">
                                <button class="btn btn-default previewTemplate" style="width:49%;" data-value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_ID']->value;?>
" data-mode="templates" data-label="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
">
                                    <i class="fa fa-eye cursorPointer" title="Preview"></i>&nbsp;Preview
                                </button>
                                <button class="btn btn-default editTemplate" style="width:49%;" data-value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_ID']->value;?>
" data-mode="templates" data-label="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
">
                                    <i class="fa fa-check-circle cursorPointer" title="Edit template" ></i>&nbsp;Select
                                </button>
                            </div>
                    </div>
                </li>
            <?php } ?>
        </div>   
</div><?php }} ?>