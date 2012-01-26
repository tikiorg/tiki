{* $Id: tracker_validator.tpl 38028 2011-10-06 19:44:16Z jonnybradley $ *}
{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	ignore: '.ignore',
	submitHandler: function(){process_submit(this.currentForm);}
});
{/jq}
{jq}
process_submit = function(me) {
	if (!$(me).attr("is_validating")) {
		$(me).attr("is_validating", true);
		$(me).validate();
	}
	if ($(me).validate().pendingRequest > 0) {
		setTimeout(function() {process_submit(me);}, 500);
		return false;
	}
	$(me).attr("is_validating", false);

	// disable submit button(s)
	// FIXME after 8.0b1 - this seems to prevent the "save" field being sent in the request on webkit
	//$(me).find("input[type=submit]").attr("disabled", true);

	me.submit();
};
{/jq}
