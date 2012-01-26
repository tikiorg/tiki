{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove polls, look for "Polls" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_polls.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form method="post" action="tiki-admin.php?page=polls">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="calprefs" value="{tr}Change settings{/tr}" />
	</div>
	<fieldset>
		<legend>{tr}Polls{/tr}{help url="Polls+Config"}</legend>
		<input type="hidden" name="pollprefs" />
		{preference name=feature_poll_anonymous}
		{preference name=feature_poll_revote}
		{preference name=feature_poll_comments}
		<div class="adminoptionboxchild" id="feature_poll_comments_childcontainer">
			{preference name=poll_comments_per_page}
			{preference name=poll_comments_default_ordering}
		</div>	
		{preference name=poll_list_categories}
		{preference name=poll_list_objects}
		{preference name=poll_multiple_per_object}
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="calprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>
