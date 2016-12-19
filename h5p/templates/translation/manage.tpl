{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $translations|@count}
		<label>
			{tr}Current translation set{/tr}
		</label>
		<ul>
			{foreach from=$translations item=trans}
				<li>
					{object_link type=$type id=$trans.objId}, {$trans.language|escape}
					{permission type=$type object=$trans.objId name=detach_translation}
						<a class="confirm-prompt" title="{tr}Detach translation{/tr}" href="{bootstrap_modal controller=translation action=detach type=$type source=$source target=$trans.objId}" data-confirm="{tr}Are you sure you want to detach the translation?{/tr}">{icon name="remove"}</a>
					{/permission}
				</li>
			{/foreach}
		</ul>
	{else}
		<div class="well well-sm">
			{tr}No translations available at this time.{/tr}
		</div>
	{/if}

	{if $canAttach}
		{if $filters.language}
			<form class="simple" method="post" action="{service controller=translation action=attach}">
				<div class="form-group">
					<label class="control-label">
						{tr}Add a new object to the set{/tr}
					</label>
					{object_selector _name=target _filter=$filters}
				</div>
				<div class="submit">
					<input type="hidden" name="type" value="{$type|escape}">
					<input type="hidden" name="source" value="{$source|escape}">
					<input type="submit" class="btn btn-primary" value="{tr}Add{/tr}">
				</div>
			</form>
		{else}
			<div class="well well-sm">
				{tr}All possible translations exist.{/tr}
			</div>
		{/if}
	{/if}
{/block}
