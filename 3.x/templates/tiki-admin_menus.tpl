{title help="Menus"}{tr}Admin Menus{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To use menus in a <a href="tiki-admin_modules.php">module</a>, <a href="tiki-admin.php?page=siteid">Site identity</a> or a template, use {literal}{menu id=x}{/literal}, where x is the ID of the menu.{/tr}
	<hr />
	{tr}To use <a target="tikihelp" href="http://phplayersmenu.sourceforge.net/">phplayersmenu</a>, you can use one of the three following syntaxes:{/tr}
	<ul>
		<li>{literal}{phplayers id=X}{/literal}</li>
		<li>{literal}{phplayers id=X type=horiz}{/literal}</li>
		<li>{literal}{phplayers id=X type=vert}{/literal}</li>
	</ul>
	{tr}This will work well (or not!) depending on your theme. To learn more about <a target="tikihelp" href="http://themes.tikiwiki.org">themes</a>{/tr}<br />
	{tr}To use a menu in a tiki format:{/tr} {literal}{menu id=X}{/literal}<br />
	{tr}To use menu in a css/suckerfish format:{/tr} {literal}{menu id=X css=y}{/literal}<br />
	{tr}To customize the menu's CSS id (#):{/tr} {literal}{menu id=X css_id=custom_name}{/literal}
{/remarksbox}

{if $menuId > 0}
	<h2>{tr}Edit this Menu:{/tr} {$info.name}</h2>
	{button href="tiki-admin_menus.php" _text="{tr}Create new Menu{/tr}"}
{else}
	<h2>{tr}Create new Menu{/tr}</h2>
{/if}

<form action="tiki-admin_menus.php" method="post">
	<input type="hidden" name="menuId" value="{$menuId|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor">
				<label for="menus_name">{tr}Name{/tr}:</label>
			</td>
			<td class="formcolor">
				<input type="text" name="name" id="menus_name" value="{$info.name|escape}" style="width:95%" />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="menus_desc">{tr}Description{/tr}:</label>
			</td>
			<td class="formcolor">
				<textarea name="description" id="menus_desc" rows="4" cols="40" style="width:95%">{$info.description|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="menus_type">{tr}Type{/tr}:</label>
			</td>
			<td class="formcolor">
				<select name="type" id="menus_type">
					<option value="d" {if $info.type eq 'd'}selected="selected"{/if}>{tr}dynamic collapsed{/tr} (d)</option>
					<option value="e" {if $info.type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr} (e)</option>
					<option value="f" {if $info.type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr} (f)</option>
				</select>
			</td>
		</tr>
		{if $prefs.feature_menusfolderstyle eq 'y'}
			<tr>
				<td class="formcolor">
					{tr}Folder Icon{/tr}:
				</td>
				<td>
					<input type="text" name="icon" value="{$info.icon}" style="width:95%" />
					<br />
					<em>{tr}Path and filename of closed folder icon{/tr}</em>.

					{remarksbox type="tip" title="{tr}Note{/tr}"}
						{tr}To use custom folder icons in menus, enter the path to the icon for the <strong>closed</strong> folder.{/tr} {tr}In the same directory, include an icon for the opened folder.{/tr} {tr}The "opened folder" icon name must be identical to the "closed folder" icon name, prefixed with the letter <strong>o</strong>.{/tr}
						<hr />
						{tr}For example, the default icon is: pics/icons/folder.png{/tr} {icon _id="folder"}
						<br />
						{tr}The name of the "open folder" icon is: pics/icons/ofolder.png{/tr} {icon _id="ofolder"}
					{/remarksbox}
				</td>
			</tr>
		{/if}
		<tr>
			<td class="formcolor">
				&nbsp;
			</td>
			<td class="formcolor">
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>

<br />
<h2>{tr}Menus{/tr}</h2>
{include file='find.tpl'}
<table class="normal">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='menuId'}{tr}ID{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
		<th>{tr}Options{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr>
			<td class="{cycle advance=false}">{$channels[user].menuId}</td>
			<td class="{cycle advance=false}">
				<a href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}" title="{tr}Edit{/tr}">{$channels[user].name}</a><br />{$channels[user].description}
			</td>
			<td class="{cycle advance=false}" style="text-align:center">{$channels[user].type}</td>
			<td class="{cycle advance=false}" style="text-align:right;">{$channels[user].options}&nbsp;</td>
			<td class="{cycle advance=true}">
				<a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
				{if $tiki_p_edit_menu_option eq 'y'}	
					<a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title="{tr}Configure/Options{/tr}">{icon _id='table' alt='{tr}Configure/Options{/tr}'}</a>
				{/if}
				{if $channels[user].individual eq 'y'}
					<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=menus&amp;permType=menus&amp;objectId={$channels[user].menuId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
				{else}
					<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=menus&amp;permType=menus&amp;objectId={$channels[user].menuId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
				{/if}
				&nbsp;
				<a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].menuId}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
			</td>
		</tr>
	{sectionelse}
		<tr>
			<td class="odd" colspan="5">No records found.</td>
		</tr>
	{/section}
</table>
{pagination_links cant=$cant step=$maxRecords offset=$offset }{/pagination_links}
