{* $Id: basic.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
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
