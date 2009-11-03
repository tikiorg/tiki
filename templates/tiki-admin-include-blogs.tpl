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
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="home_forum">{tr}Home Blog (main blog){/tr}</label>
					<select name="homeBlog" id="blogs-home"{if !$blogs} disabled="disabled"{/if}>
						{section name=ix loop=$blogs}
							<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $prefs.home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:$prefs.blog_list_title_len:"...":true|escape}</option>
						{sectionelse}
							<option value="" disabled="disabled" selected="selected">{tr}None{/tr}</option>
						{/section}
					</select>
					{if $blogs}
						<input type="submit" name="blogset" value="{tr}Set{/tr}" />
					{else}
						<a href="tiki-edit_blog.php" class="button" title="{tr}Create a blog{/tr}"> {tr}Create a blog{/tr} </a>
					{/if}
				</div>
			</div>

			<fieldset>
				<legend>
					{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Blog+Config"}{/if}
				</legend>
				<input type="hidden" name="blogfeatures" />
				{preference name=feature_blog_rankings}
				{preference name=blog_spellcheck}
				<em>{tr}Requires a separate download{/tr}. </em>
				{preference name=feature_blog_heading}

				{if $prefs.feature_categories eq 'y'}
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="blog_mandatory_category"> {tr}Force and limit categorization to within subtree of{/tr}:</label>
							<select name="feature_blog_mandatory_category" id="blog_mandatory_category">
								<option value="-1" {if $prefs.feature_blog_mandatory_category eq -1 or $prefs.feature_blog_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
								<option value="0" {if $prefs.feature_blog_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
								{section name=ix loop=$catree}
									<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_blog_mandatory_category}selected="selected"{/if}>{$catree[ix].categpath}</option>
								{/section}
							</select>
						</div>
					</div>
				{/if}
			</fieldset>

			<fieldset>
				<legend>{tr}Comments{/tr}</legend>
				<input type="hidden" name="blogcomprefs" />
				{preference name=feature_blog_comments}
				{preference name=feature_blogposts_comments}
				{preference name=blog_comments_per_page}
				{preference name=blog_comments_default_ordering}
			</fieldset>

			<fieldset>
				<legend>
					{tr}Trackback pings{/tr}{if $prefs.feature_help eq 'y'} {help url="Blog#About_Trackback"}{/if}
				</legend>
				{preference name=feature_trackbackpings}
				{preference name=feature_blogposts_pings}
			</fieldset>
		{/tab}
	
		{tab name="{tr}Blogs Listings{/tr}"}
			<input type="hidden" name="bloglistconf" />
			{preference name=blog_list_order}
			{tr}Select which items to display when listing blogs{/tr}:
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
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
