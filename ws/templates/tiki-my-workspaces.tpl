<h1>{$WS_title}</h1>

<table class = admin>
<tr>
   <th>Name</th>
   <th>Description</th>
   <th>Path</th>
</tr> 
{foreach from=$listWS item=data}
	<tr>
		<td><a href = {$data.href}>{$data.name}</a></td>
		<td>{$data.description}</td>
		<td>{$data.wspath}</td>
	</tr>
{/foreach}
</table>
{if not empty($prev_page)}
	<a class="button" href = {$prev_page}>Back</a>
{/if}
{if not empty($next_page)}
	<a class="button" href = {$next_page}>Next</a>
{/if}