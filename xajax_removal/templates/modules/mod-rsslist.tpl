{tikimodule error=$module_params.error title=$tpl_module_title name="rsslist" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  <div id="rss">
    {if $prefs.feature_wiki eq 'y' and $prefs.feed_wiki eq 'y' and $tiki_p_view eq 'y'}
        <a class="linkmodule" title="{tr}Wiki feed{/tr}" href="tiki-wiki_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Wiki{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_blogs eq 'y' and $prefs.feed_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
        <a class="linkmodule" title="{tr}Blogs feed{/tr}" href="tiki-blogs_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Blogs{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_articles eq 'y' and $prefs.feed_articles eq 'y' and $tiki_p_read_article eq 'y'}
        <a class="linkmodule" title="{tr}Articles feed{/tr}" href="tiki-articles_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Articles{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_galleries eq 'y' and $prefs.feed_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
        <a class="linkmodule" title="{tr}Image Galleries feed{/tr}" href="tiki-image_galleries_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Image Galleries{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_file_galleries eq 'y' and $prefs.feed_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
        <a class="linkmodule" title="{tr}File Galleries feed{/tr}" href="tiki-file_galleries_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}File Galleries{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_forums eq 'y' and $prefs.feed_forums eq 'y' and $tiki_p_forum_read eq 'y'}
        <a class="linkmodule" title="{tr}Forums feed{/tr}" href="tiki-forums_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Forums{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_directory eq 'y' and $prefs.feed_directories eq 'y' and $tiki_p_view_directory eq 'y'}
        <a class="linkmodule" href="tiki-directories_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Directories{/tr}
        </a>
        <br />
    {/if}
    {if $prefs.feature_calendar eq 'y' and $prefs.feed_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
        <a class="linkmodule" href="tiki-calendars_rss.php?ver={$prefs.feed_default_version}"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="{tr}Feed{/tr}" title="{tr}Feed{/tr}" width='16' height='16' />
        {tr}Calendars{/tr}
        </a>
        <br />
    {/if}
  </div>
{/tikimodule}

