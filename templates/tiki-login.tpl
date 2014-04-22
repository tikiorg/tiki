{* $Id$ *}
{title adminpage='login'}{tr}Login Screen{/tr}{/title}
<fieldset>
	<legend>{tr}Log in as a registered user{/tr}</legend>
	<div class="col-md-4 col-md-push-4">
	{module module=login_box
	mode="module"
	show_register="y"
	show_forgot="y"
	error=""
	flip=""
	decorations=""
	nobox=""
	notitle=""}
	</div>
</fieldset>
