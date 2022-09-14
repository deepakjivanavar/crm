{*+**********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="HistoryContainer">
        <div class="historyButtons btn-group" role="group" aria-label="...">
            <button type="button" class="btn btn-default" onclick='nectarcrm_Detail_Js.showUpdates(this);'>
                {vtranslate("LBL_UPDATES",$MODULE_NAME)}
            </button>
        </div>
        
        <div class='data-body'>
        </div>
    </div>
    
{/strip}