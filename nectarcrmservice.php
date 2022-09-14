<?php
/*+*******************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 ********************************************************************************/
if(isset($_REQUEST['service']))
{
	if($_REQUEST['service'] == "customerportal")
	{
		include("soap/customerportal.php");
	}
	elseif($_REQUEST['service'] == "firefox")
	{
		include("soap/firefoxtoolbar.php");
	}
	elseif($_REQUEST['service'] == "wordplugin")
	{
		include("soap/wordplugin.php");
	}
	elseif($_REQUEST['service'] == "thunderbird")
	{
		include("soap/thunderbirdplugin.php");
	}
	else
	{
		echo "No Service Configured for ". strip_tags($_REQUEST[service]);
	}
}
else
{
	echo "<h1>nectarcrmCRM Soap Services</h1>";
	echo "<li>nectarcrmCRM Outlook Plugin EndPoint URL -- Click <a href='nectarcrmservice.php?service=outlook'>here</a></li>";
	echo "<li>nectarcrmCRM Word Plugin EndPoint URL -- Click <a href='nectarcrmservice.php?service=wordplugin'>here</a></li>";
	echo "<li>nectarcrmCRM ThunderBird Extenstion EndPoint URL -- Click <a href='nectarcrmservice.php?service=thunderbird'>here</a></li>";
	echo "<li>nectarcrmCRM Customer Portal EndPoint URL -- Click <a href='nectarcrmservice.php?service=customerportal'>here</a></li>";
	echo "<li>nectarcrmCRM WebForm EndPoint URL -- Click <a href='nectarcrmservice.php?service=webforms'>here</a></li>";
	echo "<li>nectarcrmCRM FireFox Extension EndPoint URL -- Click <a href='nectarcrmservice.php?service=firefox'>here</a></li>";
}


?>
