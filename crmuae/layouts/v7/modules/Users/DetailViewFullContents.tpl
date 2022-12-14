{*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/nectarcrm/views/Detail.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
	{assign var=NAME_FIELDS value=array('first_name', 'last_name')}
	{if $MODULE_MODEL}
		{assign var=NAME_FIELDS value=$MODULE_MODEL->getNameFields()}
	{/if}
    <form id="detailView" data-name-fields='{ZEND_JSON::encode($NAME_FIELDS)}' method="POST">
        {include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    </form>
{/strip}
