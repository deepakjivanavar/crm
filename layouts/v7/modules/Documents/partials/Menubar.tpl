{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
*************************************************************************************}

{if $smarty.request.view eq 'Detail'}
<div id="modules-menu" class="modules-menu">    
    <ul>
        <li class="active">
            <a href="{$MODULE_MODEL->getListViewUrl()}">
				{$MODULE_MODEL->getModuleIcon()}
                <span>{$MODULE}</span>
            </a>
        </li>
    </ul>
</div>
{/if}