{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form action="{service controller=menu action=clone_menu}" method="post" role="form" class="form-horizontal">
		<div class="well well-sm">
			<div class="form-group">
				<label for="menus_name" class="control-label col-sm-2">
					{tr}Name{/tr}
				</label>
				<div class="form-control-static col-sm-10">
					{$info.name|escape}
					<span class="help-block">
						{if $info.menuId}
							{tr}Id{/tr}: {$info.menuId|escape}
						{/if}
						{if $symbol}
							{tr}Symbol{/tr}:{$symbol.object} ({tr}Profile Name{/tr}:{$symbol.profile}, {tr}Profile Source{/tr}:{$symbol.domain})
						{/if}	
					</span>
				</div>
			</div>
			<div class="form-group">
				<label for="menus_desc" class="control-label col-sm-2">
					{tr}Description{/tr}
				</label>
				<div class="form-control-static col-sm-10">
					{$info.description|escape}
				</div>
			</div>
		</div>

		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="menuId" value="{$info.menuId|escape}">
			<input type="submit" class="btn btn-primary" name="clone" value="{tr}Clone{/tr}">
		</div>
	</form>
{/block}
