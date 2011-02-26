{* $Id$ *}
<table>
	<caption>Count: {{$count|escape}}</caption>
	<tr>
		<th>Object</th>
		<th>Type</th>
	</tr>
	{{foreach from=$results item=row}}
		<tr>
			<td>{{$row.object_id|escape}}</td>
			<td>{{$row.object_type|escape}}</td>
		</tr>
	{{/foreach}}
</table>
