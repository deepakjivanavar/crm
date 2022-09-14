<?php /* Smarty version Smarty-3.1.7, created on 2019-12-17 07:29:54
         compiled from "/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/SendSMSForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4509328135df883f21ae2e3-59708792%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3816b5c4218f0300d7e7dce003c86bb3f4034c4c' => 
    array (
      0 => '/var/www/crmuat/includes/runtime/../../layouts/v7/modules/nectarcrm/SendSMSForm.tpl',
      1 => 1574851714,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4509328135df883f21ae2e3-59708792',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'TITLE' => 0,
    'SOURCE_MODULE' => 0,
    'VIEWNAME' => 0,
    'SELECTED_IDS' => 0,
    'EXCLUDED_IDS' => 0,
    'SEARCH_KEY' => 0,
    'OPERATOR' => 0,
    'ALPHABET_VALUE' => 0,
    'SEARCH_PARAMS' => 0,
    'PHONE_FIELDS' => 0,
    'PHONE_FIELD' => 0,
    'PHONE_FIELD_NAME' => 0,
    'SINGLE_RECORD' => 0,
    'FIELD_VALUE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5df883f22510b',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5df883f22510b')) {function content_5df883f22510b($_smarty_tpl) {?>


<div id="sendSmsContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        <?php ob_start();?><?php echo vtranslate('LBL_SEND_SMS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['TITLE'] = new Smarty_variable($_tmp1, null, 0);?>
        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['TITLE']->value), 0);?>


        <form class="form-horizontal" id="massSave" method="post" action="index.php">
            <input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" />
            <input type="hidden" name="source_module" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" />
            <input type="hidden" name="action" value="MassSaveAjax" />
            <input type="hidden" name="viewname" value="<?php echo $_smarty_tpl->tpl_vars['VIEWNAME']->value;?>
" />
            <input type="hidden" name="selected_ids" value=<?php echo ZEND_JSON::encode($_smarty_tpl->tpl_vars['SELECTED_IDS']->value);?>
>
            <input type="hidden" name="excluded_ids" value=<?php echo ZEND_JSON::encode($_smarty_tpl->tpl_vars['EXCLUDED_IDS']->value);?>
>
            <input type="hidden" name="search_key" value= "<?php echo $_smarty_tpl->tpl_vars['SEARCH_KEY']->value;?>
" />
            <input type="hidden" name="operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" />
            <input type="hidden" name="search_value" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" />
            <input type="hidden" name="search_params" value='<?php echo ZEND_JSON::encode($_smarty_tpl->tpl_vars['SEARCH_PARAMS']->value);?>
' />
            
            <div class="modal-body">
                <div>
                    <span><strong><?php echo vtranslate('LBL_STEP_1',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></span>
                    &nbsp;:&nbsp;
                    <?php echo vtranslate('LBL_SELECT_THE_PHONE_NUMBER_FIELDS_TO_SEND',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                </div>
                <br>
                <div>
                    <div>
                        <select name="fields[]" data-placeholder="<?php echo vtranslate('LBL_SELECT_THE_PHONE_NUMBER_FIELDS_TO_SEND',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-rule-required="true" multiple class = "select2 form-control">
                            <?php  $_smarty_tpl->tpl_vars['PHONE_FIELD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PHONE_FIELD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PHONE_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PHONE_FIELD']->key => $_smarty_tpl->tpl_vars['PHONE_FIELD']->value){
$_smarty_tpl->tpl_vars['PHONE_FIELD']->_loop = true;
?>
                                <?php $_smarty_tpl->tpl_vars['PHONE_FIELD_NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['PHONE_FIELD']->value->get('name'), null, 0);?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['PHONE_FIELD_NAME']->value;?>
">
                                    <?php if (!empty($_smarty_tpl->tpl_vars['SINGLE_RECORD']->value)){?>
                                        <?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['SINGLE_RECORD']->value->get($_smarty_tpl->tpl_vars['PHONE_FIELD_NAME']->value), null, 0);?>
                                    <?php }?>
                                    <?php echo vtranslate($_smarty_tpl->tpl_vars['PHONE_FIELD']->value->get('label'),$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
<?php if (!empty($_smarty_tpl->tpl_vars['FIELD_VALUE']->value)){?> (<?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
)<?php }?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <br>        
                    <div>
                        <span id='phoneFormatWarning'> 
                            <i rel="popover" data-placement="right" id="phoneFormatWarningPop" class="glyphicon glyphicon-info-sign" style="padding-right : 5px; padding-left : 5px" data-original-title="<?php echo vtranslate('LBL_WARNING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-trigger="hover" data-content="<?php echo vtranslate('LBL_PHONEFORMAT_WARNING_CONTENT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></i>
                            <?php echo vtranslate('LBL_PHONE_FORMAT_WARNING',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                        </span>
                    </div>
                </div>
                <hr>
                <div>
                    <span><strong><?php echo vtranslate('LBL_STEP_2',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></span>
                    &nbsp;:&nbsp;
                    <?php echo vtranslate('LBL_TYPE_THE_MESSAGE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;(&nbsp;<?php echo vtranslate('LBL_SMS_MAX_CHARACTERS_ALLOWED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;)
                </div>
                <br>
                <textarea class="form-control smsTextArea" data-rule-required="true" name="message" id="message" maxlength="160" placeholder="<?php echo vtranslate('LBL_WRITE_YOUR_MESSAGE_HERE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></textarea>
            </div>
            <div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-success" type="submit" name="saveButton"><strong><?php echo vtranslate('LBL_SEND',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button>
                        <a class="cancelLink" type="reset" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>
<?php }} ?>