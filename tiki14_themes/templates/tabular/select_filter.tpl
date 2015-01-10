{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=tabular action=select_filter trackerId=$trackerId permName=$permName}">
		<div class="form-group">
			<label class="control-label">{tr}Modes{/tr}</label>
			<select name="mode" class="form-control">
				{foreach $collection->getFilters() as $filter}
					<option value="{$filter->getMode()|escape}">{$filter->getLabel()|escape} ({$filter->getMode()|escape})</option>
				{/foreach}
			</select>
		</div>
		<div class="submit">
			<input class="btn btn-primary" type="submit" value="{tr}Add{/tr}">
		</div>
	</form>
{/block}
