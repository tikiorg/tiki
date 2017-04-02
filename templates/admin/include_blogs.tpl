{* $Id$ *}
<form role="form" class="form-horizontal" action="tiki-admin.php?page=blogs" method="post">
	{include file='access/include_ticket.tpl'}
	<div class="t_navbar margin-bottom-md clearfix">
		<a role="button" class="btn btn-link tips" href="tiki-list_blogs.php" title=":{tr}Blogs listing{/tr}">
			{icon name="list"} {tr}Blogs{/tr}
		</a>
		{include file='admin/include_apply_top.tpl'}
	</div>
	{tabset name="admin_blogs"}
		{tab name="{tr}General Settings{/tr}"}
			<br>
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
			<br>
			{preference name=blog_list_order}
			<fieldset>
				<legend>{tr}Items to display{/tr}</legend>
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
	{include file='admin/include_apply_bottom.tpl'}
</form>
