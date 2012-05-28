{* $Id$ *}
{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	ignore: '.ignore',
	submitHandler: function(){process_submit(this.currentForm);}
});
{/jq}
