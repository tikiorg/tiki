{* $Id$ *}
<form role="form" class="form-horizontal" action="tiki-admin.php?page=blogs" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="t_navbar margin-bottom-md clearfix">
		<a role="button" class="btn btn-link" href="tiki-list_blogs.php" title="{tr}List{/tr}">
			{icon name="list"} {tr}Blogs{/tr}
		</a>
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</div>
	{tabset name="admin_blogs"}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>
			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_blogs visible="always"}
			</fieldset>
			<fieldset>
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_bloglist}
			</fieldset>
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
				{preference name=geo_locate_blogpost}
				{preference name=feature_sefurl_title_blog}
				{preference name=blog_feature_copyrights}
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
			<h2>{tr}Blogs Listings{/tr}</h2>
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
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
