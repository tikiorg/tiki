{* $Id$ *}

<form action="tiki-admin.php?page=comments" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="commentssetprefs" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_wiki"}
		{tab name="{tr}General Preferences{/tr}"}

			<fieldset>
				<legend>{tr}Site-wide features{/tr}</legend>

				<div class="admin featurelist">
					{preference name=feature_comments_moderation}
					{preference name=feature_comments_locking}
					{preference name=feature_comments_post_as_anonymous}				
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Display options{/tr}</legend>

				<div class="admin featurelist">
					{preference name=comments_notitle}
				</div>
			</fieldset>

		{/tab}
{/tabset}

<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="commentssetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>
