{title help="Menus" admpage="general&amp;cookietab=3"}{tr}Admin Menus{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	<div class="navbar">
		{button href="tiki-admin_modules.php" _text="{tr}Admin Modules{/tr}"}
	</div>
{/if}

{tabset name="admin_menus"}
	{tab name="{tr}Menus{/tr}"}
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
				<tr class="{cycle}">
					<td>{$channels[user].menuId}</td>
					<td>
						{if $tiki_p_edit_menu_option eq 'y'}
							<a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title="{tr}Configure/Options{/tr}">{$channels[user].name|escape}</a>
						{/if}
						<br />
						{$channels[user].description|escape|nl2br}
					</td>
					<td style="text-align:center">{$channels[user].type}</td>
					<td style="text-align:right;">{$channels[user].options}&nbsp;</td>
					<td>
						{self_link menuId=$channels[user].menuId cookietab="2" _title="{tr}Edit{/tr}"}{icon _id='page_edit'}{/self_link}
						{if $tiki_p_edit_menu_option eq 'y'}
							<a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}" title="{tr}Configure/Options{/tr}">{icon _id='table' alt="{tr}Configure/Options{/tr}"}</a>
						{/if}
						{if $channels[user].individual eq 'y'}
							<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=menus&amp;permType=menus&amp;objectId={$channels[user].menuId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
						{else}
							<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=menus&amp;permType=menus&amp;objectId={$channels[user].menuId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
						{/if}
						&nbsp;
						{self_link remove=$channels[user].menuId _title="{tr}Delete{/tr}"}{icon _id='cross' alt="{tr}Delete{/tr}"}{/self_link}
					</td>
				</tr>
			{sectionelse}
				<tr>
					<td class="odd" colspan="5">No records found.</td>
				</tr>
			{/section}
		</table>
		{pagination_links cant=$cant step=$maxRecords offset=$offset }{/pagination_links} 
	{/tab}

	{tab name="{tr}Create/Edit Menu{/tr}"}
		{if $menuId > 0}
			<h2>{tr}Edit this Menu:{/tr} {$info.name}</h2>
			{button href="tiki-admin_menus.php" _text="{tr}Create new Menu{/tr}"}
		{else}
			<h2>{tr}Create new Menu{/tr}</h2>
		{/if}

		<form action="tiki-admin_menus.php?cookietab=1" method="post">
			<input type="hidden" name="menuId" value="{$menuId|escape}" />
			<table class="formcolor">
				<tr>
					<td>
						<label for="menus_name">{tr}Name{/tr}:</label>
					</td>
					<td>
						<input type="text" name="name" id="menus_name" value="{$info.name|escape}" style="width:95%" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="menus_desc">{tr}Description{/tr}:</label>
					</td>
					<td>
						<textarea name="description" id="menus_desc" rows="4" cols="40" style="width:95%">{$info.description|escape}</textarea>
					</td>
				</tr>
				<tr>
					<td><label for="menus_type">{tr}Type{/tr}:</label></td>
					<td>
						<select name="type" id="menus_type">
							<option value="d" {if $info.type eq 'd'}selected="selected"{/if}>{tr}dynamic collapsed{/tr} (d)</option>
							<option value="e" {if $info.type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr} (e)</option>
							<option value="f" {if $info.type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr} (f)</option>
						</select>
					</td>
				</tr>
				{if $prefs.feature_menusfolderstyle eq 'y'}
					<tr>
						<td rowspan="2"><label for="icon">{tr}Icons:{/tr}</label></td>
						<td>
							<div>{tr}Folder Icon{/tr}</div>
							<input type="text" id="icon" name="icon" value="{$info.icon|escape}" style="width:95%" />
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
				{if $prefs.menus_items_icons eq 'y'}
					<tr>
						<td>
							<label for="use_items_icons">
								<input type="checkbox" id="use_items_icons" name="use_items_icons"{if $info.use_items_icons eq 'y'} checked="checked"{/if}/>
								{tr}Configure icons for menu entries{/tr}
							</label>
						</td>
					</tr>
				{/if}
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="save" value="{tr}Save{/tr}" />
					{if $prefs.menus_items_icons neq 'y'}
						<input type="hidden" name="use_items_icons" value="{$info.use_items_icons}" />
					{/if}
				</td>
			</tr>
		</table>

		{remarksbox type="tip" title="{tr}Tip{/tr}"}
			{tr}To use menus in a <a href="tiki-admin_modules.php">module</a>, <a href="tiki-admin.php?page=look">Look and Feel</a> or a template, use {literal}{menu id=x}{/literal}, where x is the ID of the menu.{/tr}
			<hr />
			{tr}To use a menu in a tiki format:{/tr} {literal}{menu id=X}{/literal}
			<br />
			{if $prefs.feature_cssmenus eq 'y'}
				{tr}To use menu in a css/suckerfish format:{/tr}
				<ul>
					<li>{literal}{menu id=X css=y type=vert}{/literal}</li>
					<li>{literal}{menu id=X css=y type=horiz}{/literal}</li>
				</ul>
			{/if}
			{tr}To customize the menu's CSS id (#):{/tr} {literal}{menu id=X css_id=custom_name}{/literal}
		{/remarksbox}
		</form>
	{/tab}
{/tabset}
