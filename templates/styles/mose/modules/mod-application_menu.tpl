{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/mose/modules/mod-application_menu.tpl,v 1.42.2.1 2008-02-05 16:14:45 jyhem Exp $ *}

{tikimodule title="{tr}Menu{/tr}" name="application_menu" flip="y"}
<div id="mainmenu" style="display: block">
&nbsp;<a href="{$prefs.tikiIndex}" class="linkmenu">{tr}home{/tr}</a><br />
{if $prefs.feature_chat eq 'y'}
{if $tiki_p_chat eq 'y'}
&nbsp;<a href="tiki-chat.php" class="linkmenu">{tr}chat{/tr}</a><br />
{/if}
{/if}

{if $prefs.feature_contact eq 'y'}
  &nbsp;<a href="tiki-contact.php" class="linkmenu">{tr}contact us{/tr}</a><br />
{/if}


{if $prefs.feature_stats eq 'y' and $tiki_p_view_stats eq 'y'}
  &nbsp;<a href="tiki-stats.php" class="linkmenu">{tr}Stats{/tr}</a><br />
{/if}

{if $prefs.feature_categories eq 'y' and $tiki_p_view_categories eq 'y'}
  &nbsp;<a href="tiki-browse_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br />
{/if}

{if $prefs.feature_games eq 'y' and $tiki_p_play_games eq 'y'}
  &nbsp;<a href="tiki-list_games.php" class="linkmenu">{tr}games{/tr}</a><br />
{/if}

