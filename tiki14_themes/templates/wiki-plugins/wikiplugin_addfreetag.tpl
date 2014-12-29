<form action="{$smarty.server.PHP_SELF}?{query}" method="post" class="form-inline">
	<div class="form-group">
		<input type="text" class="form-control" name="{$wp_addfreetag|escape}">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Add Tag{/tr}">
	</div>
</form>
