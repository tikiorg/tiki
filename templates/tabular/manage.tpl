{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
	</div>
{/block}

{block name="content"}
	<table class="table">
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Tracker{/tr}</th>
			<th>{tr}Actions{/tr}</th>
		</tr>
		{foreach $list as $row}
			<tr>
				<td><a href="{service controller=tabular action=edit tabularId=$row.tabularId}">{icon name=edit}{$row.name|escape}</a></td>
				<td>{object_title type=tracker id=$row.trackerId}</td>
				<td>
					<a href="{service controller=tabular action=export_full_csv tabularId=$row.tabularId}">{icon name=export}{tr}Export{/tr}</a>
					<a href="{bootstrap_modal controller=tabular action=import_csv tabularId=$row.tabularId}">{icon name=import}{tr}Import{/tr}</a>
					<a class="text-danger" href="{bootstrap_modal controller=tabular action=delete tabularId=$row.tabularId}">{icon name=delete}<span class="sr-only">{tr}Delete{/tr}</span></a>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="3">{tr}No tabular formats defined.{/tr}</td>
			</tr>
		{/foreach}
	</table>
{/block}
