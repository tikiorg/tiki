{title help="User+Menu"}{tr}User Menu{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
<br />
{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
	<a title="({tr}May need to refresh twice to see changes{/tr})" class="link" href="tiki-usermenu.php?addbk=1">{tr}Add top level bookmarks to menu{/tr}</a> 
{/if}

<h2>{tr}Add or edit an item{/tr}</h2>
<form action="tiki-usermenu.php" method="post">
	<input type="hidden" name="menuId" value="{$menuId|escape}" />
	<table class="formcolor">
		<tr>
			<td>{tr}Name{/tr}</td>
			<td><input type="text" name="name" value="{$info.name|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}URL{/tr}</td>
			<td><input type="text" name="url" value="{$info.url|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Position{/tr}</td>
			<td><input type="text" name="position" value="{$info.position|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Mode{/tr}</td>
			<td>
				<select name="mode">
					<option value="n" {if $info.mode eq 'n'}selected="selected"{/if}>{tr}New Window{/tr}</option>
					<option value="w" {if $info.mode eq 'w'}selected="selected"{/if}>{tr}replace window{/tr}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
		</tr>
	</table>
</form>
<h2>{tr}Menus{/tr}</h2>

{include file='find.tpl'}

<form action="tiki-usermenu.php" method="post">
	<table class="normal">
		<tr>
			<th><input type="submit" name="delete" value="x " title="{tr}Delete Selected{/tr}" /></th>
			<th><a href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Pos{/tr}</a></th>
			<th><a href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
			<th><a href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
			<th><a href="tiki-usermenu.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'mode_desc'}mode_asc{else}mode_desc{/if}">{tr}Mode{/tr}</a></th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td class="checkbox">
					<input type="checkbox" name="menu[{$channels[user].menuId}]" />
				</td>
				<td class="text">{$channels[user].position}</td>
				<td class="text">
					<a class="link" href="tiki-usermenu.php?menuId={$channels[user].menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">
						{$channels[user].name}
					</a>
				</td>
				<td class="text">{$channels[user].url|truncate:40:"...":true}</td>
				<td class="text">{$channels[user].mode}</td>
			</tr>
		{sectionelse}
			{norecords _colspan=5}
		{/section}
	</table>
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
