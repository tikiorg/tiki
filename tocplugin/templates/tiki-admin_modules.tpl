{* $Id$ *}

{strip}
{title help="Modules" admpage="module"}{tr}Admin Modules{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-admin_modules.php?clear_cache=1" _icon_name="trash" _text="{tr}Clear Cache{/tr}"}
	{if empty($smarty.request.show_hidden_modules)}
		{button show_hidden_modules="y" _icon_name="ok" _text="{tr}Show hidden modules{/tr}"}
	{else}
		{button show_hidden_modules="" _icon_name="disable" _text="{tr}Hide hidden modules{/tr}"}
	{/if}
	{button _text="{tr}Save{/tr}" _type="primary" _icon_name="floppy" _id="save_modules" _ajax="n"}
	{if $tiki_p_edit_menu eq 'y'}
		{button href="tiki-admin_menus.php" _icon_name="menu" _type="link" _text="{tr}Admin Menus{/tr}"}
	{/if}
	{button href="./" _icon_name="disable" _type="link" _text="{tr}Exit Modules{/tr}"}
</div>

{if !empty($missing_params)}
	{remarksbox type="warning" title="{tr}Modules Parameters{/tr}"}
		{tr}The following required parameters are missing:{/tr}<br/>
		{section name=ix loop=$missing_params}
			{$missing_params[ix]}
			{if !$smarty.section.ix.last},&nbsp;{/if}
		{/section}
	{/remarksbox}
{/if}

{remarksbox type="note" title="{tr}Modules{/tr}" icon="star"}
	<ul>
		<li>{tr}Drag the modules around to re-order then click save when ready{/tr}</li>
		<li>{tr}Double click them to edit{/tr}</li>
		<li>{tr}Modules with "position: absolute" in their style can be dragged in to position{/tr}</li>
		<li>{tr}New modules can be dragged from the "All Modules" tab{/tr}</li>
	</ul>
	<p>
		<strong>{tr}Note:{/tr}</strong> {tr}Links and buttons in modules, apart from the Application Menu, have been deliberately disabled on this page to make drag and drop more reliable. Click here to return <a href="./">HOME</a>{/tr}<br>
		<strong><em>{tr}More info here{/tr}</em></strong> {icon name="help" href="http://dev.tiki.org/Modules+Revamp" class=""}
	</p>

{/remarksbox}

{tabset}

	{tab name="{tr}Assigned modules{/tr}"}
		{if $prefs.feature_tabs neq 'y'}
			<legend class="heading">
				<span>
					{tr}Assign/Edit modules{/tr}
				</span>
			</legend>
		{/if}
		<h2>{tr}Assigned Modules{/tr}</h2>
		<div class="margin-bottom-md">
			{button edit_assign=0 cookietab=2 _auto_args="edit_assign,cookietab" _text="{tr}Add module{/tr}"}
		</div>

		<div id="assigned_modules">
			{tabset}
				{foreach from=$module_zone_list key=zone_initial item=zone_info}
					{tab name=$zone_info.name|capitalize}
						{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
						{if $prefs.javascript_enabled !== 'y'}
							{$js = 'n'}
							{$libeg = '<li>'}
							{$liend = '</li>'}
						{else}
							{$js = 'y'}
							{$libeg = ''}
							{$liend = ''}
						{/if}
						<div id="{$zone_info.id}_modules" class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
							<div>
								<table class="table table-striped table-hover" id="assigned_zone_{$zone_initial}">
									<tr>
										<th>{tr}Name{/tr}</th>
										<th>{tr}Order{/tr}</th>
										<th>{tr}Cache{/tr}</th>
										<th>{tr}Rows{/tr}</th>
										<th>{tr}Parameters{/tr}</th>
										<th>{tr}Groups{/tr}</th>
										<th></th>
									</tr>

									{foreach from=$assigned_modules[$zone_initial] item=module name=assigned_foreach}
										<tr>
											<td>{$module.name|escape}</td>
											<td>{$module.ord}</td>
											<td>{$module.cache_time}</td>
											<td>{$module.rows}</td>
											<td style="font-size:smaller;">{$module.params_presentable}</td>
											<td style="font-size:smaller;">{$module.module_groups}</td>
											<td>
												{capture name=module_actions}
													{strip}
														{if !$smarty.section.user.first}
															{$libeg}<a href="tiki-admin_modules.php?modup={$module.moduleId}">
																{icon name="up" _menu_text='y' _menu_icon='y' alt="{tr}Move up{/tr}"}
															</a>{$liend}
														{/if}
														{if !$smarty.section.user.last}
															{$libeg}<a href="tiki-admin_modules.php?moddown={$module.moduleId}">
																{icon name="down" _menu_text='y' _menu_icon='y' alt="{tr}Move down{/tr}"}
															</a>{$liend}
														{/if}
														{$libeg}<a href="tiki-admin_modules.php?edit_assign={$module.moduleId}&cookietab=2#content_admin_modules1-2">
															{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
														</a>{$liend}
														{$libeg}<a href="tiki-admin_modules.php?unassign={$module.moduleId}">
															{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Unassign{/tr}"}
														</a>{$liend}
													{/strip}
												{/capture}
												{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
												<a
													class="tips"
													title="{tr}Actions{/tr}"
													href="#"
													{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.module_actions|escape:"javascript"|escape:"html"}{/if}
													style="padding:0; margin:0; border:0"
												>
													{icon name='wrench'}
												</a>
												{if $js === 'n'}
													<ul class="dropdown-menu" role="menu">{$smarty.capture.module_actions}</ul></li></ul>
												{/if}
											</td>
										</tr>
									{foreachelse}
										{norecords _colspan=7}
									{/foreach}
								</table>
							</div>
						</div>
					{/tab}
				{/foreach}
			{/tabset}
		</div>
		<form method="post" action="#">
			<input id="module-order" type="hidden" name="module-order" value="">
		</form>
	{/tab}
	{if isset($smarty.request.edit_assign) or $preview eq "y"}
		{tab name="{tr}Edit module{/tr}"}
			<a id="assign"></a>
			{if $assign_name eq ''}
				<h2>{tr}Assign new module{/tr}</h2>
			{else}
				<h2>{tr}Edit this assigned module:{/tr} {$assign_name}</h2>
			{/if}

			{if $preview eq 'y'}
				<h3>{tr}Preview{/tr}</h3>
				{$preview_data}
			{/if}
			<form method="post" action="tiki-admin_modules.php{if empty($assign_name)}?cookietab=2#assign{/if}">
				{* on the initial selection of a new module, reload the page to the #assign anchor *}
				{if !empty($info.moduleId)}
					<input type="hidden" name="moduleId" value="{$info.moduleId}">
				{elseif !empty($moduleId)}
					<input type="hidden" name="moduleId" value="{$moduleId}">
				{/if}
				<fieldset>
						{* because changing the module name will auto-submit the form, no reason to display these fields until a module is selected *}
						{include file='admin_modules_form.tpl'}
					{if empty($assign_name)}
						<div class="input_submit_container">
							<input type="submit" class="btn btn-default btn-sm" name="preview" value="{tr}Module Options{/tr}" onclick="needToConfirm=false;">
						</div>
					{else}
						{jq}$("#module_params").tabs();{/jq}
					{/if}
				</fieldset>
			</form>
		{/tab}
	{/if}

	{tab name="{tr}Custom Modules{/tr}"}
		{if $prefs.feature_tabs neq 'y'}
			<legend class="heading">
				<a href="#usertheme" name="usertheme"><span>{tr}Custom Modules{/tr}</span></a>
			</legend>
		{/if}
		<h2>{tr}Custom Modules{/tr}</h2>
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<div class="{if $js === 'y'}table-responsive{/if}">
			<table class="table">
				<tr>
					<th>{tr}Name{/tr}</th>
					<th>{tr}Title{/tr}</th>
					<th></th>
				</tr>

				{section name=user loop=$user_modules}
					<tr>
						<td class="text"><a class="tips" href="tiki-admin_modules.php?um_edit={$user_modules[user].name|escape:'url'}&amp;cookietab=2#editcreate" title="{tr}Edit{/tr}">{$user_modules[user].name|escape}</a></td>
						<td class="text">{$user_modules[user].title|escape}</td>
						<td class="action">
							{capture name=custom_module_actions}
								{strip}
									{$libeg}<a href="tiki-admin_modules.php?um_edit={$user_modules[user].name|escape:'url'}&amp;cookietab=2#editcreate">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_modules.php?edit_assign={$user_modules[user].name|escape:'url'}&amp;cookietab=2#assign">
										{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Assign{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_modules.php?um_remove={$user_modules[user].name|escape:'url'}&amp;cookietab=2" title="{$user_modules[user].name|escape}:{tr}Delete{/tr}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
									</a>{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.custom_module_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.custom_module_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{sectionelse}
				{norecords _colspan=3}
				{/section}
			</table>
		</div>
		<br>
		{if $um_name eq ''}
			<h2>{tr}Create new custom module{/tr}</h2>
		{else}
			<h2>{tr}Edit this custom module{/tr} {$um_name}</h2>
		{/if}
        <div class="col-sm-10 col-sm-offset-1">
            {remarksbox type="tip" title="{tr}Tip{/tr}"}
                {tr}Create your new custom module below. Make sure to preview first and make sure all is OK before <a href="#assign">assigning it</a>. Using HTML, you will be fine. However, if you improperly use wiki syntax or Smarty code, you could lock yourself out of the site.{/tr}
            {/remarksbox}
        </div>

		<form name='editusr' method="post" action="tiki-admin_modules.php" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Name{/tr}</label>
                <div class="col-sm-6">
                    <input type="text" id="um_name" name="um_name" value="{$um_name|escape}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Title{/tr}</label>
                <div class="col-sm-6">
                    <input type="text" id="um_title" name="um_title" value="{$um_title|escape}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Parse using{/tr}</label>
                <div class="col-sm-6">
                    <select name="um_parse" id="um_parse" class="form-control margin-bottom-sm">
                        <option value=""{if $um_parse eq "" and $um_wikiLingo eq ""} selected="selected"{/if}>{tr}None{/tr}</option>
                        <option value="y"{if $um_parse eq "y" and $um_wikiLingo eq ""} selected="selected"{/if}>{tr}Wiki Markup{/tr}</option>
                        {if $prefs.feature_wikilingo eq 'y'}
                            <option value="wikiLingo"{if $um_wikiLingo eq "y" and $um_parse eq "y"} selected="selected"{/if}>{tr}wikiLingo{/tr}</option>
                        {/if}
                    </select>
                </div>
            </div>
            <h3>{tr}Objects that can be included{/tr}</h3>
            {pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
            {if $prefs.feature_polls eq "y"}
                <div class="form-group">
                    <label class="col-sm-4 control-label">{tr}Available polls{/tr}</label>
                    <div class="col-sm-6">
                        <select name="polls" id='list_polls' class="form-control">
                            <option value="{literal}{{/literal}poll{literal}}{/literal}">--{tr}Random active poll{/tr}--</option>
                            <option value="{literal}{{/literal}poll id=current{literal}}{/literal}">--{tr}Random current poll{/tr}--</option>
                            {section name=ix loop=$polls}
                                <option value="{literal}{{/literal}poll pollId={$polls[ix].pollId}{literal}}{/literal}">{$polls[ix].title|escape}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_galleries', 'um_data');" title=":{tr}Use gallery{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>
                        <a title="{tr}Help{/tr}" {popup text="Params: id= showgalleryname=1 hideimgname=1 hidelink=1" width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {if $galleries}
                <div class="form-group">
                    <label class="col-sm-4 control-label">{tr}Random image from{/tr}</label>
                    <div class="col-sm-6">
                        <select name="galleries" id='list_galleries' class="form-control">
                            <option value="{literal}{{/literal}gallery id=-1{literal}}{/literal}">{tr}All galleries{/tr}</option>
                            {section name=ix loop=$galleries}
                                <option value="{literal}{{/literal}gallery id={$galleries[ix].galleryId}{literal}}{/literal}">{$galleries[ix].name|escape}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_galleries', 'um_data');" title=":{tr}Use gallery{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>
                        <a title="{tr}Help{/tr}" {popup text="Params: id= showgalleryname=1 hideimgname=1 hidelink=1" width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {if $contents}
                <div class="form-group">
                    <label class="col-sm-4 control-label">{tr}Dynamic content blocks{/tr}</label>
                    <div class="col-sm-6">
                        <select name="contents" id='list_contents' class="form-control">
                            {section name=ix loop=$contents}
                                <option value="{literal}{{/literal}content id={$contents[ix].contentId}{literal}}{/literal}">{$contents[ix].description|truncate:20:"...":true}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_contents', 'um_data');" title=":{tr}Use dynamic content{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>>
                        <a title="{tr}Help{/tr}" {popup text="Params: id=" width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {if $rsss}
                <div class="form-group">
                    <label class="col-sm-4 control-label">{tr}External feeds{/tr}</label>
                    <div class="col-sm-6">
                        <select name="rsss" id='list_rsss' class="form-control">
                            {section name=ix loop=$rsss}
                                <option value="{literal}{{/literal}rss id={$rsss[ix].rssId}{literal}}{/literal}">{$rsss[ix].name|escape}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_rsss', 'um_data');" title=":{tr}Use RSS module{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>
                        <a title="{tr}Help{/tr}" {popup text="Params: id= max= skip=x,y " width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {if $banners}
                <div class="form-group">
                    <label class="col-sm-3 control-label">{tr}Banner zones{/tr}</label>
                    <div class="col-sm-6 col-sm-offset-1">
                        <select name="banners" id='list_banners' class="form-control">
                            {section name=ix loop=$banners}
                                <option value="{literal}{{/literal}banner zone={$banners[ix].zone}{literal}}{/literal}">{$banners[ix].zone}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_banners', 'um_data');" title=":{tr}Use banner zone{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>
                        <a title="{tr}Help{/tr}" {popup text="Params: zone= target=_blank|_self|" width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {if $wikistructures}
                <div class="form-group">
                    <label class="col-sm-4 control-label">{tr}Wiki{/tr} {tr}Structures{/tr}</label>
                    <div class="col-sm-6">
                        <select name="structures" id='list_wikistructures' class="form-control">
                            {section name=ix loop=$wikistructures}
                                <option value="{literal}{{/literal}wikistructure id={$wikistructures[ix].page_ref_id}{literal}}{/literal}">{$wikistructures[ix].pageName|escape}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a class="tips" href="javascript:setUserModuleFromCombo('list_wikistructures', 'um_data');" title=":{tr}Use wiki structure{/tr}">{icon name='add' alt="{tr}Use{/tr}"}</a>
                        <a title="{tr}Help{/tr}" {popup text="Params: id=" width=100 center=true}>{icon name='help'}</a>
                    </div>
                </div>
            {/if}
            {pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
            <div class="col-sm-10 col-sm-offset-1">
                {remarksbox type="tip" title="{tr}Tip{/tr}"}
                {if $prefs.feature_cssmenus eq 'y'}
                    {tr}To use a <a target="tikihelp" href="http://users.tpg.com.au/j_birch/plugins/superfish/">CSS (Superfish) menu</a>, use one of these syntaxes:{/tr}
                    <ul>
                        <li>{literal}{menu id=X type=vert}{/literal}</li>
                        <li>{literal}{menu id=X type=horiz}{/literal}</li>
                    </ul>
                {/if}
                {tr}To use a default Tiki menu:{/tr}
                    <ul>
                        <li>{literal}{menu id=X css=n}{/literal}</li>
                    </ul>
                {/remarksbox}
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">{tr}Data{/tr}</label>
                <div class="col-sm-9">
                    <a id="editcreate"></a>
                    {textarea name='um_data' id='um_data' _class=form-control _toolbars='y' _previewConfirmExit='n' _wysiwyg="n"}{$um_data}{/textarea}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-9">
                    <input type="submit" class="btn btn-primary" name="um_update" value="{if empty($um_name)}{tr}Create{/tr}{else}{tr}Save{/tr}{/if}" onclick="needToConfirm=false">
                </div>
            </div>
		</form>
	{/tab}

	{tab name="{tr}All Modules{/tr}"}
		<h2>{tr}All Modules{/tr}</h2>
		<form method="post" action="tiki-admin_modules.php">
			<div style="height:400px;overflow:auto;">
				<div class="navbar">
					{listfilter selectors='#module_list li'}
					<input type="checkbox" name="module_list_show_all" id="module_list_show_all"{if $module_list_show_all} checked="checked"{/if}>
					<label for="module_list_show_all">{tr}Show all modules{/tr}</label>
				</div>
				<ul id="module_list">
					{foreach key=name item=info from=$all_modules_info}
						<li class="{if $info.enabled}enabled{else}disabled{/if} clearfix">
							<input type="hidden" value="{$name}">
							<div class="q1 tips"
									title="{$info.name} &lt;em&gt;({$name})&lt;/em&gt;|{$info.description}
									{if not $info.enabled}&lt;br /&gt;&lt;small&gt;&lt;em&gt;({tr}Requires{/tr} {' &amp; '|implode:$info.prefs})&lt;/em&gt;&lt;/small&gt;{/if}">
								{icon name="module"} <strong>{$info.name}</strong> <em>{$name}</em>
							</div>
							<div class="description q23">
								{$info.description}
							</div>
						</li>
					{/foreach}
				</ul>
			</div>
		</form>
		{jq}
$("#module_list_show_all").click(function(){
	$("#module_list li.disabled").toggle($(this).prop("checked"));
});
		{/jq}
	{/tab}

{/tabset}
{/strip}
