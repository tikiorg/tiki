{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		{permission name=admin_trackers}
			<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
		{/permission}
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
				<td><a href="{service controller=tabular action=list tabularId=$row.tabularId}">{$row.name|escape}</a></td>
				<td>{object_title type=tracker id=$row.trackerId}</td>
				<td>
					<a href="{service controller=tabular action=edit tabularId=$row.tabularId}">{icon name=edit}{tr}Edit{/tr}</a>
					{permission_link type=tabular id=$row.tabularId title=$row.name mode=text}
					<a href="{service controller=tabular action=export_full_csv tabularId=$row.tabularId}">{icon name=export}{tr}Full{/tr}</a>
					<a href="{bootstrap_modal controller=tabular action=filter target=export tabularId=$row.tabularId}">{icon name=export}{tr}Partial{/tr}</a>
					<a href="tiki-searchindex.php?tabularId={$row.tabularId|escape}&amp;filter~tracker_id={$row.trackerId|escape}">{icon name=export}{tr}Custom{/tr}</a>
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
