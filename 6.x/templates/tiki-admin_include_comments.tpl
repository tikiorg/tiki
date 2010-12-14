{* $Id$ *}

<div class="navbar">
	{button href="tiki-list_comments.php" _text="{tr}List comments{/tr}"}
</div>


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
					{preference name=comments_vote}				
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Display options{/tr}</legend>

				<div class="admin featurelist">
					{preference name=comments_notitle}
					{preference name=comments_field_email}
					{preference name=comments_field_website}
					{preference name=default_rows_textarea_comment}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Permissions{/tr}</legend>
					<a class="link" href="tiki-objectpermissions.php?textFilter=comment&amp;show_disabled_features=y" title="{tr}Permission{/tr}">{tr}Permissions{/tr} {icon _id="key" alt="{tr}Permission{/tr}"}</a>
			</fieldset>

			<fieldset>
				<legend>{tr}Inline comments{/tr}</legend>
					<a class="link" href="{$prefs.helpurl}Inline+comments">{tr}Inline comments{/tr}</a>

				{tr}The feature below must be activated for this feature to work.{/tr}
				{preference name=feature_wiki_paragraph_formatting}
			</fieldset>

			<fieldset>
				<legend>{tr}Using comments in various features{/tr}</legend>

				<div class="admin">
					{preference name=feature_article_comments}

					{preference name=feature_wiki_comments}
					<div class="adminoptionboxchild" id="feature_wiki_comments_childcontainer">
						{preference name=wiki_comments_displayed_default}
						{preference name=wiki_comments_per_page}
						{preference name=wiki_comments_default_ordering}
						{preference name=wiki_comments_allow_per_page}
						{preference name=wiki_watch_comments}
					</div>

					{preference name=feature_blogposts_comments}
					{preference name=feature_file_galleries_comments}
					<div class="adminoptionboxchild" id="feature_file_galleries_comments_childcontainer">
						{preference name='file_galleries_comments_per_page'}
						{preference name='file_galleries_comments_default_ordering'}
					</div>
					{preference name=feature_poll_comments}
					{preference name=feature_faq_comments}
					{preference name=wikiplugin_trackercomments}
				</div>
			</fieldset>

		{/tab}
{/tabset}

<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="commentssetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>
