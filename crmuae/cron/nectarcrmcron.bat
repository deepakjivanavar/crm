@echo OFF
REM #*********************************************************************************
REM # The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
REM # ("License"); You may not use this file except in compliance with the License
REM # The Original Code is:  nectarcrm CRM Open Source
REM # The Initial Developer of the Original Code is nectarcrm.
REM # Portions created by nectarcrm are Copyright (C) nectarcrm.
REM # All Rights Reserved.
REM #
REM # ********************************************************************************

set NECTARCRMCRM_ROOTDIR="C:\Program Files\nectarcrmcrm5\apache\htdocs\nectarcrmCRM"
set PHP_EXE="C:\Program Files\nectarcrmcrm5\php\php.exe"

cd /D %NECTARCRMCRM_ROOTDIR%

%PHP_EXE% -f nectarcrmcron.php 
