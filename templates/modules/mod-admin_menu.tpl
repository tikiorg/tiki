{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-admin_menu.tpl,v 1.32 2007-10-14 17:51:00 mose Exp $ *}

{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Admin Menu{/tr}"}{/if}
{tikimodule title="{tr}$tpl_module_title{/tr}" name="admin_menu" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
    {if $prefs.feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
  		&nbsp;<a href="tiki-live_support_admin.php" class="linkmenu">{tr}Live support{/tr}</a><br />
	{/if}

	{if $prefs.feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  		&nbsp;<a href="tiki-admin_banning.php" class="linkmenu">{tr}Banning{/tr}</a><br />
	{/if}
    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-adminusers.php" class="linkmenu">{tr}Users{/tr}</a><br />
      &nbsp;<a href="tiki-admingroups.php" class="linkmenu">{tr}Groups{/tr}</a><br />
      &nbsp;<a href="tiki-list_cache.php" class="linkmenu">{tr}Cache{/tr}</a><br />
      &nbsp;<a href="tiki-admin_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br />
      &nbsp;<a href="tiki-admin_links.php" class="linkmenu">{tr}Links{/tr}</a><br />
      &nbsp;<a href="tiki-admin_hotwords.php" class="linkmenu">{tr}Hotwords{/tr}</a><br />
      &nbsp;<a href="tiki-admin_rssmodules.php" class="linkmenu">{tr}RSS modules{/tr}</a><br />
      &nbsp;<a href="tiki-admin_menus.php" class="linkmenu">{tr}Menus{/tr}</a><br />
      &nbsp;<a href="tiki-admin_polls.php" class="linkmenu">{tr}Polls{/tr}</a><br />
      &nbsp;<a href="tiki-admin_notifications.php" class="linkmenu">{tr}Mail notifications{/tr}</a><br />
      &nbsp;<a href="tiki-search_stats.php" class="linkmenu">{tr}Search stats{/tr}</a><br />
			&nbsp;<a href="tiki-admin_quicktags.php" class="linkmenu">{tr}QuickTags{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_chat eq 'y'}
      &nbsp;<a href="tiki-admin_chat.php" class="linkmenu">{tr}Chat{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_categories eq 'y'}
      &nbsp;<a href="tiki-admin_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br />
    {/if}   
    {if $tiki_p_admin_banners eq 'y'}
      &nbsp;<a href="tiki-list_banners.php" class="linkmenu">{tr}Banners{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_templates eq 'y'}
      &nbsp;<a href="tiki-edit_templates.php" class="linkmenu">{tr}Edit templates{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a href="tiki-admin_drawings.php" class="linkmenu">{tr}Admin drawings{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a href="tiki-list_contents.php" class="linkmenu">{tr}Dynamic content{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a href="tiki-admin_cookies.php" class="linkmenu">{tr}Cookies{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a href="tiki-admin_mailin.php" class="linkmenu">{tr}Mail-in{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a href="tiki-admin_content_templates.php" class="linkmenu">{tr}Content templates{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a href="tiki-admin_html_pages.php" class="linkmenu">{tr}HTML pages{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a href="tiki-shoutbox.php" class="linkmenu">{tr}Shoutbox{/tr}</a><br />
      &nbsp;<a href="tiki-admin_shoutbox_words.php" class="linkmenu"{tr}Shoutbox Words{/tr}</a><br />
    {/if}
    {if $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php" class="linkmenu">{tr}Referer stats{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
    &nbsp;<a href="tiki-import_phpwiki.php" class="linkmenu">{tr}Import PHPWiki Dump{/tr}</a><br />
    &nbsp;<a href="tiki-phpinfo.php" class="linkmenu">{tr}phpinfo{/tr}</a><br />
    &nbsp;<a href="tiki-admin_dsn.php" class="linkmenu">{tr}Admin dsn{/tr}</a><br />
    &nbsp;<a href="tiki-admin_external_wikis.php" class="linkmenu">{tr}External wikis{/tr}</a><br />
    &nbsp;<a href="tiki-admin_system.php" class="linkmenu">{tr}System Admin{/tr}</a><br />
    &nbsp;<a href="tiki-admin_security.php" class="linkmenu">{tr}Security Admin{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_code_hilight eq 'y'}
    &nbsp;<a href="tiki-admin_code_syntax.php" class="linkmenu">{tr}Syntax highlighting{/tr}</a><br />
    {/if}
{/if}
{/tikimodule}
