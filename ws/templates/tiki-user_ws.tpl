<h1>{$WS_title}</h1>

<table class="admin">
<tr>
   <th>Object Name</th>
   <th>Type</th>
   <th>Description</th>
</tr> 
{foreach from=$resources item=data}
	<tr>
		<td><a href = {$data.href}>{$data.name}</a></td>
		<td>{$data.type}</td>
		<td>{$data.description}</td>
	</tr>
{/foreach}
</table>
