REM  **************************************************************************************
REM  * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0 *
REM  * ("License"); You may not use this file except in compliance with the License       *
REM  * The Original Code is:  nectarcrm CRM Open Source                                      *
REM  * The Initial Developer of the Original Code is nectarcrm.                              *
REM  * Portions created by nectarcrm are Copyright (C) nectarcrm.                               *
REM  * All Rights Reserved.                                                               *
REM  *                                                                                    *
REM  **************************************************************************************  
@echo off
set SCH_INSTALL=%1
FOR %%X in (%SCH_INSTALL%) DO SET SCH_INSTALL=%%~sX
schtasks /create /tn "nectarcrmCRM Notification Scheduler" /tr %SCH_INSTALL%\apache\htdocs\nectarcrmCRM\cron\intimateTaskStatus.bat /sc daily /st 11:00:00 /RU SYSTEM
schtasks /create /tn "nectarcrmCRM Email Reminder" /tr %SCH_INSTALL%\apache\htdocs\nectarcrmCRM\modules\Calendar\SendReminder.bat /sc minute /mo 1 /RU SYSTEM
