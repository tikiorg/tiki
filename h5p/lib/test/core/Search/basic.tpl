{* $Id$ *}
<table>
	<caption>{$foo.bar|default:'Count'}: {$count|escape}</caption>
	<tr>
		<th>Object</th>
		<th>Type</th>
	</tr>
	{foreach from=$results item=row}
		<tr>
			<td>{$row.object_id}</td>
			<td>{$row.object_type|escape}</td>
		</tr>
	{/foreach}
</table>
