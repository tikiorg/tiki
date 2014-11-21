{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=tabular action=select trackerId=$trackerId permName=$permName}">
		<div class="form-group">
			<label class="control-label">{tr}Modes{/tr}</label>
			<select name="mode" class="form-control">
				{foreach $schema->getColumns() as $column}
					<option value="{$column->getMode()|escape}">{$column->getLabel()|escape} ({$column->getMode()|escape})</option>
				{/foreach}
			</select>
		</div>
		<div class="submit">
			<input class="btn btn-primary" type="submit" value="{tr}Add{/tr}">
		</div>
	</form>
{/block}
