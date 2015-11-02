{* $Id$ *}
{if isset($validationjs)}{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	submitHandler: function(){
		if( typeof nosubmitItemForm{{$trackerEditFormId}} !== "undefined" && nosubmitItemForm{{$trackerEditFormId}} == true ) {
			return false;
		} else {
			process_submit(this.currentForm);
		}
	}
});
{/jq}{/if}
