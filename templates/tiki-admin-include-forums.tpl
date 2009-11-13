{* $Id$ *}
{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To add/remove forums, look for "Admin forums" under "Forums" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_forums.php">{tr}Click Here{/tr}</a>.
{/remarksbox}

<form method="post" action="tiki-admin.php?page=forums">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_forums"}
		{tab name="{tr}General Settings{/tr}"}
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="home_forum">{tr}Home Forum (main forum){/tr}</label>
					<select name="home_forum" id="home_forum">
						{section name=ix loop=$forums}
							<option value="{$forums[ix].forumId|escape}" {if $forums[ix].forumId eq $prefs.home_forum}selected="selected"{/if}>{$forums[ix].name|truncate:20:"...":true|escape}</option>
						{sectionelse}
							<option value="">{tr}None{/tr}</option>
						{/section}
					</select>
					{if $forums}
						<input type="submit" name="homeforumprefs" value="{tr}Set{/tr}" />
					{else}
						<a href="tiki-admin_forums.php" class="button" title="{tr}Create a forum{/tr}"> {tr}Create a forum{/tr} </a>
					{/if}
				</div>
			</div>

			<fieldset>
				<legend>
					{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Forum+Admin"}{/if}
				</legend>
				<input type="hidden" name="forumprefs" />
				{preference name=feature_forum_rankings}
				{preference name=feature_forum_parse}
				{preference name=feature_forum_topics_archiving}
				{preference name=feature_forum_quickjump}
				{preference name=feature_forum_replyempty}
				<em>{tr}If disabled, replies will quote the original post{/tr}.</em>
				{preference name=forum_comments_no_title_prefix}
				{preference name=feature_forums_allow_thread_titles}
				<em>{tr}Will be a thread title{/tr}.</em>
				{preference name=forum_match_regex}
			</fieldset>

			<fieldset>
				<legend>{tr}Threads{/tr}</legend>
				<input type="hidden" name="forumthreadprefs" />
				{preference name=forum_thread_defaults_by_forum}
				{preference name=forum_thread_user_settings}
				<em>{tr}Allows users to override the defaults{/tr}.</em>
				<div class="adminoptionboxchild" id="forum_thread_user_settings_childcontainer">
					{preference name=forum_thread_user_settings_keep}
					{preference name=forum_thread_user_settings_threshold}
				</div>
				{preference name=forum_comments_per_page}
				{preference name=forum_thread_style}
				{preference name=forum_thread_sort_mode}
			</fieldset>

			<fieldset>
				<legend>{tr}Searches{/tr}</legend>
				{preference name=feature_forums_name_search}
				<em>{tr}When listing forums{/tr}.</em>
				{preference name=feature_forums_search}
				<em>{tr}When listing forums{/tr}.</em>
				{preference name=feature_forum_content_search}
				<div class="adminoptionboxchild" id="feature_forum_content_search_childcontainer">
					{preference name=feature_forum_local_tiki_search}
					{preference name=feature_forum_local_search}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Forums Listing{/tr}"}
			<input type="hidden" name="forumlistprefs" />
			{preference name=forums_ordering}
			{tr}Select which items to display when listing forums{/tr}:
			{preference name=forum_list_topics}
			{preference name=forum_list_posts}
			{preference name=forum_list_ppd}
			{preference name=forum_list_lastpost}
			{preference name=forum_list_visits}
			{preference name=forum_list_desc}
			<div class="adminoptionboxchild" id="forum_list_desc_childcontainer">
				{preference name=forum_list_description_len}
			</div>
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
