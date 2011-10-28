<table>
	{foreach from=$fields item=field}
		<tr>
			<th>{$field.name|escape}</th>
			<td>{trackeroutput field=$field item=$item process=y showlinks=n}</td>
		</tr>
	{/foreach}
</table>
