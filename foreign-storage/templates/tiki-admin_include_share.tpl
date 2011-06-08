{* $Id$ *}

<form action="tiki-admin.php?page=share" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="commentssetprefs" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_share"}
		{tab name="{tr}General Preferences{/tr}"}

			<fieldset>
				<legend>{tr}Site-wide features{/tr}</legend>

				<div class="admin featurelist">
					{preference name=share_display_links}
					{preference name=share_token_notification}
					{preference name=share_contact_add_non_existant_contact}
					{preference name=share_display_name_and_email}
					{preference name=share_can_choose_how_much_time_access}
					<div class="adminoptionboxchild" id="share_can_choose_how_much_time_access_childcontainer">
						{remarksbox type="remark" title="Default"}
							{tr}If you don't want to limit, an input will be display else it wille be a select{/tr}
						{/remarksbox}
						{preference name=share_max_access_time}
					</div>
				</div>
			</fieldset>
		{/tab}
	{/tabset}

<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="sharesetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>
