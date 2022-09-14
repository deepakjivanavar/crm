{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
        <link type='text/css' rel='stylesheet' href='{vresource_url("layouts/v7/mycss/sidebar.css")}'>
 

<div class="sidebar app-menu hide" id="app-menu" >
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-xs-2 cursorPointer app-switcher-container" style="padding:20px;">
				<div class="row sidebar-app-navigator" >
					<span id="menu-toggle-action" class="app-icon fa fa-bars" style="font-size:12px;   margin:-5px;"></span> 								 

				</div>
				

			</div>
			
			<div class="col-sm-8 col-xs-8">
			    					<img src="layouts/v7/resources/Images/nectarcrm logo.png" style="width:60%; margin-top:20px;">
			    
			</div>
		</div>
		{assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
		{assign var=HOME_MODULE_MODEL value=nectarcrm_Module_Model::getInstance('Home')}
		{assign var=DASHBOARD_MODULE_MODEL value=nectarcrm_Module_Model::getInstance('Dashboard')}
		<div class="app-list row">
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($DASHBOARD_MODULE_MODEL->getId())}
				<div class="sidebar-menu-item menu-item app-item dropdown-toggle" data-default-url="{$HOME_MODULE_MODEL->getDefaultUrl()}">
					<div class="menu-items-wrapper">
						<span class="app-icon-list fa fa-dashboard"></span>
						<span class="sidebarapp-name"> {vtranslate('LBL_DASHBOARD',$MODULE)}</span>
					</div>
				</div>
			{/if}
			{assign var=APP_GROUPED_MENU value=Settings_MenuEditor_Module_Model::getAllVisibleModules()}
			{assign var=APP_LIST value=nectarcrm_MenuStructure_Model::getAppMenuList()}
			{foreach item=APP_NAME from=$APP_LIST}
				{if $APP_NAME eq 'ANALYTICS'} {continue}{/if}
				{if !empty($APP_GROUPED_MENU.$APP_NAME)}
					<div class="dropdown app-modules-dropdown-container">
						{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
							{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
							{if $APP_MENU_MODEL}
								{break}
							{/if}
						{/foreach}
						{* Fix for Responsive Layout Menu - Changed data-default-url to # *}
						<div class="sidebar-menu-item   app-item dropdown-toggle " data-app-name="{$APP_NAME}" id="{$APP_NAME}_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="#">
							<div class="menu-items-wrapper app-menu-items-wrapper">
								<span class="app-icon-list fa {$APP_IMAGE_MAP.$APP_NAME}" style="font-size:20px;"></span>
								<span class="sidebarapp-name"> {vtranslate("LBL_$APP_NAME")}</span>
								
								<span class="fa fa-chevron-right pull-right"></span>
							</div>
						</div>
						<div class="dropdown-menu app-modules-dropdown" aria-labelledby="{$APP_NAME}_modules_dropdownMenu"  style="background:#fff; margin-left:5px; border:none;">
							{foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
								{assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
									<a  href="{$moduleModel->getDefaultUrl()}&app={$APP_NAME}" title="{$translatedModuleLabel}">
 								     <div class="sidebar-menu-item" style="padding:10px; cursor:pointer; ">
								
										 {$moduleModel->getModuleIcon()} 
										<span style="padding-left:40px;"> {$translatedModuleLabel}</span>
								
								 </div>	
								 </a> 
 							{/foreach}
						</div>
					</div>
				{/if}
			{/foreach}
			<!--<div class="app-list-divider"></div>-->
			{assign var=MAILMANAGER_MODULE_MODEL value=nectarcrm_Module_Model::getInstance('MailManager')}
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($MAILMANAGER_MODULE_MODEL->getId())}
				<div class="sidebar-menu-item  menu-item app-item app-item-misc" data-default-url="index.php?module=MailManager&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list fa">{$MAILMANAGER_MODULE_MODEL->getModuleIcon()}</span>
						<span class="sidebarapp-name"> {vtranslate('MailManager')}</span>
					</div>
				</div>
			{/if}
			{assign var=DOCUMENTS_MODULE_MODEL value=nectarcrm_Module_Model::getInstance('Documents')}
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($DOCUMENTS_MODULE_MODEL->getId())}
				<div class="sidebar-menu-item  menu-item app-item app-item-misc" data-default-url="index.php?module=Documents&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list fa">{$DOCUMENTS_MODULE_MODEL->getModuleIcon()}</span>
						<span class="sidebarapp-name"> {vtranslate('Documents')}</span>
					</div>
				</div>
			{/if}
			{if $USER_MODEL->isAdminUser()}
				{if vtlib_isModuleActive('ExtensionStore')}
					<div class="sidebar-menu-item  menu-item app-item app-item-misc" data-default-url="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore">
						<div class="menu-items-wrapper">
							<span class="app-icon-list fa fa-shopping-cart"></span>
							<span class="sidebarapp-name"> {vtranslate('LBL_EXTENSION_STORE', 'Settings:nectarcrm')}</span>
						</div>
					</div>
				{/if}
			{/if}
			{if $USER_MODEL->isAdminUser()}
				<div class="  dropdown app-modules-dropdown-container dropdown-compact" >
					<div class="sidebar-menu-item menu-item app-item dropdown-toggle app-item-misc" data-app-name="TOOLS" id="TOOLS_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="#">
						<div class="menu-items-wrapper app-menu-items-wrapper">
							<span class="app-icon-list fa fa-cog"></span>
							<span class="sidebarapp-name"> {vtranslate('LBL_SETTINGS', 'Settings:nectarcrm')}</span>
							{if $USER_MODEL->isAdminUser()}
								<span class="fa fa-chevron-right pull-right"></span>
							{/if}
						</div>
					</div>
					<ul class="dropdown-menu app-modules-dropdown dropdown-modules-compact" aria-labelledby="{$APP_NAME}_modules_dropdownMenu" data-height="0.27" style=" border:none; background:#fff;">
						<li>
							<a href="?module=nectarcrm&parent=Settings&view=Index">
								<span class="fa fa-cog module-icon"></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('LBL_CRM_SETTINGS','nectarcrm')}</span>
							</a>
						</li>
						<li>
							<a href="?module=Users&parent=Settings&view=List">
								<span class="fa fa-user module-icon"></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('LBL_MANAGE_USERS','nectarcrm')}</span>
							</a>
						</li>
					</ul>
				</div>
			{/if}
		</div>
	</div>
</div>
