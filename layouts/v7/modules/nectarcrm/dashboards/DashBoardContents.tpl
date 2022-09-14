{*+**********************************************************************************
* The contents of this file are subject to the nectarcrm CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: nectarcrm CRM Open Source
* The Initial Developer of the Original Code is nectarcrm.
* Portions created by nectarcrm are Copyright (C) nectarcrm.
* All Rights Reserved.
************************************************************************************}
{* modules/nectarcrm/views/DashBoard.php *}
    
{strip}
<div class="dashBoardContainer clearfix" style="background:#F7F8FA;">:
        <div class="tabContainer" >
            

            <ul class="nav nav-tabs tabs sortable container-fluid" style="background:#F7F8FA;">
                {foreach key=index item=TAB_DATA from=$DASHBOARD_TABS}
                    <li class="{if $TAB_DATA["id"] eq $SELECTED_TAB}active{/if} dashboardTab" data-tabid="{$TAB_DATA["id"]}" data-tabname="{$TAB_DATA["tabname"]}">
                        <a data-toggle="tab" href="#tab_{$TAB_DATA["id"]}">
                            <div>
                                <span class="name textOverflowEllipsis" value="{$TAB_DATA["tabname"]}" style="color:#7D86A9;width:10%">
                                    <strong>{$TAB_DATA["tabname"]}</strong>
                                </span>
                                <span class="editTabName hide">
                                    <input type="text" name="tabName"/>
                                </span>
                                {if $TAB_DATA["isdefault"] eq 0}
                                    <i class="fa fa-close deleteTab"></i>
                                {/if}
                                <i class="fa fa-bars moveTab hide"></i>
                            </div>
                        </a>
                    </li>

                {/foreach}
                
                <div class="pull-right" style="position:relative;bottom:10px;">
						<div class="btn-group">
							{if $SELECTABLE_WIDGETS|count gt 0}
								<button class='btn btn-default addButton dropdown-toggle' data-toggle='dropdown'>
									<img src="layouts/v7/resources/Images/Widget_2.png">
								</button>

								<ul class="dropdown-menu dropdown-menu-right widgetsList pull-right" style="min-width:100%;text-align:left;">
									{assign var="MINILISTWIDGET" value=""}
									{foreach from=$SELECTABLE_WIDGETS item=WIDGET}
										{if $WIDGET->getName() eq 'MiniList'}
											{assign var="MINILISTWIDGET" value=$WIDGET} {* Defer to display as a separate group *}
										{elseif $WIDGET->getName() eq 'Notebook'}
											{assign var="NOTEBOOKWIDGET" value=$WIDGET} {* Defer to display as a separate group *}
										{else}
											<li>
												<a onclick="nectarcrm_DashBoard_Js.addWidget(this, '{$WIDGET->getUrl()}')" href="javascript:void(0);"
													data-linkid="{$WIDGET->get('linkid')}" data-name="{$WIDGET->getName()}" data-width="{$WIDGET->getWidth()}" data-height="{$WIDGET->getHeight()}">
													{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}</a>
											</li>
										{/if}
									{/foreach}

									{if $MINILISTWIDGET && $MODULE_NAME == 'Home'}
										<li class="divider"></li>
										<li>
											<a onclick="nectarcrm_DashBoard_Js.addMiniListWidget(this, '{$MINILISTWIDGET->getUrl()}')" href="javascript:void(0);"
												data-linkid="{$MINILISTWIDGET->get('linkid')}" data-name="{$MINILISTWIDGET->getName()}" data-width="{$MINILISTWIDGET->getWidth()}" data-height="{$MINILISTWIDGET->getHeight()}">
												{vtranslate($MINILISTWIDGET->getTitle(), $MODULE_NAME)}</a>
										</li>
										<li>
											<a onclick="nectarcrm_DashBoard_Js.addNoteBookWidget(this, '{$NOTEBOOKWIDGET->getUrl()}')" href="javascript:void(0);"
												data-linkid="{$NOTEBOOKWIDGET->get('linkid')}" data-name="{$NOTEBOOKWIDGET->getName()}" data-width="{$NOTEBOOKWIDGET->getWidth()}" data-height="{$NOTEBOOKWIDGET->getHeight()}">
												{vtranslate($NOTEBOOKWIDGET->getTitle(), $MODULE_NAME)}</a>
										</li>
									{/if}

								</ul>
							{else if $MODULE_PERMISSION}
								<button class='btn btn-default addButton dropdown-toggle' disabled="disabled" data-toggle='dropdown'>
									<strong>{vtranslate('LBL_ADD_WIDGET')}</strong> &nbsp;&nbsp;
									<i class="caret"></i>
								</button>
							{/if}
						</div>
				</div>        
    
                <div class=" pull-right">
                    
                        <ul class=" moreDashBoards" style="padding:10px;">
                          <a class = "dashbtn addNewDashBoard" href="#">{vtranslate('LBL_ADD_NEW_DASHBOARD',$MODULE)}</a> 
                            <a class = "dashbtn reArrangeTabs" href="#">{vtranslate('LBL_REARRANGE_DASHBOARD_TABS',$MODULE)}</a>
                        </ul>
                   
                    <button class="btn-success updateSequence pull-right hide">{vtranslate('LBL_SAVE_ORDER',$MODULE)}</button>
                    
                    
                </div>
                
                	

            </ul>

            <div class="tab-content" style="background:#F7F8FA; ">

                {foreach key=index item=TAB_DATA from=$DASHBOARD_TABS}
                    <div id="tab_{$TAB_DATA["id"]}" data-tabid="{$TAB_DATA["id"]}" data-tabname="{$TAB_DATA["tabname"]}" class="tab-pane fade {if $TAB_DATA["id"] eq $SELECTED_TAB}in active{/if}">
                        {if $TAB_DATA["id"] eq $SELECTED_TAB}
                            {include file="dashboards/DashBoardTabContents.tpl"|vtemplate_path:$MODULE TABID=$TABID}
                        {/if}
                    </div>
                {/foreach}
          
        </div>        </div>

</div>
{/strip}