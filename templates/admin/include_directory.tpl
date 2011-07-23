{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To configure your directory, find "Admin Directory" under "Directory" on the application menu, or{/tr} <a class="rbox-link" href="tiki-directory_admin.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form action="tiki-admin.php?page=directory" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" name="directory" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset class="admin">
		<legend>{tr}Directory{/tr}</legend>
		{preference name=directory_columns}
		{preference name=directory_links_per_page}
		{preference name=directory_validate_urls}
		{preference name=directory_cool_sites}
		{preference name=directory_country_flag}
		{preference name=directory_open_links}
	</fieldset>
	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" name="directory" value="{tr}Change preferences{/tr}" />
	</div>
</form>
