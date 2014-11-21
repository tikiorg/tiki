{* $Id$ *}
{if isset($validationjs)}{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	errorClass: "label label-warning",
	errorPlacement: function(error, element) {
		if ($(element).parents('.input-group').length > 0) {
			error.insertAfter($(element).parents('.input-group').first());
		} else if ($(element).parents('.has-error').length > 0) {
			error.appendTo($(element).parents('.has-error').first());
		} else {
			error.insertAfter(element);
		}
	},
	highlight: function(element) {
		$(element).parents('div, p').first().addClass('has-error');
	},
	unhighlight: function(element) {
		$(element).parents('div, p').first().removeClass('has-error');
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
