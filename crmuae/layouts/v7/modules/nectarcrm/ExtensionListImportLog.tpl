{*<!--
/*********************************************************************************
** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
*********************************************************************************/
-->*}

<script type="text/javascript" src="{vresource_url('layouts/v7/modules/nectarcrm/resources/ExtensionCommon.js')}"></script>

<div class='fc-overlay-modal modal-content'>
    <div class="overlayHeader">
        {assign var=TITLE value={vtranslate('LBL_IMPORT_RESULTS_GOOGLE',$MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
    </div>
    <div class="modal-body" style = "margin-bottom:450px">
        {include file="ExtensionListLog.tpl"|vtemplate_path:$MODULE}
    </div>
</div>

