#*********************************************************************************
# The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
# ("License"); You may not use this file except in compliance with the License
# The Original Code is:  nectarcrm CRM Open Source
# The Initial Developer of the Original Code is nectarcrm.
# Portions created by nectarcrm are Copyright (C) nectarcrm.
# All Rights Reserved.
#
# ********************************************************************************

export NECTARCRMCRM_ROOTDIR=`dirname "$0"`/..
export USE_PHP=php

cd $NECTARCRMCRM_ROOTDIR
# TO RUN ALL CORN JOBS
$USE_PHP -f nectarcrmcron.php 
