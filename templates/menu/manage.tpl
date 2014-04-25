{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form action="{service controller=menu action=manage}" method="post" role="form" class="form">
		{ticket}
		<div class="form-group">
			<label for="menus_name" class="control-label">
				{tr}Name{/tr}
			</label>
			<input type="text" name="name" id="menus_name" value="{$info.name|escape}" class="form-control" required="required">
			<p class="help-block">
				{if $info.menuId}
					{tr}Id{/tr}: {$info.menuId|escape}
				{/if}
				{if $symbol}
					{tr}Symbol{/tr}:{$symbol.object} ({tr}Profile Name{/tr}:{$symbol.profile}, {tr}Profile Source{/tr}:{$symbol.domain})
				{/if}	
			</p>
		</div>
		<div class="form-group">
			<label for="menus_desc" class="control-label">
				{tr}Description{/tr}
			</label>
			<textarea name="description" id="menus_desc" class="form-control">{$info.description|escape}</textarea>
		</div>
		<div class="form-group">
			<label for="menus_type" class="control-label">
				{tr}Type{/tr}
			</label>
			<select name="type" id="menus_type" class="form-control">
				<option value="d" {if $info.type eq 'd'}selected="selected"{/if}>{tr}dynamic collapsed{/tr} (d)</option>
				<option value="e" {if $info.type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr} (e)</option>
				<option value="f" {if $info.type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr} (f)</option>
			</select>
		</div>
		<div class="form-group">
			<label for="menus_parse">
				<input type="checkbox" name="parse" id="menus_parse"{if $info.parse eq 'y'} checked="checked"{/if} value="1">
				{tr}Wiki Parse{/tr}
			</label>
			<em>{tr}Labels of all options will be wiki parsed.{/tr}</em>
		</div>
		{if $prefs.feature_menusfolderstyle eq 'y'}
			<div class="form-group">
				<label for="icon" class="control-label">
					{tr}Folder Icon{/tr}
				</label>
				<input type="text" id="icon" name="icon" value="{$info.icon|escape}" class="form-control">
				<em>{tr}Path and filename of closed folder icon{/tr}</em>.
				{remarksbox type="tip" title="{tr}Note{/tr}"}
					{tr}To use custom folder icons in menus, enter the path to the icon for the <strong>closed</strong> folder.{/tr} {tr}In the same directory, include an icon for the opened folder.{/tr} {tr}The "opened folder" icon name must be identical to the "closed folder" icon name, prefixed with the letter <strong>o</strong>.{/tr}
					<hr>
					{tr}For example, the default icon is: img/icons/folder.png{/tr} {icon _id="folder"}
					<br>
					{tr}The name of the "open folder" icon is: img/icons/ofolder.png{/tr} {icon _id="ofolder"}
				{/remarksbox}
			</div>
		{/if}
		{if $prefs.menus_items_icons eq 'y'}
			<div class="form-group">
				<label for="use_items_icons">
					<input type="checkbox" id="use_items_icons" name="use_items_icons" {if $info.use_items_icons eq 'y'} checked="checked"{/if} value="1">
					{tr}Configure icons for menu entries{/tr}
				</label>
			</div>
		{/if}
		{remarksbox type="tip" title="{tr}Tip{/tr}"}
			{tr}You can use menus by assigning the menu <a href="tiki-admin_modules.php">module</a> (to the top, left, right, etc.), or you can customize a template, using {literal}{menu id=x}{/literal}, where x is the ID of the menu.{/tr}
			<hr>
			{tr}To use a menu in a tiki format:{/tr} {literal}{menu id=X}{/literal}
			<br>
			{if $prefs.feature_cssmenus eq 'y'}
				{tr}To use menu in a css/suckerfish format:{/tr}
				<ul>
					<li>{literal}{menu id=X css=y type=vert}{/literal}</li>
					<li>{literal}{menu id=X css=y type=horiz}{/literal}</li>
				</ul>
			{/if}
			{tr}To customize the menu's CSS id (#):{/tr} {literal}{menu id=X css_id=custom_name}{/literal}
		{/remarksbox}
		<div class="submit">
			{if $prefs.menus_items_icons neq 'y'}
				<input type="hidden" name="use_items_icons" value="{$info.use_items_icons}">
			{/if}
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="menuId" value="{$info.menuId|escape}">
			<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
		</div>
	</form>
{/block}
