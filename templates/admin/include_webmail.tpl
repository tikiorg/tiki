<form action="tiki-admin.php?page=webmail" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="webmail" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>
		{preference name=webmail_view_html}
		{preference name=webmail_max_attachment}
		{preference name=webmail_quick_flags}
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="webmail" value="{tr}Change preferences{/tr}" />
	</div>
</form>
