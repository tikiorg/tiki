{* $Id$ *}
{jq}
$jq("#editItemForm{{$trackerEditFormId}}").submit(function(evt){
	if (!$jq(this).attr("is_validating")) {
		$jq(this).attr("is_validating", true);
		$jq(this).validate();
	}
	if ($jq(this).validate().pendingRequest > 0) {
		setTimeout(function(){$jq(this).submit();}, 500);
		return false;
	}
	$jq(this).attr("is_validating", false);
	return $jq(this).valid();
}).validate({
	{{$validationjs}}
});
{/jq}