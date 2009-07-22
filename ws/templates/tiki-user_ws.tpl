<h1>{$rWS_title}</h1>

<table>
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

<p> "I am still learning how to deal with smartasses..." </p>
