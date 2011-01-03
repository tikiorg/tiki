{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To add/remove blogs, go to "Create/Edit Blog" under "Blogs" on the application menu, or{/tr} <a class="rbox-link" href="tiki-edit_blog.php">{tr}Click Here{/tr}</a>.
{/remarksbox}

<form action="tiki-admin.php?page=blogs" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_blogs"}
		{tab name="{tr}General Settings{/tr}"}
			{preference name=home_blog}
			<fieldset>
				<legend>
					{tr}Features{/tr}{help url="Blog+Config"}
				</legend>
				<input type="hidden" name="blogfeatures" />
				{preference name=feature_blog_rankings}
				{preference name=feature_blog_heading}
				{preference name=feature_blog_edit_publish_date}

				{if $prefs.feature_categories eq 'y'}
					{preference name=feature_blog_mandatory_category}
				{/if}
			</fieldset>

			<fieldset>
				<legend>{tr}Comments{/tr}</legend>
				<input type="hidden" name="blogcomprefs" />
				{preference name=feature_blogposts_comments}
				{preference name=blog_comments_per_page}
				{preference name=blog_comments_default_ordering}
			</fieldset>

			<fieldset>
				<legend>
					{tr}Sharing on social networks{/tr}{help url="Social+Networks#Using+ShareThis"}
				</legend>
				{preference name=feature_blog_sharethis}
				<div class="adminoptionboxchild" id="feature_blog_sharethis_childcontainer">
					{preference name=blog_sharethis_publisher}
				</div>
			</fieldset>

		{/tab}
	
		{tab name="{tr}Blogs Listings{/tr}"}
			<input type="hidden" name="bloglistconf" />
			{preference name=blog_list_order}

			<fieldset>
				<legend>{tr}Select which items to display when listing blogs:{/tr}</legend>
				{preference name=blog_list_title}
				<div class="adminoptionboxchild" id="blog_list_title_childcontainer">
					{preference name=blog_list_title_len}
				</div>
				{preference name=blog_list_description}
				{preference name=blog_list_created}
				{preference name=blog_list_lastmodif}
				{preference name=blog_list_user}
				{preference name=blog_list_posts}
				{preference name=blog_list_visits}
				{preference name=blog_list_activity}
			</fieldset>
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
