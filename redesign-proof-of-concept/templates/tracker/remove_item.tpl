<form method="post" action="{service controller="tracker" action="remove_item"}">
	<p>{tr}Are you sure you want to delete this item?{/tr}</p>
	<div class="submit">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="hidden" name="itemId" value="{$itemId|escape}">
		<input type="submit" value="{tr}Delete item{/tr}">
	</div>
</form>
