{* $Id$ *}
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
	$(me).find("input[type=submit]").attr("disable", true).css("opacity",0.5)
	me.submit();
};
{/jq}
