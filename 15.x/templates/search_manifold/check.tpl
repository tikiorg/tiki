{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<table class="table">
		<tr>
			<th>{tr}Index{/tr}</th>
			<th>{tr}Type{/tr}</th>
			<th>{tr}Created{/tr}</th>
			<th>{tr}Defined{/tr}</th>
			<th>{tr}Valid{/tr}</th>
		</tr>
		{foreach $instances as $row}
			<tr>
				<td>{$row.name|escape}</td>
				<td>{$row.type|escape}</td>
				<td>
					{if $row.indexExists}
						{icon name="ok"}
					{else}
						<a href="{service controller=search_manifold action=create_index index=$row.name type=$row.type}">{tr}Create{/tr}</a>
					{/if}
				</td>
				<td>
					{if $row.typeExists}
						{icon name="ok"}
					{elseif $row.indexExists}
						<a href="{service controller=search_manifold action=create_index index=$row.name type=$row.type}">{tr}Create{/tr}</a>
					{/if}
				</td>
				<td>
					{if $row.valid}
						{icon name="ok"}
					{elseif $row.typeExists}
						<a href="{service controller=search_manifold action=create_index index=$row.name type=$row.type}" class="text-danger" title="{tr}May not work or corrupt data{/tr}">{icon name="warning"} {tr}Alter{/tr}</a>
					{/if}
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="3">{tr}No instances configured{/tr}</td>
			</tr>
		{/foreach}
	</table>
{/block}
