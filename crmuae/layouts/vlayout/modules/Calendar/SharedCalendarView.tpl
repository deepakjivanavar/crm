{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  nectarcrm CRM Open Source
   * The Initial Developer of the Original Code is nectarcrm.
   * Portions created by nectarcrm are Copyright (C) nectarcrm.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
	{literal}<style type="text/css">

</style>{/literal}
<input type="hidden" id="currentView" value="{getPurifiedSmartyParameters('view')}" />
<input type="hidden" id="activity_view" value="{$CURRENT_USER->get('activity_view')}" />
<input type="hidden" id="time_format" value="{$CURRENT_USER->get('hour_format')}" />
<input type="hidden" id="start_hour" value="{$CURRENT_USER->get('start_hour')}" />
<input type="hidden" id="date_format" value="{$CURRENT_USER->get('date_format')}" />
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<p><!-- Divider --></p>
				<div id="calendarview"></div>
			</div>
		</div>
	</div>
{/strip}