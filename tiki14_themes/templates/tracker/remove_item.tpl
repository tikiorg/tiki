{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller="tracker" action="remove_item"}">
	{if $affectedCount}
		<div class="form-group">
			<label class="control-label" for="replacement">{tr}Replacement{/tr}</label>
			{object_selector _id=replacement _simplename=replacement type=trackeritem tracker_id=$trackerId}
			<div class="help-block">
				{tr _0=$affectedCount}%0 other item(s) currently refer to the element you are trying to delete. They will be replaced by this one.{/tr}
			</div>
		</div>
	{/if}
	<p>{tr}Are you sure you want to delete this item?{/tr}</p>
	<div class="submit">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="hidden" name="itemId" value="{$itemId|escape}">
		<input type="submit" class="btn btn-danger" value="{tr}Delete item{/tr}">
	</div>
</form>
{/block}
