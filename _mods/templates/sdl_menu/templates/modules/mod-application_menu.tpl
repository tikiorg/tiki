{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_menu/templates/modules/mod-application_menu.tpl,v 1.1 2004-05-09 23:10:34 damosoft Exp $ *}
{* sdl's revised application menu *}
{* TikiWiki 1.8 only *}

{tikimodule title="<a class=\"flip\" href=\"javascript:flip('mainmenu');\">{tr}Menu{/tr}</a>" name="application_menu"}

&nbsp;<a href="{$tikiIndex}" class="linkmenu">{tr}Home{/tr}</a><br />
{if $feature_chat eq 'y' and $tiki_p_chat eq 'y'}
&nbsp;<a href="tiki-chat.php" class="linkmenu">{tr}Chat{/tr}</a><br />
{/if}

{if $feature_contact eq 'y'}
  &nbsp;<a href="tiki-contact.php" class="linkmenu">{tr}Contact Us{/tr}</a><br />
{/if}


{if $feature_stats eq 'y' and $tiki_p_view_stats eq 'y'}
  &nbsp;<a href="tiki-stats.php" class="linkmenu">{tr}Statistics{/tr}</a><br />
{/if}

{if $feature_categories eq 'y' and $tiki_p_view_categories eq 'y'}
  &nbsp;<a href="tiki-browse_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br />
{/if}

{if $feature_games eq 'y' and $tiki_p_play_games eq 'y'}
  &nbsp;<a href="tiki-list_games.php" class="linkmenu">{tr}Games{/tr}</a><br />
{/if}

{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  &nbsp;<a href="tiki-calendar.php" class="linkmenu">{tr}Calendar{/tr}</a><br />
{/if}

{if $user}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('mymenu');"><img src="img/icons/fo.gif" style="border: 0" name="mymenuicn" class="fldicn" alt="{tr}MyMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('mymenu');"><img src="img/icons/plus.gif" style="border: 0" name="mymenuicn" class="fldicn" alt="{tr}MyMenu{/tr}"/></a>&nbsp;{/if}
  {if $feature_userPreferences eq 'y'}
  <a href="tiki-my_tiki.php" class="separator">{tr}My Area{/tr}</a>
  {else}
  <span class="separator">{tr}My Area{/tr}</span>
  {/if}
   </div>
  <div id="mymenu" style="{$mnu_mymenu}">
  {if $feature_userPreferences eq 'y'}
      &nbsp;<a href="tiki-user_preferences.php" class="linkmenu">{tr}Preferences{/tr}</a><br />  
  {/if}
  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
     &nbsp;<a href="messu-mailbox.php" class="linkmenu">{tr}Messages{/tr}</a><br /> 
  {/if}
  {if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      &nbsp;<a href="tiki-user_tasks.php" class="linkmenu">{tr}Tasks{/tr}</a><br />
  {/if}
  
  {if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
      &nbsp;<a href="tiki-user_bookmarks.php" class="linkmenu">{tr}Bookmarks{/tr}</a><br />
  {/if}
  {if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
      &nbsp;<a href="tiki-user_assigned_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br />
  {/if}
  {if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
    &nbsp;<a href="tiki-newsreader_servers.php" class="linkmenu">{tr}Newsreader{/tr}</a><br />  
  {/if}
  {if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
    &nbsp;<a href="tiki-webmail.php" class="linkmenu">{tr}Webmail{/tr}</a><br />  
  {/if}
  {if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
    &nbsp;<a href="tiki-notepad_list.php" class="linkmenu">{tr}Notepad{/tr}</a><br />  
  {/if}
  {if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
    &nbsp;<a href="tiki-userfiles.php" class="linkmenu">{tr}My Files{/tr}</a><br />  
  {/if}
  {if $feature_usermenu eq 'y'}
     &nbsp;<a href="tiki-usermenu.php" class="linkmenu">{tr}User Menu{/tr}</a><br />    
  {/if}
  {if $feature_minical eq 'y'}
     &nbsp;<a href="tiki-minical.php" class="linkmenu">{tr}Mini Calendar{/tr}</a><br />    
  {/if}
  {if $feature_user_watches eq 'y'}
    &nbsp;<a href="tiki-user_watches.php" class="linkmenu">{tr}My Watches{/tr}</a><br />      
  {/if}
  </div>
{/if}

{if $feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('wfmenu');"><img src="img/icons/fo.gif" style="border: 0" name="wfmenuicn" alt="{tr}WfMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('wfmenu');"><img src="img/icons/plus.gif" style="border: 0" name="wfmenuicn" class="fldicn" alt="{tr}WfMenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-g-user_processes.php" class="separator">{tr}Workflow{/tr}</a>

  </div>
  <div id="wfmenu" style="{$mnu_workflow}">
  {if $tiki_p_admin_workflow eq 'y'}
      &nbsp;<a href="tiki-g-admin_processes.php" class="linkmenu">{tr}Admin Processes{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_processes.php" class="linkmenu">{tr}Monitor Processes{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_activities.php" class="linkmenu">{tr}Monitor Activities{/tr}</a><br />  
      &nbsp;<a href="tiki-g-monitor_instances.php" class="linkmenu">{tr}Monitor Instances{/tr}</a><br />  
  {/if}
  &nbsp;<a href="tiki-g-user_processes.php" class="linkmenu">{tr}User Processes{/tr}</a><br />  
  &nbsp;<a href="tiki-g-user_activities.php" class="linkmenu">{tr}User Activities{/tr}</a><br />  
  &nbsp;<a href="tiki-g-user_instances.php" class="linkmenu">{tr}User Instances{/tr}</a><br />  
  </div>
{/if}
{if $feature_wiki eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('wikimenu');"><img src="img/icons/fo.gif" style="border: 0" name="wikimenuicn" alt="{tr}WikiMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('wikimenu');"><img src="img/icons/plus.gif" style="border: 0" name="wikimenuicn" class="fldicn" alt="{tr}wikimenu{/tr}"/></a>&nbsp;{/if}
  <a class="separator" href="tiki-index.php">{tr}Wiki{/tr}</a>
  </div>
  <div id="wikimenu" style="{$mnu_wikimenu}">
  {if $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-index.php" class="linkmenu">{tr}Wiki Home{/tr}</a><br />
  {/if}
  {if $feature_lastChanges eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-lastchanges.php" class="linkmenu">{tr}Last Changes{/tr}</a><br />
  {/if}
  {if $feature_dump eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="dump/{$tikidomain}new.tar" class="linkmenu">{tr}Dump{/tr}</a><br />
  {/if}
  {if $feature_wiki_rankings eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-wiki_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_listPages eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-listpages.php" class="linkmenu">{tr}List Pages{/tr}</a><br />
    &nbsp;<a href="tiki-orphan_pages.php" class="linkmenu">{tr}Orphan Pages{/tr}</a><br />
  {/if}
  {if $feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-editpage.php?page=SandBox" class="linkmenu">{tr}Sandbox{/tr}</a><br />
  {/if}
  {if $feature_wiki_multiprint eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-print_pages.php" class="linkmenu">{tr}Print{/tr}</a><br />
  {/if}
  {if $tiki_p_send_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}Send Pages{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-received_pages.php" class="linkmenu">{tr}Received Pages{/tr}</a><br />
  {/if}
  {if $tiki_p_edit_structures eq 'y'}
   &nbsp;<a href="tiki-admin_structures.php" class="linkmenu">{tr}Structures{/tr}</a><br />
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('galmenu');"><img src="img/icons/fo.gif" style="border: 0" name="galmenuicn" alt="{tr}GalMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('galmenu');"><img src="img/icons/plus.gif" style="border: 0" name="galmenuicn" class="fldicn" alt="{tr}galmenu{/tr}"/></a>&nbsp;{/if} 
  <a class="separator" href="tiki-galleries.php">{tr}Image Galleries{/tr}</a> 
  </div>
  <div id="galmenu" style="{$mnu_galmenu}">
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries.php" class="linkmenu">{tr}Galleries{/tr}</a><br />
  {/if}
  {if $feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a href="tiki-upload_image.php" class="linkmenu">{tr}Upload Image{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_galleries eq 'y'}
  &nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkmenu">{tr}System Gallery{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('cmsmenu');"><img src="img/icons/fo.gif" style="border: 0" name="cmsmenuicn" alt=""/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('cmsmenu');"><img src="img/icons/plus.gif" style="border: 0" name="cmsmenuicn" class="fldicn" alt="{tr}cmsMenu{/tr}"/></a>&nbsp;{/if}
  <a class="separator" href='tiki-view_articles.php'>{tr}Articles{/tr}</a>
  </div>
  <div id="cmsmenu" style="{$mnu_cmsmenu}">
  {if $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-view_articles.php" class="linkmenu">{tr}Articles Home{/tr}</a><br />
  &nbsp;<a href="tiki-list_articles.php" class="linkmenu">{tr}List Articles{/tr}</a><br />
  {/if}
  {if $feature_cms_rankings eq 'y' and $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-cms_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y' and $tiki_p_edit_article eq 'n'}
    &nbsp;<a href="tiki-edit_submission.php" class="linkmenu">{tr}Submit Article{/tr}</a><br />
    {/if}
    {if $tiki_p_submit_article eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
    &nbsp;<a href="tiki-list_submissions.php" class="linkmenu">{tr}View submissions{/tr}</a><br />
    {/if}
  {/if}
  {if $tiki_p_edit_article eq 'y'}
      &nbsp;<a href="tiki-edit_article.php" class="linkmenu">{tr}Create Article{/tr}</a><br />
  {/if}
  {if $tiki_p_send_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}Send Articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-received_articles.php" class="linkmenu">{tr}Received Articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_cms eq 'y'}
      &nbsp;<a href="tiki-admin_topics.php" class="linkmenu">{tr}Admin Topics{/tr}</a><br />
      &nbsp;<a href="tiki-article_types.php" class="linkmenu">{tr}Admin Types{/tr}</a><br />
  {/if}  
  </div>
{/if}

{if $feature_blogs eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('blogmenu');"><img src="img/icons/fo.gif" style="border: 0" name="blogmenuicn" alt=""/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('blogmenu');"><img src="img/icons/plus.gif" style="border: 0" name="blogmenuicn" class="fldicn" alt="{tr}blogMenu{/tr}"/></a>&nbsp;{/if}
  <a class="separator" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a>
  </div>
  <div id="blogmenu" style="{$mnu_blogmenu}">
  {if $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-list_blogs.php" class="linkmenu">{tr}List Blogs{/tr}</a><br />
  {/if}
  {if $feature_blog_rankings eq 'y' and $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-blog_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  &nbsp;<a href="tiki-edit_blog.php" class="linkmenu">{tr}Create/Edit Blog{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  &nbsp;<a href="tiki-blog_post.php" class="linkmenu">{tr}Post{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_admin eq 'y'}
  &nbsp;<a href="tiki-list_posts.php" class="linkmenu">{tr}Admin Posts{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_forums eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('formenu');"><img src="img/icons/fo.gif" style="border: 0" name="formenuicn" alt="{tr}ForMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('formenu');"><img src="img/icons/plus.gif" style="border: 0" name="formenuicn" class="fldicn" alt="{tr}formenu{/tr}"/></a>&nbsp;{/if} 
  <a class="separator" href="tiki-forums.php">{tr}Forums{/tr}</a>
  </div>
  <div id="formenu" style="{$mnu_formenu}">
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forums.php" class="linkmenu">{tr}List Forums{/tr}</a><br />
  {/if}
  {if $feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forum_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_forum eq 'y'}
  &nbsp;<a href="tiki-admin_forums.php" class="linkmenu">{tr}Admin Forums{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_directory eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('dirmenu');"><img src="img/icons/fo.gif" style="border: 0" name="dirmenuicn" alt="{tr}DirMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('dirmenu');"><img src="img/icons/plus.gif" style="border: 0" name="dirmenuicn" class="fldicn" alt="{tr}dirmenu{/tr}"/></a>&nbsp;{/if} 
  <a class="separator" href="tiki-directory_browse.php">{tr}Links Directory{/tr}</a>
  </div>
  <div id="dirmenu" style="{$mnu_dirmenu}">
	{if $tiki_p_submit_link eq 'y'}
	&nbsp;<a href="tiki-directory_add_site.php" class="linkmenu">{tr}Submit New Link{/tr}</a><br />
	{/if}
  {if $tiki_p_view_directory eq 'y'}
  &nbsp;<a href="tiki-directory_browse.php" class="linkmenu">{tr}Browse Directory{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
  &nbsp;<a href="tiki-directory_admin.php" class="linkmenu">{tr}Admin Directory{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_file_galleries eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('filegalmenu');"><img src="img/icons/fo.gif" style="border: 0" name="filegalmenuicn" alt="{tr}FileGalMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('filegalmenu');"><img src="img/icons/plus.gif" style="border: 0" name="filegalmenuicn" class="fldicn" alt="{tr}filegalmenu{/tr}"/></a>&nbsp;{/if} 
  <a class="separator" href="tiki-file_galleries.php">{tr}File Galleries{/tr}</a>
  </div>
  <div id="filegalmenu" style="{$mnu_filegalmenu}">
  {if $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries.php" class="linkmenu">{tr}List Galleries{/tr}</a><br />
  {/if}
  {if $feature_file_galleries_rankings eq 'y' and $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  &nbsp;<a href="tiki-upload_file.php" class="linkmenu">{tr}Upload File{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_faqs eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('faqsmenu');"><img src="img/icons/fo.gif" style="border: 0" name="faqsmenuicn" alt=""/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('faqsmenu');"><img src="img/icons/plus.gif" style="border: 0" name="faqsmenuicn" class="fldicn" alt="{tr}faqsmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-list_faqs.php" class="separator">{tr}FAQs{/tr}</a>
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

{if $feature_maps eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('mapsmenu');"><img src="img/icons/fo.gif" style="border: 0" name="mapsmenuicn" alt=""/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('mapsmenu');"><img src="img/icons/plus.gif" style="border: 0" name="mapsmenuicn" class="fldicn" alt="{tr}mapsmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-map.phtml" class="separator">{tr}Maps{/tr}</a>
  </div>
  <div id="mapsmenu" style="{$mnu_mapsmenu}">
  {if $tiki_p_map_view eq 'y'}
  &nbsp;<a href="tiki-map_edit.php" class="linkmenu">{tr}Map Files{/tr}</a><br />
  {/if}
  {if $tiki_p_map_edit eq 'y'}
  &nbsp;<a href="tiki-map_upload.php" class="linkmenu">{tr}Layer Management{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_quizzes eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('quizmenu');"><img src="img/icons/fo.gif" style="border: 0" name="quizmenuicn" alt="{tr}QuizMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('quizmenu');"><img src="img/icons/plus.gif" style="border: 0" name="quizmenuicn" class="fldicn" alt="{tr}quizmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-list_quizzes.php" class="separator">{tr}Quizzes{/tr}</a>
  </div>
  <div id="quizmenu" style="{$mnu_quizmenu}">
  &nbsp;<a href="tiki-list_quizzes.php" class="linkmenu">{tr}List Quizzes{/tr}</a><br />
  {if $tiki_p_view_quiz_stats eq 'y'}
  &nbsp;<a href="tiki-quiz_stats.php" class="linkmenu">{tr}Quiz Statistics{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_quizzes eq 'y'}
  &nbsp;<a href="tiki-edit_quiz.php" class="linkmenu">{tr}Admin Quiz{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_trackers eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('trkmenu');"><img src="img/icons/fo.gif" style="border: 0" name="trkmenuicn" alt="{tr}TrkMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('trkmenu');"><img src="img/icons/plus.gif" style="border: 0" name="trkmenuicn" class="fldicn" alt="{tr}trkmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-list_trackers.php" class="separator">{tr}Trackers{/tr}</a>
  </div>
  <div id="trkmenu" style="{$mnu_trkmenu}">
  &nbsp;<a href="tiki-list_trackers.php" class="linkmenu">{tr}List Trackers{/tr}</a><br />
  {if $tiki_p_admin_trackers eq 'y'}
  &nbsp;<a href="tiki-admin_trackers.php" class="linkmenu">{tr}Admin Trackers{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_surveys eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('srvmenu');"><img src="img/icons/fo.gif" style="border: 0" name="srvmenuicn" alt="{tr}SrvMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('srvmenu');"><img src="img/icons/plus.gif" style="border: 0" name="srvmenuicn" class="fldicn" alt="{tr}srvmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-list_surveys.php" class="separator">{tr}Surveys{/tr}</a>
  </div>
  <div id="srvmenu" style="{$mnu_srvmenu}">
  &nbsp;<a href="tiki-list_surveys.php" class="linkmenu">{tr}List Surveys{/tr}</a><br />
  {if $tiki_p_view_survey_stats eq 'y'}
  &nbsp;<a href="tiki-survey_stats.php" class="linkmenu">{tr}Statistics{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_surveys eq 'y'}
  &nbsp;<a href="tiki-admin_surveys.php" class="linkmenu">{tr}Admin Surveys{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_newsletters eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('nlmenu');"><img src="img/icons/fo.gif" style="border: 0" name="nlmenuicn" alt=""/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('nlmenu');"><img src="img/icons/plus.gif" style="border: 0" name="nlmenuicn" class="fldicn" alt="{tr}nlmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-newsletters.php" class="separator">{tr}Newsletters{/tr}</a>
  </div>
  <div id="nlmenu" style="{$mnu_nlmenu}">
  {if $tiki_p_admin_newsletters eq 'y'}
  &nbsp;<a href="tiki-send_newsletters.php" class="linkmenu">{tr}Send Newsletters{/tr}</a><br />
  &nbsp;<a href="tiki-admin_newsletters.php" class="linkmenu">{tr}Admin Newsletters{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_eph eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('ephmenu');"><img src="img/icons/fo.gif" style="border: 0" name="ephmenuicn" alt="{tr}EphMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('ephmenu');"><img src="img/icons/plus.gif" style="border: 0" name="ephmenuicn" class="fldicn" alt="{tr}ephmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-eph.php" class="separator">{tr}Ephemerides{/tr}</a>
  </div>
  <div id="ephmenu" style="{$mnu_ephmenu}">
  {if $tiki_p_eph_admin eq 'y'}
  &nbsp;<a href="tiki-eph_admin.php" class="linkmenu">{tr}Admin{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_charts eq 'y'}
  <div class="separator">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('chartmenu');"><img src="img/icons/fo.gif" style="border: 0" name="chartmenuicn" alt="{tr}ChartMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('chartmenu');"><img src="img/icons/plus.gif" style="border: 0" name="chartmenuicn" class="fldicn" alt="{tr}chartmenu{/tr}"/></a>&nbsp;{/if} 
  <a href="tiki-charts.php" class="separator">{tr}Charts{/tr}</a>
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
  {if $feature_menusfolderstyle eq 'y'}
  <a class="separator" href="javascript:icntoggle('admmnu');"><img src="img/icons/fo.gif" style="border: 0" name="admmnuicn" alt="{tr}AdmMenu{/tr}"/></a>&nbsp;
  {else} <a class="separator" href="javascript:pltoggle('admmnu');"><img src="img/icons/plus.gif" style="border: 0" name="admmnuicn" class="fldicn" alt="{tr}admmnu{/tr}"/></a>&nbsp;{/if}
  {if $tiki_p_admin eq 'y'}
  <a class="separator" href='tiki-admin.php'>{tr}Admin (click!){/tr}</a>
  {else}
  {tr}Admin{/tr}
  {/if}
  </div>
  <div id="admmnu" style="{$mnu_admmnu}">
  {sortlinks}
	{if $feature_debug_console eq 'y'}
		&nbsp;<a href="javascript:toggle('debugconsole');" class="linkmenu">{tr}Debugger Console{/tr}</a><br />
	{/if}

	{if $feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
  		&nbsp;<a href="tiki-live_support_admin.php" class="linkmenu">{tr}Live Support{/tr}</a><br />
	{/if}

	{if $feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  		&nbsp;<a href="tiki-admin_banning.php" class="linkmenu">{tr}Banning{/tr}</a><br />
	{/if}

	{if $feature_calendar eq 'y' and ($tiki_p_admin_calendar eq 'y')}
  		&nbsp;<a href="tiki-admin_calendars.php" class="linkmenu">{tr}Calendar{/tr}</a><br />
	{/if}

    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-adminusers.php" class="linkmenu">{tr}Users{/tr}</a><br />
      &nbsp;<a href="tiki-admingroups.php" class="linkmenu">{tr}Groups{/tr}</a><br />
      &nbsp;<a href="tiki-list_cache.php" class="linkmenu">{tr}Cache{/tr}</a><br />
      &nbsp;<a href="tiki-admin_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br />
      {if $feature_featuredLinks eq 'y'}
      &nbsp;<a href="tiki-admin_links.php" class="linkmenu">{tr}Links{/tr}</a><br />
      {/if}
      {if $feature_hotwords eq 'y'}
      &nbsp;<a href="tiki-admin_hotwords.php" class="linkmenu">{tr}Hotwords{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_rssmodules.php" class="linkmenu">{tr}RSS Modules{/tr}</a><br />
      &nbsp;<a href="tiki-admin_menus.php" class="linkmenu">{tr}Menus{/tr}</a><br />
      {if $feature_polls eq 'y'}
      &nbsp;<a href="tiki-admin_polls.php" class="linkmenu">{tr}Polls{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-backup.php" class="linkmenu">{tr}Backups{/tr}</a><br />
      &nbsp;<a href="tiki-admin_notifications.php" class="linkmenu">{tr}Mail Notifications{/tr}</a><br />
      {if $feature_search_stats eq 'y'}
      &nbsp;<a href="tiki-search_stats.php" class="linkmenu">{tr}Search Statistics{/tr}</a><br />
	  {/if}
      {if $feature_theme_control eq 'y'}
      &nbsp;<a href="tiki-theme_control.php" class="linkmenu">{tr}Theme Control{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_quicktags.php" class="linkmenu">{tr}QuickTags{/tr}</a><br />
    {/if}
    {if $feature_chat eq 'y' and $tiki_p_admin_chat eq 'y'}
      &nbsp;<a href="tiki-admin_chat.php" class="linkmenu">{tr}Chat{/tr}</a><br />
    {/if}
    {if $feature_categories eq 'y' and $tiki_p_admin_categories eq 'y'}
      &nbsp;<a href="tiki-admin_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br />
    {/if}   
    {if $feature_banners eq 'y' and $tiki_p_admin_banners eq 'y'}
      &nbsp;<a href="tiki-list_banners.php" class="linkmenu">{tr}Banners{/tr}</a><br />
    {/if}
    {if $feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
      &nbsp;<a href="tiki-edit_templates.php" class="linkmenu">{tr}Edit Templates{/tr}</a><br />
    {/if}
    {if $feature_drawings eq 'y' and $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a href="tiki-admin_drawings.php" class="linkmenu">{tr}Drawings{/tr}</a><br />
    {/if}
    {if $feature_dynamic_content eq 'y' and $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a href="tiki-list_contents.php" class="linkmenu">{tr}Dynamic Content{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a href="tiki-admin_cookies.php" class="linkmenu">{tr}Cookies{/tr}</a><br />
    {/if}
    {if $feature_webmail eq 'y' and $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a href="tiki-admin_mailin.php" class="linkmenu">{tr}Mail-in{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a href="tiki-admin_content_templates.php" class="linkmenu">{tr}Content Templates{/tr}</a><br />
    {/if}
    {if $feature_html_pages eq 'y' and $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a href="tiki-admin_html_pages.php" class="linkmenu">{tr}HTML Pages{/tr}</a><br />
    {/if}
    {if $feature_shoutbox eq 'y' and $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a href="tiki-shoutbox.php" class="linkmenu">{tr}Shoutbox{/tr}</a><br />
    {/if}
    {if $feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php" class="linkmenu">{tr}Referer Statistics{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_languages eq 'y' && $lang_use_db eq 'y'}
      &nbsp;<a href="tiki-edit_languages.php" class="linkmenu">{tr}Edit Languages{/tr}</a><br />
    {/if}
    {if $feature_integrator eq 'y' and $tiki_p_admin_integrator eq 'y'}
          &nbsp;<a href="tiki-admin_integrator.php" class="linkmenu">{tr}Integrator{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-import_phpwiki.php" class="linkmenu">{tr}Import PHPWiki Dump{/tr}</a><br />
      &nbsp;<a href="tiki-phpinfo.php" class="linkmenu">{tr}phpinfo{/tr}</a><br />
      &nbsp;<a href="tiki-admin_dsn.php" class="linkmenu">{tr}DSN{/tr}</a><br />
      &nbsp;<a href="tiki-admin_external_wikis.php" class="linkmenu">{tr}External Wikis{/tr}</a><br />
			&nbsp;<a href="tiki-admin_system.php" class="linkmenu">{tr}System Admin{/tr}</a><br />
    {/if}
    {/sortlinks}
  </div>
{/if}


{if $feature_usermenu eq 'y'}
  <div class="separator">

    
    {if $feature_menusfolderstyle eq 'y'}
      <a class="separator" href="javascript:icntoggle('usrmenu');">
        <img src="img/icons/fo.gif" style="border: 0" name="usrmenuicn" alt="{tr}UsrMenu{/tr}"/></a>&nbsp;
    {else}
      <a class="separator" href="javascript:pltoggle('usrmenu');"><img src="img/icons/plus.gif" style="border: 0" name="usrmenuicn" class="fldicn" alt="{tr}Usrmenu{/tr}"/></a>&nbsp;
    {/if}

    <a title="{tr}Click here to manage your personal menu{/tr}"
       href="tiki-usermenu.php" class="separator">{tr}User Menu{/tr}</a>
  </div>

  {* Show user menu contents only if user have smth to show *}

  <div id="usrmenu" style="{$mnu_usrmenu}">
  {if count($usr_user_menus) gt 0}
      {section name=ix loop=$usr_user_menus}
      &nbsp;<a {if $usr_user_menus[ix].mode eq 'n'}target="_blank"{/if} href="{$usr_user_menus[ix].url}" class="linkmenu">{$usr_user_menus[ix].name}</a><br />
      {/section}
  {/if}
  </div>
{/if}
{/tikimodule}

{if $feature_menusfolderstyle eq 'y'}
<script type='text/javascript'>
{if $user}
  {if $feature_userPreferences eq 'y'}
    setplusiconstate('mymenu');
  {/if}
{/if}
{if $feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  setfoldericonstate('wfmenu');
{/if}
{if $feature_wiki eq 'y'}
  setfoldericonstate('wikimenu');
{/if}
{if $feature_galleries eq 'y'}
  setfoldericonstate('galmenu');
{/if}
{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  setfoldericonstate('cmsmenu');
{/if}
{if $feature_blogs eq 'y'}
  setfoldericonstate('blogmenu');
{/if}
{if $feature_forums eq 'y'}
  setfoldericonstate('formenu');
{/if}
{if $feature_directory eq 'y'}
  setfoldericonstate('dirmenu');
{/if}
{if $feature_file_galleries eq 'y'}
  setfoldericonstate('filegalmenu');
{/if}
{if $feature_faqs eq 'y'}
  setfoldericonstate('faqsmenu');
{/if}
{if $feature_quizzes eq 'y'}
  setfoldericonstate('quizmenu');
{/if}
{if $feature_trackers eq 'y'}
  setfoldericonstate('trkmenu');
{/if}
{if $feature_surveys eq 'y'}
  setfoldericonstate('srvmenu');
{/if}
{if $feature_newsletters eq 'y'}
  setfoldericonstate('nlmenu');
{/if}
{if $feature_eph eq 'y'}
  setfoldericonstate('ephmenu');
{/if}
{if $feature_charts eq 'y'}
  setfoldericonstate('chartmenu');
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
  setfoldericonstate('admmnu');
{/if}
{if $feature_usermenu eq 'y'}
  setfoldericonstate('usrmenu');
{/if}
</script>
{/if}

{if $feature_menusfolderstyle neq 'y'}
<script type='text/javascript'>
{if $user}
  {if $feature_userPreferences eq 'y'}
    setplusiconstate('mymenu');
  {/if}
{/if}
{if $feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  setplusconstate('wfmenu');
{/if}
{if $feature_wiki eq 'y'}
  setplusiconstate('wikimenu');
{/if}
{if $feature_galleries eq 'y'}
  setplusiconstate('galmenu');
{/if}
{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  setplusiconstate('cmsmenu');
{/if}
{if $feature_blogs eq 'y'}
  setplusiconstate('blogmenu');
{/if}
{if $feature_forums eq 'y'}
  setplusiconstate('formenu');
{/if}
{if $feature_directory eq 'y'}
  setplusiconstate('dirmenu');
{/if}
{if $feature_file_galleries eq 'y'}
  setplusiconstate('filegalmenu');
{/if}
{if $feature_faqs eq 'y'}
  setplusiconstate('faqsmenu');
{/if}
{if $feature_quizzes eq 'y'}
  setplusiconstate('quizmenu');
{/if}
{if $feature_trackers eq 'y'}
  setplusiconstate('trkmenu');
{/if}
{if $feature_surveys eq 'y'}
  setplusiconstate('srvmenu');
{/if}
{if $feature_newsletters eq 'y'}
  setplusiconstate('nlmenu');
{/if}
{if $feature_eph eq 'y'}
  setplusiconstate('ephmenu');
{/if}
{if $feature_charts eq 'y'}
  setplusiconstate('chartmenu');
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
  setplusiconstate('admmnu');
{/if}
{if $feature_usermenu eq 'y'}
  setplusiconstate('usrmenu');
{/if}
</script>
{/if}

