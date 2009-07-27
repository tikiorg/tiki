<h1>{$WS_title}</h1>

<table class="admin">
<tr>
   <th>Object Name</th>
   <th>Type</th>
   <th>Description</th>
</tr> 
{foreach from=$resources item=data}
	<tr>
		<td>
			{if $data.userCanView eq "y"}
				<a href = {$data.href}>{$data.name}</a>
			{else}
				{$data.name}
			{/if}
		</td>
		<td>{$data.type}</td>
		<td>{$data.description}</td>
	</tr>
{/foreach}
</table>
{if not empty($prev_page)}
	<a class="button" href = {$prev_page}>Back</a>
{/if}
{if not empty($next_page)}
	<a class="button" href = {$next_page}>Next</a>
{/if}