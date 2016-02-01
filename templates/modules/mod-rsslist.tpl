{tikimodule error=$module_params.error title=$tpl_module_title name="rsslist" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div id="rss">
		{if $prefs.feature_wiki eq 'y' and $prefs.feed_wiki eq 'y' and $tiki_p_view eq 'y'}
			<a class="linkmodule tips" title=":{tr}Wiki feed{/tr}" href="tiki-wiki_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Wiki{/tr}
			</a>
		{/if}
		{if $prefs.feature_blogs eq 'y' and $prefs.feed_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
			<a class="linkmodule tips" title=":{tr}Blogs feed{/tr}" href="tiki-blogs_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Blogs{/tr}
			</a>
		{/if}
		{if $prefs.feature_articles eq 'y' and $prefs.feed_articles eq 'y' and $tiki_p_read_article eq 'y'}
			<a class="linkmodule tips" title=":{tr}Articles feed{/tr}" href="tiki-articles_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Articles{/tr}
			</a>
		{/if}
		{if $prefs.feature_galleries eq 'y' and $prefs.feed_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
			<a class="linkmodule tips" title=":{tr}Image Galleries feed{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Image Galleries{/tr}
			</a>
		{/if}
		{if $prefs.feature_file_galleries eq 'y' and $prefs.feed_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
			<a class="linkmodule tips" title=":{tr}File Galleries feed{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}File Galleries{/tr}
			</a>
		{/if}
		{if $prefs.feature_forums eq 'y' and $prefs.feed_forums eq 'y' and $tiki_p_forum_read eq 'y'}
			<a class="linkmodule tips" title=":{tr}Forums feed{/tr}" href="tiki-forums_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Forums{/tr}
			</a>
		{/if}
		{if $prefs.feature_directory eq 'y' and $prefs.feed_directories eq 'y' and $tiki_p_view_directory eq 'y'}
			<a class="linkmodule tips" title=":{tr}Directory feed{/tr}" href="tiki-directories_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Directories{/tr}
			</a>
		{/if}
		{if $prefs.feature_calendar eq 'y' and $prefs.feed_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
			<a class="linkmodule tips" title=":{tr}Calendar feed{/tr}" href="tiki-calendars_rss.php?ver={$prefs.feed_default_version}">
				{icon name="rss"} {tr}Calendars{/tr}
			</a>
		{/if}
		{if $prefs.feature_trackers eq 'y' and $prefs.feed_tracker eq 'y'}
			{foreach from=$rsslist_trackers item="tracker"}
				<a class="linkmodule" href="tiki-tracker_rss.php?ver={$prefs.feed_default_version}&trackerId={$tracker.trackerId}">
					{icon name="rss"} {tr}{$tracker.name}{/tr}
				</a>
			{/foreach}
		{/if}
	</div>
{/tikimodule}