{if $prefs.feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  &nbsp;<a href="tiki-calendar.php" class="linkmenu">{tr}Calendar{/tr}</a><br />
{/if}

{if $user}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('mymenu');"><img src="styles/mose/{$icn_mymenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="mymenuicn" class="fldicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('mymenu');">[-]</a>{/if} 
  <a href="tiki-my_tiki.php" class="separator">{tr}MyTiki{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('mymenu');">[+]</a>{/if} 
  </div>
  <div id="mymenu" style="{$mnu_mymenu}">
	{if $prefs.feature_userPreferences eq 'y'}
			&nbsp;<a href="tiki-my_tiki.php" class="linkmenu">{tr}MyTiki home{/tr}</a><br />
	{/if}
  {if $prefs.feature_userPreferences eq 'y'}
      &nbsp;<a href="tiki-user_preferences.php" class="linkmenu">{tr}Preferences{/tr}</a><br />  
  {/if}
  {if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
     &nbsp;<a href="messu-mailbox.php" class="linkmenu">{tr}Messages{/tr}</a><br /> 
  {/if}
  {if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      &nbsp;<a href="tiki-user_tasks.php" class="linkmenu">{tr}Tasks{/tr}</a><br />
  {/if}
  
  {if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
      &nbsp;<a href="tiki-user_bookmarks.php" class="linkmenu">{tr}Bookmarks{/tr}</a><br />
  {/if}
  {if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
      &nbsp;<a href="tiki-user_assigned_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br />
  {/if}
  {if $prefs.feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
    &nbsp;<a href="tiki-newsreader_servers.php" class="linkmenu">{tr}Newsreader{/tr}</a><br />  
  {/if}
  {if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
    &nbsp;<a href="tiki-webmail.php" class="linkmenu">{tr}Webmail{/tr}</a><br />  
  {/if}
  {if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
    &nbsp;<a href="tiki-notepad_list.php" class="linkmenu">{tr}Notepad{/tr}</a><br />  
  {/if}
  {if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
    &nbsp;<a href="tiki-userfiles.php" class="linkmenu">{tr}My files{/tr}</a><br />  
  {/if}
  {if $prefs.feature_usermenu eq 'y'}
     &nbsp;<a href="tiki-usermenu.php" class="linkmenu">{tr}User menu{/tr}</a><br />    
  {/if}
  {if $prefs.feature_minical eq 'y'}
     &nbsp;<a href="tiki-minical.php" class="linkmenu">{tr}Calendar{/tr}</a><br />    
  {/if}
  {if $prefs.feature_user_watches eq 'y'}
    &nbsp;<a href="tiki-user_watches.php" class="linkmenu">{tr}My watches{/tr}</a><br />      
  {/if}
  </div>
{/if}

{if $prefs.feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('wfmenu');"><img src="styles/mose/{$icn_workflow}fo.gif" width="16" height="12" alt="[+]" border="0" name="wfmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('wfmenu');">[-]</a>{/if} 
  <a href="tiki-g-user_processes.php" class="separator">{tr}Workflow{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('wfmenu');">[+]</a>{/if}
  </div>
  <div id="wfmenu" style="{$mnu_wfmenu}">
  {if $tiki_p_admin_workflow eq 'y'}
      &nbsp;<a href="tiki-g-admin_processes.php" class="linkmenu">{tr}Admin processes{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_processes.php" class="linkmenu">{tr}Monitor processes{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_activities.php" class="linkmenu">{tr}Monitor activities{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_instances.php" class="linkmenu">{tr}Monitor instances{/tr}</a><br />  
  {/if}
  &nbsp;<a href="tiki-g-user_processes.php" class="linkmenu">{tr}User processes{/tr}</a><br />  
  &nbsp;<a href="tiki-g-user_activities.php" class="linkmenu">{tr}User activities{/tr}</a><br />  
  &nbsp;<a href="tiki-g-user_instances.php" class="linkmenu">{tr}User instances{/tr}</a><br />  
  </div>
{/if}

{if $prefs.feature_friends eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('friendsmenu');"><img src="styles/mose/{$icn_friendsmenu}fo.gif" width="16" height="12" alt="[+]" style="border: 0" name="friendsmenuicn" /></a>&nbsp;
  {else}<a class="separator" href="javascript:toggle('friendsmenu');">[-]</a>{/if}
  <a class="separator" href="tiki-list_users.php">{tr}Community{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('friendsmenu');">[+]</a>{/if}
  </div>
  <div id="friendsmenu" style="{$mnu_friendsmenu}">
  {if $tiki_p_list_users eq 'y'}
    &nbsp;<a href="tiki-list_users.php" class="linkmenu">{tr}Member list{/tr}</a><br />
  {/if}
  {if $prefs.feature_friends eq 'y'}
    &nbsp;<a href="tiki-friends.php" class="linkmenu">{tr}Friendship Network{/tr}</a><br />
  {/if}
  </div>
{/if}


{if $prefs.feature_wiki eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('wikimenu');"><img src="styles/mose/{$icn_wikimenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="wikimenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('wikimenu');">[-]</a>{/if}
  <a class="separator" href="tiki-index.php">{tr}Wiki{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('wikimenu');">[+]</a>{/if}
  </div>
  <div id="wikimenu" style="{$mnu_wikimenu}">
  {if $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-index.php" class="linkmenu">{tr}home{/tr}</a><br />
  {/if}
  {if $prefs.feature_lastChanges eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-lastchanges.php" class="linkmenu">{tr}Last Changes{/tr}</a><br />
  {/if}
  {if $prefs.feature_dump eq 'y' and $tiki_p_view eq 'y' and $wiki_dump_exists eq 'y'}
    &nbsp;<a href="dump/{if $tikidomain}{$tikidomain}/{/if}new.tar" class="linkmenu">{tr}dump{/tr}</a><br />
  {/if}
  {if $prefs.feature_wiki_rankings eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-wiki_rankings.php" class="linkmenu">{tr}rankings{/tr}</a><br />
  {/if}
  {if $prefs.feature_listPages eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-listpages.php" class="linkmenu">{tr}List Pages{/tr}</a><br />
    &nbsp;<a href="tiki-orphan_pages.php" class="linkmenu">{tr}orphan pages{/tr}</a><br />
  {/if}
  {if $prefs.feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-editpage.php?page=SandBox" class="linkmenu">{tr}sandbox{/tr}</a><br />
  {/if}
  {if $prefs.feature_wiki_multiprint eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-print_pages.php" class="linkmenu">{tr}Print{/tr}</a><br />
  {/if}
  {if $tiki_p_send_pages eq 'y' and $prefs.feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}Send{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_pages eq 'y'}
    &nbsp;<a href="tiki-received_pages.php" class="linkmenu">{tr}received pages{/tr}</a><br />
  {/if}
  {if $tiki_p_edit_structures eq 'y'}
   &nbsp;<a href="tiki-admin_structures.php" class="linkmenu">{tr}structures{/tr}</a><br />
  {/if}
  </div>
{/if}
{if $prefs.feature_galleries eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('galmenu');"><img src="styles/mose/{$icn_galmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="galmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('galmenu');">[-]</a>{/if} 
  <a class="separator" href="tiki-galleries.php">{tr}Image Galleries{/tr}</a> 
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('galmenu');">[+]</a>{/if}
  </div>
  <div id="galmenu" style="{$mnu_galmenu}">
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries.php" class="linkmenu">{tr}Galleries{/tr}</a><br />
  {/if}
  {if $prefs.feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a href="tiki-upload_image.php" class="linkmenu">{tr}Upload image{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_galleries eq 'y'}
  &nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkmenu">{tr}System gallery{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_articles eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('cmsmenu');"><img src="styles/mose/{$icn_cmsmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="cmsmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('cmsmenu');">[-]</a>{/if}
  <a class="separator" href='tiki-view_articles.php'>{tr}Articles{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('cmsmenu');">[+]</a>{/if}
  </div>
  <div id="cmsmenu" style="{$mnu_cmsmenu}">
  {if $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-view_articles.php" class="linkmenu">{tr}Articles Home{/tr}</a><br />
  &nbsp;<a href="tiki-list_articles.php" class="linkmenu">{tr}List articles{/tr}</a><br />
  {/if}
  {if $prefs.feature_cms_rankings eq 'y' and $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-cms_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $prefs.feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y'}
    &nbsp;<a href="tiki-edit_submission.php" class="linkmenu">{tr}Submit article{/tr}</a><br />
    {/if}
    {if $tiki_p_submit_article eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
    &nbsp;<a href="tiki-list_submissions.php" class="linkmenu">{tr}View submissions{/tr}</a><br />
    {/if}
  {/if}
  {if $tiki_p_edit_article eq 'y' && $prefs.feature_submissions ne 'y'}
      &nbsp;<a href="tiki-edit_article.php" class="linkmenu">{tr}Edit article{/tr}</a><br />
  {/if}
  {if $tiki_p_send_articles eq 'y' and $prefs.feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}Send articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_articles eq 'y'}
    &nbsp;<a href="tiki-received_articles.php" class="linkmenu">{tr}Received articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_cms eq 'y'}
      &nbsp;<a href="tiki-admin_topics.php" class="linkmenu">{tr}Admin topics{/tr}</a><br />
      &nbsp;<a href="tiki-article_types.php" class="linkmenu">{tr}Admin types{/tr}</a><br />
  {/if}  
  </div>
{/if}

{if $prefs.feature_blogs eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('blogmenu');"><img src="styles/mose/{$icn_blogmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="blogmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('blogmenu');">[-]</a>{/if}
  <a class="separator" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('blogmenu');">[+]</a>{/if}
  </div>
  <div id="blogmenu" style="{$mnu_blogmenu}">
  {if $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-list_blogs.php" class="linkmenu">{tr}List blogs{/tr}</a><br />
  {/if}
  {if $prefs.feature_blog_rankings eq 'y' and $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-blog_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  &nbsp;<a href="tiki-edit_blog.php" class="linkmenu">{tr}Create/Edit Blog{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  &nbsp;<a href="tiki-blog_post.php" class="linkmenu">{tr}Post{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_admin eq 'y'}
  &nbsp;<a href="tiki-list_posts.php" class="linkmenu">{tr}Admin posts{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_forums eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('formenu');"><img src="styles/mose/{$icn_formenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="formenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('formenu');">[-]</a>{/if} 
  <a class="separator" href="tiki-forums.php">{tr}Forums{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('formenu');">[+]</a>{/if}
  </div>
  <div id="formenu" style="{$mnu_formenu}">
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forums.php" class="linkmenu">{tr}List forums{/tr}</a><br />
  {/if}
  {if $prefs.feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forum_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_forum eq 'y'}
  &nbsp;<a href="tiki-admin_forums.php" class="linkmenu">{tr}Admin forums{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_directory eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('dirmenu');"><img src="styles/mose/{$icn_dirmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="dirmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('dirmenu');">[-]</a>{/if} 
  <a class="separator" href="tiki-directory_browse.php">{tr}Directory{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('dirmenu');">[+]</a>{/if}
  </div>
  <div id="dirmenu" style="{$mnu_dirmenu}">
  {if $tiki_p_submit_link eq 'y'}
  &nbsp;<a href="tiki-directory_add_site.php" class="linkmenu">{tr}Submit a new link{/tr}</a><br />
  {/if}
  {if $tiki_p_view_directory eq 'y'}
  &nbsp;<a href="tiki-directory_browse.php" class="linkmenu">{tr}Browse Directory{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
  &nbsp;<a href="tiki-directory_admin.php" class="linkmenu">{tr}Admin directory{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_file_galleries eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('filegalmenu');"><img src="styles/mose/{$icn_filegalmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="filegalmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('filegalmenu');">[-]</a>{/if} 
  <a class="separator" href="tiki-file_galleries.php">{tr}File Galleries{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('filegalmenu');">[+]</a>{/if}
  </div>
  <div id="filegalmenu" style="{$mnu_filegalmenu}">
  {if $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries.php" class="linkmenu">{tr}List galleries{/tr}</a><br />
  {/if}
  {if $prefs.feature_file_galleries_rankings eq 'y' and $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  &nbsp;<a href="tiki-upload_file.php" class="linkmenu">{tr}Upload file{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_faqs eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('faqsmenu');"><img src="styles/mose/{$icn_faqsmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="faqsmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('faqsmenu');">[-]</a>{/if} 
  <a href="tiki-list_faqs.php" class="separator">{tr}FAQs{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('faqsmenu');">[+]</a>{/if}
  </div>
  <div id="faqsmenu" style="{$mnu_faqsmenu}">
  {if $tiki_p_view_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="linkmenu">{tr}List FAQs{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="linkmenu">{tr}Admin FAQs{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_maps eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('mapsmenu');"><img src="styles/mose/{$icn_mapsmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="mapsmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('mapsmenu');">[-]</a>{/if} 
  <a href="tiki-map.phtml" class="separator">{tr}Maps{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('mapsmenu');">[+]</a>{/if}
  </div>
  <div id="mapsmenu" style="{$mnu_mapsmenu}">
  {if $tiki_p_map_view eq 'y'}
  &nbsp;<a href="tiki-map_edit.php" class="linkmenu">{tr}Mapfiles{/tr}</a><br />
  {/if}
  {if $tiki_p_map_edit eq 'y'}
  &nbsp;<a href="tiki-map_upload.php" class="linkmenu">{tr}Layer management{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_quizzes eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('quizmenu');"><img src="styles/mose/{$icn_quizmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="quizmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('quizmenu');">[-]</a>{/if} 
  <a href="tiki-list_quizzes.php" class="separator">{tr}Quizzes{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('quizmenu');">[+]</a>{/if}
  </div>
  <div id="quizmenu" style="{$mnu_quizmenu}">
  &nbsp;<a href="tiki-list_quizzes.php" class="linkmenu">{tr}List Quizzes{/tr}</a><br />
  {if $tiki_p_view_quiz_stats eq 'y'}
  &nbsp;<a href="tiki-quiz_stats.php" class="linkmenu">{tr}Quiz stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_quizzes eq 'y'}
  &nbsp;<a href="tiki-edit_quiz.php" class="linkmenu">{tr}Admin quiz{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_trackers eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('trkmenu');"><img src="styles/mose/{$icn_trkmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="trkmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('trkmenu');">[-]</a>{/if} 
  <a href="tiki-list_trackers.php" class="separator">{tr}Trackers{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('trkmenu');">[+]</a>{/if}
  </div>
  <div id="trkmenu" style="{$mnu_trkmenu}">
  &nbsp;<a href="tiki-list_trackers.php" class="linkmenu">{tr}List Trackers{/tr}</a><br />
  {if $tiki_p_admin_trackers eq 'y'}
  &nbsp;<a href="tiki-admin_trackers.php" class="linkmenu">{tr}Admin trackers{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_surveys eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('srvmenu');"><img src="styles/mose/{$icn_srvmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="srvmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('srvmenu');">[-]</a>{/if} 
  <a href="tiki-list_surveys.php" class="separator">{tr}Surveys{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('srvmenu');">[+]</a>{/if}
  </div>
  <div id="srvmenu" style="{$mnu_srvmenu}">
  &nbsp;<a href="tiki-list_surveys.php" class="linkmenu">{tr}List Surveys{/tr}</a><br />
  {if $tiki_p_view_survey_stats eq 'y'}
  &nbsp;<a href="tiki-survey_stats.php" class="linkmenu">{tr}Stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_surveys eq 'y'}
  &nbsp;<a href="tiki-admin_surveys.php" class="linkmenu">{tr}Admin surveys{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $prefs.feature_newsletters eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('nlmenu');"><img src="styles/mose/{$icn_nlmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="nlmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('nlmenu');">[-]</a>{/if} 
  <a href="tiki-newsletters.php" class="separator">{tr}Newsletters{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('nlmenu');">[+]</a>{/if}
  </div>
  <div id="nlmenu" style="{$mnu_nlmenu}">
  {if $tiki_p_admin_newsletters eq 'y'}
  &nbsp;<a href="tiki-send_newsletters.php" class="linkmenu">{tr}Send newsletters{/tr}</a><br />
  &nbsp;<a href="tiki-admin_newsletters.php" class="linkmenu">{tr}Admin newsletters{/tr}</a><br />
  {/if}
  </div>
{/if}


{if $feature_eph eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('ephmenu');"><img src="styles/mose/{$icn_ephmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="ephmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('ephmenu');">[-]</a>{/if} 
  <a href="tiki-eph.php" class="separator">{tr}Ephemerides{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('ephmenu');">[+]</a>{/if}
  </div>
  <div id="ephmenu" style="{$mnu_ephmenu}">
  {if $tiki_p_eph_admin eq 'y'}
  &nbsp;<a href="tiki-eph_admin.php" class="linkmenu">{tr}Admin{/tr}</a><br />
  {/if}
  </div>
{/if}


{if $prefs.feature_charts eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('chartmenu');"><img src="styles/mose/{$icn_chartmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="chartmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('chartmenu');">[-]</a>{/if} 
  <a href="tiki-charts.php" class="separator">{tr}Charts{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('chartmenu');">[+]</a>{/if}
  </div>
  <div id="chartmenu" style="{$mnu_chartmenu}">
  {if $tiki_p_admin_charts eq 'y'}
  &nbsp;<a href="tiki-admin_charts.php" class="linkmenu">{tr}Admin{/tr}</a><br />
  {/if}
  </div>
{/if}


{if $tiki_p_admin eq 'y' or 
 $tiki_p_admin_chat eq 'y' or
 $tiki_p_admin_categories eq 'y' or
 $tiki_p_admin_banners eq 'y' or
 $tiki_p_edit_templates eq 'y' or
 $tiki_p_admin_dynamic eq 'y' or
 $tiki_p_admin_dynamic eq 'y' or
 $tiki_p_admin_mailin eq 'y' or
 $tiki_p_edit_content_templates eq 'y' or
 $tiki_p_edit_html_pages eq 'y' or
 $tiki_p_view_referer_stats eq 'y' or
 $tiki_p_admin_drawings eq 'y' or
 $tiki_p_admin_shoutbox eq 'y' or
 $tiki_p_admin_live_support eq 'y' or
 $user_is_operator eq 'y'
 }
 
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('admmnu');"><img src="styles/mose/{$icn_admmnu}fo.gif" width="16" height="12" alt="[+]" border="0" name="admmnuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('admmnu');">[-]</a>{/if}
  {if $tiki_p_admin eq 'y'}<a class="separator" href='tiki-admin.php'>{/if} {tr}Admin{/tr}{if $tiki_p_admin eq 'y'}</a>{/if}
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('admmnu');">[+]</a>{/if}
  </div>
  <div id="admmnu" style="{$mnu_admmnu}">
	{if $tiki_p_admin eq 'y'}
		&nbsp;<a href="tiki-admin.php" class="linkmenu">{tr}Admin home{/tr}</a><br />
	{/if}
	{sortlinks}
	{if $prefs.feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
  		&nbsp;<a href="tiki-live_support_admin.php" class="linkmenu">{tr}Live support{/tr}</a><br />
	{/if}

	{if $prefs.feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  		&nbsp;<a href="tiki-admin_banning.php" class="linkmenu">{tr}Banning{/tr}</a><br />
	{/if}
  {if $prefs.feature_calendar eq 'y' and ($tiki_p_admin_calendar eq 'y')}
		  &nbsp;<a href="tiki-admin_calendars.php" class="linkmenu">{tr}Calendar{/tr}</a><br />
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
      &nbsp;<a href="tiki-backup.php" class="linkmenu">{tr}Backups{/tr}</a><br />
      &nbsp;<a href="tiki-admin_notifications.php" class="linkmenu">{tr}Mail notifications{/tr}</a><br />
      &nbsp;<a href="tiki-search_stats.php" class="linkmenu">{tr}Search stats{/tr}</a><br />
      &nbsp;<a href="tiki-theme_control.php" class="linkmenu">{tr}Theme control{/tr}</a><br />
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
    {/if}
    {if $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php" class="linkmenu">{tr}Referer stats{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_languages eq 'y' && $prefs.lang_use_db eq 'y'}
      &nbsp;<a href="tiki-edit_languages.php" class="linkmenu">{tr}Edit languages{/tr}</a><br />
    {/if}
    {if $tiki_p_admin_integrator eq 'y' && $prefs.feature_integrator eq 'y'}
      &nbsp;<a href="tiki-admin_integrator.php" class="linkmenu">{tr}Integrator{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
    &nbsp;<a href="tiki-phpinfo.php" class="linkmenu">{tr}phpinfo{/tr}</a><br />
    &nbsp;<a href="tiki-admin_dsn.php" class="linkmenu">{tr}Admin dsn{/tr}</a><br />
    &nbsp;<a href="tiki-admin_external_wikis.php" class="linkmenu">{tr}External wikis{/tr}</a><br />
		&nbsp;<a href="tiki-admin_system.php" class="linkmenu">{tr}System Admin{/tr}</a><br />
		&nbsp;<a href="tiki-mods.php" class="linkmenu">{tr}Mods Admin{/tr}</a><br />
    &nbsp;<a href="tiki-admin_security.php" class="linkmenu">{tr}Security Admin{/tr}</a><br />
    {/if}
		{/sortlinks}
  </div>
{/if}
{if $prefs.feature_usermenu eq 'y'}
  <div class="separator">
  {if $prefs.feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('usrmenu');"><img src="styles/mose/{$icn_usrmenu}fo.gif" width="16" height="12" alt="[+]" border="0" name="usrmenuicn" />&nbsp;</a>
  {else}<a class="separator" href="javascript:toggle('usrmenu');">[-]</a>{/if} 
  <a href="tiki-usermenu.php" class="separator">{tr}User Menu{/tr}</a>
  {if $prefs.feature_menusfolderstyle ne 'y'}<a class="separator" href="javascript:toggle('usrmenu');">[+]</a>{/if}
  </div>
  <div id="usrmenu" style="{$mnu_usrmenu}">
  {section name=ix loop=$usr_user_menus}
  &nbsp;<a {if $usr_user_menus[ix].mode eq 'n'}target="_blank"{/if} href="{$usr_user_menus[ix].url}" class="linkmenu">{$usr_user_menus[ix].name}</a><br />
  {/section}
  </div>
{/if}
</div>
{/tikimodule}
