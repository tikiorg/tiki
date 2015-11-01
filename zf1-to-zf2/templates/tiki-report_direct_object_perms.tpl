<h1 class="pagetitle">Wiki pages with direct object permissions</h1>
<a href="tiki-list_object_permissions.php">Click here to access list of permissions for all objects.</a>
<div class="table-responsive">
<table class="table">
	<tr>
		<th>Pagename</th>
	</tr>
	{foreach from=$pagesWithDirectPerms item=pageName }
		<tr>
			<td><a href="tiki-index.php?page={$pageName}">{$pageName}</a></td>
		</tr>
	{/foreach}
</table>
</div>
