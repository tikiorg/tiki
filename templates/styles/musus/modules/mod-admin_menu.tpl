{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-admin_menu.tpl,v 1.5 2004-01-26 04:04:05 musus Exp $ *}

{tikimodule title="{tr}Admin Menu{/tr}" name="admin_menu"}
{if $tiki_p_admin eq 'y' or 
 $tiki_p_admin_chat eq 'y' or
 $tiki_p_admin_categories eq 'y' or
 $tiki_p_admin_banners eq 'y' or
 $tiki_p_edit_templates eq 'y' or
 $tiki_p_admin_mailin eq 'y' or
 $tiki_p_admin_dynamic eq 'y' or
 $tiki_p_admin_dynamic eq 'y' or
 $tiki_p_edit_content_templates eq 'y' or
 $tiki_p_edit_html_pages eq 'y' or
 $tiki_p_view_referer_stats eq 'y' or
 $tiki_p_admin_drawings eq 'y' or
 $tiki_p_admin_shoutbox eq 'y'
 }
{if $feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
&nbsp;<a title="" href="tiki-live_support_admin.php" class="menu">{tr}Live support{/tr}</a><br />
{/if}

{if $feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  &nbsp;<a title="" href="tiki-admin_banning.php" class="menu">{tr}Banning{/tr}</a><br />
{/if}
{if $tiki_p_admin eq 'y'}
  &nbsp;<a title="" href="tiki-adminusers.php" class="menu">{tr}Users{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admingroups.php" class="menu">{tr}Groups{/tr}</a><br />
  &nbsp;<a title="" href="tiki-list_cache.php" class="menu">{tr}Cache{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_modules.php" class="menu">{tr}Modules{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_links.php" class="menu">{tr}Links{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_hotwords.php" class="menu">{tr}Hotwords{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_rssmodules.php" class="menu">{tr}RSS modules{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_menus.php" class="menu">{tr}Menus{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_polls.php" class="menu">{tr}Polls{/tr}</a><br />
  &nbsp;<a title="" href="tiki-backup.php" class="menu">{tr}Backups{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_notifications.php" class="menu">{tr}Mail notifications{/tr}</a><br />
  &nbsp;<a title="" href="tiki-search_stats.php" class="menu">{tr}Search stats{/tr}</a><br />
  &nbsp;<a title="" href="tiki-admin_quicktags.php" class="menu">{tr}QuickTags{/tr}</a><br />
{/if}
{if $tiki_p_admin_chat eq 'y'}
      &nbsp;<a title="" href="tiki-admin_chat.php" class="menu">{tr}Chat{/tr}</a><br />
{/if}
{if $tiki_p_admin_categories eq 'y'}
      &nbsp;<a title="" href="tiki-admin_categories.php" class="menu">{tr}Categories{/tr}</a><br />
{/if}   
{if $tiki_p_admin_banners eq 'y'}
      &nbsp;<a title="" href="tiki-list_banners.php" class="menu">{tr}Banners{/tr}</a><br />
{/if}
{if $tiki_p_edit_templates eq 'y'}
      &nbsp;<a title="" href="tiki-edit_templates.php" class="menu">{tr}Edit templates{/tr}</a><br />
{/if}
{if $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a title="" href="tiki-admin_drawings.php" class="menu">{tr}Admin drawings{/tr}</a><br />
{/if}
    {if $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a title="" href="tiki-list_contents.php" class="menu">{tr}Dynamic content{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a title="" href="tiki-admin_cookies.php" class="menu">{tr}Cookies{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a title="" href="tiki-admin_mailin.php" class="menu">{tr}Mail-in{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a title="" href="tiki-admin_content_templates.php" class="menu">{tr}Content templates{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a title="" href="tiki-admin_html_pages.php" class="menu">{tr}HTML pages{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a title="" href="tiki-shoutbox.php" class="menu">{tr}Shoutbox{/tr}</a><br />
    {/if}
    {if $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a title="" href="tiki-referer_stats.php" class="menu">{tr}Referer stats{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
    &nbsp;<a title="" href="tiki-import_phpwiki.php" class="menu">{tr}Import PHPWiki Dump{/tr}</a><br />
    &nbsp;<a title="" href="tiki-phpinfo.php" class="menu">{tr}phpinfo{/tr}</a><br />
    &nbsp;<a title="" href="tiki-admin_dsn.php" class="menu">{tr}DSN{/tr}</a><br />
    &nbsp;<a title="" href="tiki-admin_external_wikis.php" class="menu">{tr}External wikis{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_code_hilight eq 'y'}
    &nbsp;<a title="" href="tiki-admin_code_syntax.php" class="menu">{tr}Syntax highlighting{/tr}</a><br />
    {/if}
{/if}
{/tikimodule}
