{* $Id$ *}

{strip}
{title help="Modules" admpage="module"}{tr}Admin Modules{/tr}{/title}

<div class="navbar">
	{button href="tiki-admin_modules.php?clear_cache=1" _text="{tr}Clear Cache{/tr}"}
	{if $tiki_p_edit_menu eq 'y'}
		{button href="tiki-admin_menus.php" _text="{tr}Admin Menus{/tr}"}
	{/if}
</div>

{if !empty($missing_params)}
<div class="simplebox highlight">
	{tr}The following required parameters are missing:{/tr}
	<br/>
	{section name=ix loop=$missing_params}
		{$missing_params[ix]}
		{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div>
<br />
{/if}

{tabset name='tabs_adminmodules'}

{tab name="{tr}Assign/Edit modules{/tr}"}
	{if $prefs.feature_tabs neq 'y'}
		<legend class="heading">
			<span>
				{tr}Assign/Edit modules{/tr}
			</span>
		</legend>
	{/if}
	<h2>{tr}Assigned Modules{/tr}</h2>
	<a name="leftmod"></a>
	<table class="normal">
		<caption>{tr}Left Modules{/tr}</caption>
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Order{/tr}</th>
			<th>{tr}Cache{/tr}</th>
			<th>{tr}Rows{/tr}</th>
			<th>{tr}Parameters{/tr}</th>
			<th>{tr}Groups{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle print=false values="even,odd"}
		{section name=user loop=$left}
			<tr class="{cycle}">
				<td>{$left[user].name|escape}</td>
				<td>{$left[user].ord}</td>
				<td>{$left[user].cache_time}</td>
				<td>{$left[user].rows}</td>
				<td style="max-width: 40em; white-space: normal;">{$left[user].params|stringfix:"&":"<br />"}</td>
				<td>{$left[user].module_groups}</td>
				<td>
					<a class="link" href="tiki-admin_modules.php?edit_assign={$left[user].moduleId}#assign" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
					{if $left[0].moduleId ne $left[user].moduleId}
						<a class="link" href="tiki-admin_modules.php?modup={$left[user].moduleId}" title="{tr}Move Up{/tr}">{icon _id='resultset_up'}</a>
					{/if}
					{if !$smarty.section.user.last and $left[user.index_next].moduleId}
						<a class="link" href="tiki-admin_modules.php?moddown={$left[user].moduleId}" title="{tr}Move Down{/tr}">{icon _id='resultset_down'}</a>
					{/if}
					<a class="link" href="tiki-admin_modules.php?modright={$left[user].moduleId}" title="{tr}Move to Right Column{/tr}">{icon _id='arrow_right'}</a>
					<a class="link" href="tiki-admin_modules.php?unassign={$left[user].moduleId}" title="{tr}Unassign{/tr}">{icon _id='cross' alt="{tr}x{/tr}"}</a>
				</td>
			</tr>
		{sectionelse}
			<tr>
				<td colspan="7"><b>{tr}No records found{/tr}</b></td>
			</tr>
		{/section}
	</table>
	<br />
	<br />
	<a name="rightmod"></a>
	<table class="normal">
		<caption>{tr}Right Modules{/tr}</caption>
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Order{/tr}</th>
			<th>{tr}Cache{/tr}</th>
			<th>{tr}Rows{/tr}</th>
			<th>{tr}Parameters{/tr}</th>
			<th>{tr}Groups{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle print=false values="even,odd"}
		{section name=user loop=$right}
			<tr class="{cycle}">
				<td>{$right[user].name|escape}</td>
				<td>{$right[user].ord}</td>
				<td>{$right[user].cache_time}</td>
				<td>{$right[user].rows}</td>
				<td>{$right[user].params|stringfix:"&":"<br />"}</td>
				<td>{$right[user].module_groups}</td>
				<td>
					<a class="link" href="tiki-admin_modules.php?edit_assign={$right[user].moduleId}#assign" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
					{if $right[0].moduleId ne $right[user].moduleId}
						<a class="link" href="tiki-admin_modules.php?modup={$right[user].moduleId}" title="{tr}Move Up{/tr}">{icon _id='resultset_up'}</a>
					{/if}
					{if !$smarty.section.user.last and $right[user.index_next].moduleId}
						<a class="link" href="tiki-admin_modules.php?moddown={$right[user].moduleId}" title="{tr}Move Down{/tr}">{icon _id='resultset_down'}</a>
					{/if}
					<a class="link" href="tiki-admin_modules.php?modleft={$right[user].moduleId}" title="{tr}Move to Left Column{/tr}">{icon _id='arrow_left'}</a>
					<a class="link" href="tiki-admin_modules.php?unassign={$right[user].moduleId}" title="{tr}Unassign{/tr}">{icon _id='cross' alt="{tr}x{/tr}"}</a>
				</td>
			</tr>
		{sectionelse}
			<tr>
				<td colspan="7"><b>{tr}No records found{/tr}</b></td>
			</tr>
		{/section}
	</table>
	<br/>
	<a name="assign"></a>
	{if $assign_name eq ''}
		<h2>{tr}Assign new module{/tr}</h2>
	{else}
		<h2>{tr}Edit this assigned module:{/tr} {$assign_name}</h2>
	{/if}

	{if $preview eq 'y'}
		<h3>{tr}Preview{/tr}</h3>
		{$preview_data}
	{/if}
	<form method="post" action="tiki-admin_modules.php{if (empty($assign_name))}#assign{/if}">
	{* on the initial selection of a new module, reload the page to the #assign anchor *}
		{if !empty($info.moduleId)}
			<input type="hidden" name="moduleId" value="{$info.moduleId}" />
		{elseif !empty($moduleId)}
			<input type="hidden" name="moduleId" value="{$moduleId}" />
		{/if}
		<table class="formcolor">
			<tr>
				<td><label for="assign_name">{tr}Module Name{/tr}</label></td>
				<td>
					<select id="assign_name" name="assign_name" onchange="needToConfirm=false;this.form.preview.click()">
						<option value=""></option>
						{foreach key=name item=info from=$all_modules_info}
							<option value="{$name|escape}" {if $assign_name eq $name || $assign_selected eq $name}selected="selected"{/if}>{$info.name}</option>
						{/foreach}
					</select>
				</td>
			</tr>

{if !empty($assign_name)}
{* because changing the module name willl auto-submit the form, no reason to display these fields until a module is selected *}
			<tr>
				<td><label for="assign_position">{tr}Position{/tr}</label></td>
				<td>
					<select id="assign_position" name="assign_position">
						<option value="l" {if $assign_position eq 'l'}selected="selected"{/if}>{tr}Left{/tr}</option>
						<option value="r" {if $assign_position eq 'r'}selected="selected"{/if}>{tr}Right{/tr}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="assign_order">{tr}Order{/tr}</label></td>
				<td>
					<select id="assign_order" name="assign_order">
						{section name=ix loop=$orders}
							<option value="{$orders[ix]|escape}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="assign_cache">{tr}Cache Time{/tr} ({tr}secs{/tr})</label></td>
				<td>
					<input type="text" id="assign_cache" name="assign_cache" value="{$assign_cache|escape}" />
				</td>
			</tr>
			{if !isset($assign_info.type) or $assign_info.type neq 'function'}
				<tr>
						<td><label for="assign_rows">{tr}Rows{/tr}</label></td>
						<td>
								<input type="text" id="assign_rows" name="assign_rows" value="{$assign_rows|escape}" />
						</td>
				</tr>
			{/if}
			{if isset($assign_info.type) and $assign_info.type eq 'function'}
				{foreach from=$assign_info.params key=name item=param}
					<tr>
						<td><label for="assign_params[{$name|escape}]">{$param.name|escape}{if $param.required} <span class="attention">({tr}required{/tr})</span>{/if}</label></td>
						<td>
							<input type="text" id="assign_params[{$name|escape}]" name="assign_params[{$name|escape}]" value="{$param.value|escape}"/>
							<div class="description">
								{$param.description}
								{if !empty($param.default)} - {tr}Default:{/tr} {$param.default|escape}{/if}
							</div>
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td>
						<a {popup text="{tr}Params: specific params to the module and/or general params ('lang', 'flip', 'title', 'decorations', 'section', 'overflow', 'page', 'nobox', 'bgcolor', 'color', 'theme', 'notitle', 'nopage'). Separator between params:'&amp;'. E.g. maxlen=15&amp;nonums=y.{/tr}" width=200 center=true}><label for="assign_params">{tr}Parameters{/tr}</label></a>
					</td>
					<td>
						<textarea id="assign_params" name="assign_params" rows="1" cols="60" >{$assign_params|escape}</textarea>
						{help url="Module+Parameters"}
					</td>
				</tr>
			{/if}
			<tr>
				<td><label for="groups">{tr}Groups{/tr}</label></td>
				<td>
					{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					<select multiple="multiple" id="groups" name="groups[]">
						{section name=ix loop=$groups}
							<option value="{$groups[ix].groupName|escape}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].groupName|escape}</option>
						{/section}
					</select>
					{if $prefs.modallgroups eq 'y'}
						<div class="simplebox">
							{icon _id=information style="vertical-align:middle;float:left"} {tr}The{/tr} <a class="rbox-link" href="tiki-admin.php?page=module">{tr}Display Modules to All Groups{/tr}</a> {tr}setting will override your selection of specific groups.{/tr}
						</div>
						<br />
					{/if}
				</td>
			</tr>
			{if $prefs.user_assigned_modules eq 'y'}
				<tr>
					<td>{tr}Visibility{/tr}</td>
					<td>
						<select name="assign_type">
							<option value="D" {if $assign_type eq 'D'}selected="selected"{/if}>
								{tr}Displayed now for all eligible users even with personal assigned modules{/tr}
							</option>
							<option value="d" {if $assign_type eq 'd'}selected="selected"{/if}>
								{tr}Displayed for the eligible users with no personal assigned modules{/tr}
							</option>
							<option value="P" {if $assign_type eq 'P'}selected="selected"{/if}>
								{tr}Displayed now, can't be unassigned{/tr}
							</option>
							<option value="h" {if $assign_type eq 'h'}selected="selected"{/if}>
								{tr}Not displayed until a user chooses it{/tr}
							</option>
						</select>
						<div class="simplebox">
							{icon _id=information style="vertical-align:middle;float:left;"}{tr}Because <a class="rbox-link" href="tiki-admin.php?page=module">Users can Configure Modules</a>, select either{/tr} &quot;{tr}Displayed now for all eligible users even with personal assigned modules{/tr}&quot; {tr}or{/tr} &quot;{tr}Displayed now, can't be unassigned{/tr}&quot; {tr}to make sure users will notice any newly assigned modules.{/tr}
						</div>
					</td>
				</tr>
			{/if}
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
					<input type="submit" name="assign" value="{tr}Assign{/tr}" onclick="needToConfirm=false;" />
				</td>
			</tr>
{else}
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="preview" value="{tr}Module Options{/tr}" onclick="needToConfirm=false;" />
				</td>
			</tr>

{/if}
		</table>
	</form>
{/tab}

{tab name="{tr}User Modules{/tr}"}
	{if $prefs.feature_tabs neq 'y'}
		<legend class="heading">
			<a href="#usertheme" name="usertheme"><span>{tr}User Modules{/tr}</span></a>
		</legend>
	{/if}
	<h2>{tr}User Modules{/tr}</h2>
	<table class="normal">
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Title{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle print=false values="even,odd"}
		{section name=user loop=$user_modules}
			<tr class="{cycle}">
				<td>{$user_modules[user].name|escape}</td>
				<td>{$user_modules[user].title|escape}</td>
				<td>
					<a class="link" href="tiki-admin_modules.php?um_edit={$user_modules[user].name|escape:'url'}&amp;cookietab=2#editcreate" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
					<a class="link" href="tiki-admin_modules.php?edit_assign={$user_modules[user].name|escape:'url'}&amp;cookietab=1#assign" title="{tr}Assign{/tr}">{icon _id='add' alt="{tr}Assign{/tr}"}</a>
					<a class="link" href="tiki-admin_modules.php?um_remove={$user_modules[user].name|escape:'url'}&amp;cookietab=2" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				</td>
			</tr>
		{sectionelse}
			<tr>
				<td colspan="3" class="odd"><b>{tr}No records found{/tr}</b></td>
			</tr>
		{/section}
	</table>
	<br />
	<a name="editcreate"></a>
	{if $um_name eq ''}
		<h2>{tr}Create new user module{/tr}</h2>
	{else}
		<h2>{tr}Edit this user module:{/tr} {$um_name}</h2>
	{/if}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Create your new custom module below. Make sure to preview first and make sure all is OK before <a href="#assign">assigning it</a>. Using html, you will be fine. However, if you improperly use wiki syntax or Smarty code, you could lock yourself out of the site.{/tr}
	{/remarksbox}

	<form name='editusr' method="post" action="tiki-admin_modules.php">
		<table class="formcolor">
			<tr valign="top">
				<td valign="top" class="odd">
					{if $um_name ne ''}
						<a href="tiki-admin_modules.php#editcreate">{tr}Create new user module{/tr}</a>
					{/if}
					<table>
						<tr>
							<td><label for="um_name">{tr}Name{/tr}</label></td>
							<td><input type="text" id="um_name" name="um_name" value="{$um_name|escape}" /></td>
						</tr>
						<tr>
							<td><label for="um_title">{tr}Title{/tr}</label></td>
							<td><input type="text" id="um_title" name="um_title" value="{$um_title|escape}" /></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<label><input type="checkbox" name="um_parse" value="y" {if $um_parse eq "y"}checked="checked"{/if} /> {tr}Must be wiki parsed{/tr}.</label>
							</td>
						</tr>
					</table>
				</td>
				<td class="even" style="vertical-align:top">
					<h3>{tr}Objects that can be included{/tr}</h3>
					{pagination_links cant=$maximum step=$maxRecords offset=$offset }{/pagination_links}
					<table>
						{if $polls}
							<tr>
								<td>
									<label for="list_polls">{tr}Available polls:{/tr}</label>
								</td>
								<td>
									<select name="polls" id='list_polls'>
										<option value="{literal}{{/literal}poll{literal}}{/literal}">--{tr}Random active poll{/tr}--</option>
										<option value="{literal}{{/literal}poll id=current{literal}}{/literal}">--{tr}Random current poll{/tr}--</option>
										{section name=ix loop=$polls}
											<option value="{literal}{{/literal}poll id={$polls[ix].pollId}{literal}}{/literal}">{$polls[ix].title|escape}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_polls', 'um_data');" title="{tr}Use Poll{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: id= rate=" width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}
						{if $galleries}
							<tr>
								<td>
									<label for="list_galleries">{tr}Random image from:{/tr}</label>
								</td>
								<td>
									<select name="galleries" id='list_galleries'>
										<option value="{literal}{{/literal}gallery id=-1{literal}}{/literal}">{tr}All galleries{/tr}</option>
										{section name=ix loop=$galleries}
											<option value="{literal}{{/literal}gallery id={$galleries[ix].galleryId}{literal}}{/literal}">{$galleries[ix].name|escape}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_galleries', 'um_data');" title="{tr}Use Gallery{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: id= showgalleryname=1 hideimgname=1 hidelink=1" width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}
						{if $contents}
							<tr>
								<td>
									<label for="list_contents">{tr}Dynamic content blocks:{/tr}</label>
								</td>
								<td>
									<select name="contents" id='list_contents'>
										{section name=ix loop=$contents}
											<option value="{literal}{{/literal}content id={$contents[ix].contentId}{literal}}{/literal}">{$contents[ix].description|truncate:20:"...":true}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_contents', 'um_data');" title="{tr}Use Dynamic Content{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: id=" width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}
						{if $rsss}
							<tr>
								<td>
									<label for="list_rsss">{tr}External feeds:{/tr}</label>
								</td>
								<td>
									<select name="rsss" id='list_rsss'>
										{section name=ix loop=$rsss}
											<option value="{literal}{{/literal}rss id={$rsss[ix].rssId}{literal}}{/literal}">{$rsss[ix].name|escape}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_rsss', 'um_data');" title="{tr}Use RSS Module{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: id= max= skip=x,y " width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}

						{if $menus}
							<tr>
								<td>
									<label for="list_menus">{tr}Default Tiki menus:{/tr}</label>
								</td>
								<td>
									<select name="menus" id='list_menus'>
										{section name=ix loop=$menus}
											<option value="{literal}{{/literal}menu id={$menus[ix].menuId}{literal}}{/literal}">{$menus[ix].name|escape}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_menus', 'um_data');" title="{tr}Use Menu{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params:<br />id=<br />structureId=<br />css=<br />link_on_section=y <i>or</i> n<br />type=vert <i>or</i> horiz<br />translate=y <i>or</i> n<br />menu_cookie=y <i>or</i> n" width=120 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
							{if $prefs.feature_cssmenus eq "y"}
								<tr>
									<td>
										<label for="list_cssmenus">{tr}CSS menus:{/tr}</label>
									</td>
									<td>
										<select name="cssmenus" id='list_cssmenus'>
											{section name=ix loop=$menus}
												<option value="{literal}{{/literal}menu id={$menus[ix].menuId} css=y type= {literal}}{/literal}">{$menus[ix].name}</option>
											{/section}
										</select>
									</td>
									<td>
										<a class="link" href="javascript:setUserModuleFromCombo('list_cssmenus', 'um_data');" title="{tr}Use CSS menu{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
									</td>
									<td>
										<a {popup text="Params:<br />id=<br />type=horiz <i>or</i> vert<br />sectionLevel=<br />toLevel= " width=100 center=true}>{icon _id='help'}</a>
									</td>
								</tr>
							{/if}							
							{if $prefs.feature_phplayers eq "y"}
								<tr>
									<td>
										<label for="list_phpmenus">{tr}PHP Layers menus:{/tr}</label>
									</td>
									<td>
										<select name="phpmenus" id='list_phpmenus'>
											{section name=ix loop=$menus}
												<option value="{literal}{{/literal}phplayers id={$menus[ix].menuId}{literal}}{/literal}">{$menus[ix].name}</option>
											{/section}
										</select>
									</td>
									<td>
										<a class="link" href="javascript:setUserModuleFromCombo('list_phpmenus', 'um_data');" title="{tr}Use phplayermenu{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
									</td>
									<td>
										<a {popup text="Params:<br />id=<br />type=tree <i>or</i> phptree <i>or</i> plain <i>or</i> horiz <i>or</i> vert<br />file=<br />sectionLevel=" width=100 center=true}>{icon _id='help'}</a>
									</td>
								</tr>
							{/if}
						{/if}
						{if $banners}
							<tr>
								<td>
									<label for="list_banners">{tr}Banner zones:{/tr}</label>
								</td>
								<td>
									<select name="banners" id='list_banners'>
										{section name=ix loop=$banners}
											<option value="{literal}{{/literal}banner zone={$banners[ix].zone}{literal}}{/literal}">{$banners[ix].zone}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_banners', 'um_data');" title="{tr}Use Banner Zone{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: zone= target=_blank|_self|" width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}
						{if $wikistructures}
							<tr>
								<td>
									<label for="list_wikistructures">{tr}Wiki{/tr} {tr}Structures:{/tr}</label>
								</td>
								<td>
									<select name="structures" id='list_wikistructures'>
										{section name=ix loop=$wikistructures}
											<option value="{literal}{{/literal}wikistructure id={$wikistructures[ix].page_ref_id}{literal}}{/literal}">{$wikistructures[ix].pageName|escape}</option>
										{/section}
									</select>
								</td>
								<td>
									<a class="link" href="javascript:setUserModuleFromCombo('list_wikistructures', 'um_data');" title="{tr}Use Wiki Structure{/tr}">{icon _id='add' alt="{tr}Use{/tr}"}</a>
								</td>
								<td>
									<a {popup text="Params: id=" width=100 center=true}>{icon _id='help'}</a>
								</td>
							</tr>
						{/if}
					</table>
					{pagination_links cant=$maximum step=$maxRecords offset=$offset }{/pagination_links}
					{remarksbox type="tip" title="{tr}Tip{/tr}"}
							{tr}To use a default Tiki menu:{/tr}
							<ul>
								<li>{literal}{menu id=X}{/literal}</li>
							</ul>
						{if $prefs.feature_cssmenus eq 'y'}
							{tr}To use a <a target="tikihelp" href="http://users.tpg.com.au/j_birch/plugins/superfish/">CSS (Superfish) menu</a>, use one of these syntaxes:{/tr}
							<ul>
								<li>{literal}{menu id=X css=y type=vert}{/literal}</li>
								<li>{literal}{menu id=X css=y type=horiz}{/literal}</li>
							</ul>
						{/if}
						{if $prefs.feature_phplayers eq "y"}
						{tr}To use a <a target="tikihelp" href="http://phplayersmenu.sourceforge.net/">PHP Layers menu</a>, use one of these syntaxes (Note: PHP Layers menus are being replaced by CSS menus and may have only basic styling in newer themes.):{/tr}
						<ul>
							<li>{literal}{phplayers id=X}{/literal}</li>
							<li>{literal}{phplayers id=X type=horiz}{/literal}</li>
							<li>{literal}{phplayers id=X type=vert}{/literal}</li>
						</ul>
						<br />
						{/if}
					{/remarksbox}
				</td>
			</tr>
			<tr>
				<td colspan="2" class="odd">{tr}Data{/tr}<br />
					{textarea name='um_data' id='um_data' rows="6" cols="80" _toolbars='y' _zoom='n' _previewConfirmExit='n' _wysiwyg="n"}{$um_data}{/textarea}
					<br />
					<input type="submit" name="um_update" value="{if $um_title eq ''}{tr}Create{/tr}{else}{tr}Save{/tr}{/if}" onclick="needToConfirm=false" />
				</td>
			</tr>
		</table>
	</form>
{/tab}

{/tabset}
{/strip}
