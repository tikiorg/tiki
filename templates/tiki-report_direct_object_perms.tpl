<h1>Wiki pages with direct object permissions</h1>
<table>
	<tr>
		<th>Pagename</th>
	</tr>
	{foreach from=$pagesWithDirectPerms item=pageName }
		<tr>
			<td><a href="tiki-index.php?page={$pageName}">{$pageName}</a></td>
		</tr>
	{/foreach}
</table>

