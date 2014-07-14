{* $Id$ *}
{title adminpage='login'}{tr}Login Screen{/tr}{/title}
<div class="row"><div class="col-md-6 col-md-offset-3">
<fieldset>
	<legend>{tr}Log in as a registered user{/tr}</legend>
    {module module=login_box
	mode="module"
	show_register="y"
	show_forgot="y"
	error=""
	flip=""
	decorations=""
	nobox=""
	notitle=""}
</fieldset>
</div></div>