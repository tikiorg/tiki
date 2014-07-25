{* $Id$ *}
{if isset($validationjs)}{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	highlight: function(element) {
		$(element).parent('div, p').addClass('has-error');
	},
	unhighlight: function(element) {
		$(element).parent('div, p').removeClass('has-error');
	},
	showErrors: function(errorMap, errorList) {
		this.defaultShowErrors();
		$.each(this.successList, function(index, value) {
			return $(value).popover("hide");
		});
		return $.each(errorList, function(index, value) {
			var _popover;
			_popover = $(value.element).popover({
				trigger: "manual",
				placement: "top",
				content: value.message,
				template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
		});
		_popover.data("bs.popover").options.content = value.message;
		return $(value.element).popover("show");
		});
	},
	
	ignore: '.ignore',
	submitHandler: function(){
		if( typeof nosubmitItemForm{{$trackerEditFormId}} !== "undefined" && nosubmitItemForm{{$trackerEditFormId}} == true ) {
			return false;
		} else {
			process_submit(this.currentForm);
		}
	}
});
{/jq}{/if}
