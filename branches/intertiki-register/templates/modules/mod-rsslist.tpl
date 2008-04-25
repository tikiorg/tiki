{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}RSS Feeds{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="rsslist" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <div id="rss">
    {if $prefs.feature_wiki eq 'y' and $prefs.rss_wiki eq 'y' and $tiki_p_view eq 'y'}
        <a title="{tr}Wiki RSS{/tr}" href="tiki-wiki_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Wiki{/tr}</small><br />
    {/if}
    {if $prefs.feature_blogs eq 'y' and $prefs.rss_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
        <a title="{tr}Blogs RSS{/tr}" href="tiki-blogs_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Blogs{/tr}</small><br />
    {/if}
    {if $prefs.feature_articles eq 'y' and $prefs.rss_articles eq 'y' and $tiki_p_read_article eq 'y'}
        <a title="{tr}Articles RSS{/tr}" href="tiki-articles_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Articles{/tr}</small><br />
    {/if}
    {if $prefs.feature_galleries eq 'y' and $prefs.rss_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
        <a title="{tr}Image Galleries RSS{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Image Galleries{/tr}</small><br />
    {/if}
    {if $prefs.feature_file_galleries eq 'y' and $prefs.rss_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
        <a title="{tr}File Galleries RSS{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}File Galleries{/tr}</small><br />
    {/if}
    {if $prefs.feature_forums eq 'y' and $prefs.rss_forums eq 'y' and $tiki_p_forum_read eq 'y'}
        <a title="{tr}Forums RSS{/tr}" href="tiki-forums_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Forums{/tr}</small><br />
    {/if}
    {if $prefs.feature_maps eq 'y' and $prefs.rss_mapfiles eq 'y' and $tiki_p_map_view eq 'y'}
        <a title="{tr}Maps RSS{/tr}" href="tiki-map_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Maps{/tr}</small><br />
    {/if}
    {if $prefs.feature_directory eq 'y' and $prefs.rss_directories eq 'y' and $tiki_p_view_directory eq 'y'}
        <a href="tiki-directories_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Directories{/tr}</small><br />
    {/if}
    {if $prefs.feature_calendar eq 'y' and $prefs.rss_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
        <a href="tiki-calendars_rss.php?ver={$prefs.rssfeed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a>
        <small>{tr}Calendars{/tr}</small><br />
    {/if}
  </div>
{/tikimodule}

