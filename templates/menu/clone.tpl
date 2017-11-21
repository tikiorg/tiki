{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form action="{service controller=menu action=clone}" method="post" role="form">
		<div class="form-group">
			<label for="name" class="control-label">
				{tr}Name{/tr}
			</label>
			<div class="">
				<input class="form-control" name="name" id="name" value="{tr _0=$info.name|escape}%0 Copy{/tr}">
				<div class="small">
					{if $info.menuId}
						{tr}Clone of Menu Id{/tr}: {$info.menuId|escape}
					{/if}
					{if $symbol}
						<a class="btn btn-link btn-sm tips" title="{tr}Symbol Information{/tr}|{tr}Symbol{/tr}: <strong>{$symbol.object}</strong><br>{tr}Profile Name{/tr}: <strong>{$symbol.profile}</strong><br>{tr}Profile Source{/tr}: <strong>{$symbol.domain}</strong>">
							{icon name="information"}
						</a>
					{/if}
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">
				{tr}Description{/tr}
			</label>
			<div class="">
				<textarea name="description" id="description" class="form-control">{$info.description|escape}</textarea>
			</div>
		</div>

		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="menuId" value="{$info.menuId|escape}">
			<input type="submit" class="btn btn-primary" name="clone" value="{tr}Clone{/tr}">
		</div>
	</form>
{/block}
