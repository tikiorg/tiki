{jq}
$jq("#editItemForm{{$trackerEditFormId}}").validate({
	rules: {
		{{$validationjs}}
	}
});
{/jq}