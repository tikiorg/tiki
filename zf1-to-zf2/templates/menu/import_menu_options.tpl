{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{remarksbox type="tip" title="{tr}Tips{/tr}"}
		{tr}Menu{/tr}: {$menuInfo.name|escape} ({tr}Id{/tr}: {$menuInfo.menuId|escape})	
		{if $menuSymbol}
			<span class="help-block">
				{tr}Symbol{/tr}:{$menuSymbol.object} ({tr}Profile Name{/tr}:{$menuSymbol.profile}, {tr}Profile Source{/tr}:{$menuSymbol.domain})
			</span>
		{/if}
		<p>
		{tr}To add new options to the menu set the optionId field to 0. To remove an option set the remove field to 'y'.{/tr}
		{tr}Duplicate options will be ignored.{/tr}
	{/remarksbox}
	<form action="{service controller=menu action=import_menu_options menuId=$menuId}" method="post" enctype="multipart/form-data" role="form" class="no-ajax form">
		<div class="form-group">
			<label for="csvfile" class="control-label">
				{tr}File{/tr} 
			</label>
			<input name="csvfile" type="file" required="required">
		</div>
		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="submit" class="btn btn-primary" name="import" value="{tr}Import{/tr}">
			{if isset($confirm)}
				<a class="btn btn-default" href="tiki-admin_menu_options.php?menuId={$menuId}">
					{icon name="list"} {tr}Menu Options{/tr}
				</a>
			{/if}
		</div>
	</form>
{/block}
