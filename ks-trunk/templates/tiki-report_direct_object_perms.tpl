<h1>Wiki pages with direct object permissions</h1>
<a href="tiki-list_object_permissions.php">Click here to access list of permissions for all objects.</a>
<table class="normal">
	<tr>
		<th>Pagename</th>
	</tr>
	{foreach from=$pagesWithDirectPerms item=pageName }
		<tr>
			<td><a href="tiki-index.php?page={$pageName}">{$pageName}</a></td>
		</tr>
	{/foreach}
</table>

