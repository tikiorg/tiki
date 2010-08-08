{* $Id$ *}
{jq}
$jq("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	submitHandler: function(){process_submit(this.currentForm);}
});
process_submit = function(me) {
	if (!$jq(me).attr("is_validating")) {
		$jq(me).attr("is_validating", true);
		$jq(me).validate();
	}
	if ($jq(me).validate().pendingRequest > 0) {
		setTimeout(function() {process_submit(me);}, 500);
		return false;
	}
	$jq(me).attr("is_validating", false);
	me.submit();
}
{/jq}