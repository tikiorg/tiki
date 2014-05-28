{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form class="no-ajax inline-edit-dialog" method="post" action="{service controller=edit action=inline_dialog}">
		{foreach $fields as $field}
			<div class="form-group">
				<label>{$field.label|escape}</label>
				{$field.field}
			</div>
		{/foreach}
		<div class="submit">
			<button class="btn btn-primary">{tr}Save{/tr}</button>
		</div>
	</form>
{/block}
