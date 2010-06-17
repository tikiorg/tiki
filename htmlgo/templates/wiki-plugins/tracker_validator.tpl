{jq}
$jq("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}}
});
{/jq}