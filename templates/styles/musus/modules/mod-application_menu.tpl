{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-application_menu.tpl,v 1.4 2004-01-19 18:17:24 musus Exp $ *}

{tikimodule title="<a class=\"flip\" href=\"javascript:flip('mainmenu');\">{tr}Menu{/tr}</a>" name="application_menu"}

&nbsp;<a href="{$tikiIndex}" class="menu">{tr}Home{/tr}</a><br />
{if $feature_chat eq 'y' and $tiki_p_chat eq 'y'}
&nbsp;<a href="tiki-chat.php" class="menu">{tr}Chat{/tr}</a><br />
{/if}
{if $feature_contact eq 'y'}
  &nbsp;<a href="tiki-contact.php" class="menu">{tr}Contact us{/tr}</a><br />
{/if}
{if $feature_stats eq 'y' and $tiki_p_view_stats eq 'y'}
  &nbsp;<a href="tiki-stats.php" class="menu">{tr}Stats{/tr}</a><br />
{/if}

{if $feature_categories eq 'y' and $tiki_p_view_categories eq 'y'}
  &nbsp;<a href="tiki-browse_categories.php" class="menu">{tr}Categories{/tr}</a><br />
{/if}

{if $feature_games eq 'y' and $tiki_p_play_games eq 'y'}
  &nbsp;<a href="tiki-list_games.php" class="menu">{tr}Games{/tr}</a><br />
{/if}

{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  &nbsp;<a href="tiki-calendar.php" class="menu">{tr}Calendar{/tr}</a><br />
{/if}

{if $user}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('mymenu');"><img src="img/icons/fo.gif" class="fldicn" alt="{tr}MyMenu{/tr}" /></a>&nbsp;
  {else}<a class="menu-subtitle" title="" href="javascript:toggle('mymenu');">[-]</a>{/if}
  {if $feature_userPreferences eq 'y'}
  <a class="menu-subtitle" title="" href="tiki-my_tiki.php">{tr}MyTiki (click!){/tr}</a>
  {else}
  <div class="menu-subtitle">{tr}MyTiki{/tr}</div>
  {/if}
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" title="" href="javascript:toggle('mymenu');">[+]</a>{/if} 
  </div>
  <div id="mymenu" style="{$mnu_mymenu}">
  {if $feature_userPreferences eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-user_preferences.php">{tr}Preferences{/tr}</a><br />  
  {/if}
  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
     &nbsp;<a class="menu" title="" href="messu-mailbox.php">{tr}Messages{/tr}</a><br /> 
  {/if}
  {if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-user_tasks.php">{tr}Tasks{/tr}</a><br />
  {/if}
  
  {if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</a><br />
  {/if}
  {if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-user_assigned_modules.php">{tr}Modules{/tr}</a><br />
  {/if}
  {if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-newsreader_servers.php">{tr}Newsreader{/tr}</a><br />  
  {/if}
  {if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-webmail.php">{tr}Webmail{/tr}</a><br />  
  {/if}
  {if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-notepad_list.php">{tr}Notepad{/tr}</a><br />  
  {/if}
  {if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-userfiles.php">{tr}My files{/tr}</a><br />  
  {/if}
  {if $feature_usermenu eq 'y'}
     &nbsp;<a class="menu" title="" href="tiki-usermenu.php">{tr}User menu{/tr}</a><br />    
  {/if}
  {if $feature_minical eq 'y'}
     &nbsp;<a class="menu" title="" href="tiki-minical.php">{tr}Mini calendar{/tr}</a><br />    
  {/if}
  {if $feature_user_watches eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-user_watches.php">{tr}My watches{/tr}</a><br />      
  {/if}
  </div>
{/if}

{if $feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('wfmenu');"><img src="img/icons/fo.gif" alt="{tr}WfMenu{/tr}"/></a>&nbsp;
  {else}<a title="" href="javascript:toggle('wfmenu');">[-]</a>{/if} 
  <a title="" href="tiki-g-user_processes.php">{tr}Workflow{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a title="" href="javascript:toggle('wfmenu');">[+]</a>{/if}
  </div>
  <div id="wfmenu" style="{$mnu_workflow}">
  {if $tiki_p_admin_workflow eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-g-admin_processes.php">{tr}Admin processes{/tr}</a><br />  
      &nbsp;<a class="menu" title="" href="tiki-g-monitor_processes.php">{tr}Monitor processes{/tr}</a><br />  
      &nbsp;<a class="menu" title="" href="tiki-g-monitor_activities.php">{tr}Monitor activities{/tr}</a><br />  
      &nbsp;<a class="menu" title="" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a><br />  
  {/if}
  &nbsp;<a class="menu" title="" href="tiki-g-user_processes.php">{tr}User processes{/tr}</a><br />  
  &nbsp;<a class="menu" title="" href="tiki-g-user_activities.php">{tr}User activities{/tr}</a><br />  
  &nbsp;<a class="menu" title="" href="tiki-g-user_instances.php">{tr}User instances{/tr}</a><br />  
  </div>
{/if}
{if $feature_wiki eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('wikimenu');"><img src="img/icons/fo.gif" alt="{tr}WikiMenu{/tr}" /></a>&nbsp;
  {else}<a title="" href="javascript:toggle('wikimenu');">[-]</a>{/if}
  <a title="" href="tiki-index.php">{tr}Wiki{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a title="" href="javascript:toggle('wikimenu');">[+]</a>{/if}
  </div>
  <div id="wikimenu" style="{$mnu_wikimenu}">
  {if $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-index.php">{tr}Wiki Home{/tr}</a><br />
  {/if}
  {if $feature_lastChanges eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-lastchanges.php">{tr}Last changes{/tr}</a><br />
  {/if}
  {if $feature_dump eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="dump/{$tikidomain}new.tar">{tr}Dump{/tr}</a><br />
  {/if}
  {if $feature_wiki_rankings eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-wiki_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_listPages eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-listpages.php">{tr}List pages{/tr}</a><br />
    &nbsp;<a class="menu" title="" href="tiki-orphan_pages.php">{tr}Orphan pages{/tr}</a><br />
  {/if}
  {if $feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-editpage.php?page=SandBox">{tr}Sandbox{/tr}</a><br />
  {/if}
  {if $feature_wiki_multiprint eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-print_pages.php">{tr}Print{/tr}</a><br />
  {/if}
  {if $tiki_p_send_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-send_objects.php">{tr}Send pages{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-received_pages.php">{tr}Received pages{/tr}</a><br />
  {/if}
  {if $tiki_p_edit_structures eq 'y'}
   &nbsp;<a class="menu" title="" href="tiki-admin_structures.php">{tr}Structures{/tr}</a><br />
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('galmenu');"><img src="img/icons/fo.gif" alt="{tr}GalMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('galmenu');">[-]</a>{/if} 
  <a title="" href="tiki-galleries.php">{tr}Image Galleries{/tr}</a> 
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('galmenu');">[+]</a>{/if}
  </div>
  <div id="galmenu" style="{$mnu_galmenu}">
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-galleries.php">{tr}Galleries{/tr}</a><br />
  {/if}
  {if $feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-galleries_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-upload_image.php">{tr}Upload image{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_galleries eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-list_gallery.php?galleryId=0">{tr}System gallery{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('cmsmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('cmsmenu');">[-]</a>{/if}
  <a href='tiki-view_articles.php'>{tr}Articles{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('cmsmenu');">[+]</a>{/if}
  </div>
  <div id="cmsmenu" style="{$mnu_cmsmenu}">
  {if $tiki_p_read_article eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-view_articles.php">{tr}Articles home{/tr}</a><br />
  &nbsp;<a class="menu" title="" href="tiki-list_articles.php">{tr}List articles{/tr}</a><br />
  {/if}
  {if $feature_cms_rankings eq 'y' and $tiki_p_read_article eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-cms_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-edit_submission.php">{tr}Submit article{/tr}</a><br />
    {/if}
    {if $tiki_p_submit_article eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-list_submissions.php">{tr}View submissions{/tr}</a><br />
    {/if}
  {/if}
  {if $tiki_p_edit_article eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-edit_article.php">{tr}Edit article{/tr}</a><br />
  {/if}
  {if $tiki_p_send_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-send_objects.php">{tr}Send articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a class="menu" title="" href="tiki-received_articles.php">{tr}Received articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_cms eq 'y'}
      &nbsp;<a class="menu" title="" href="tiki-admin_topics.php">{tr}Admin topics{/tr}</a><br />
      &nbsp;<a class="menu" title="" href="tiki-article_types.php">{tr}Admin types{/tr}</a><br />
  {/if}  
  </div>
{/if}

{if $feature_blogs eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('blogmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('blogmenu');">[-]</a>{/if}
  <a href="tiki-list_blogs.php">{tr}Blogs{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('blogmenu');">[+]</a>{/if}
  </div>
  <div id="blogmenu" style="{$mnu_blogmenu}">
  {if $tiki_p_read_blog eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-list_blogs.php">{tr}List blogs{/tr}</a><br />
  {/if}
  {if $feature_blog_rankings eq 'y' and $tiki_p_read_blog eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-blog_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-edit_blog.php">{tr}Create/Edit blog{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-blog_post.php">{tr}Post{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_admin eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-list_posts.php">{tr}Admin posts{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_forums eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('formenu');"><img src="img/icons/fo.gif" name="formenuicn" alt="{tr}ForMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('formenu');">[-]</a>{/if} 
  <a href="tiki-forums.php">{tr}Forums{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('formenu');">[+]</a>{/if}
  </div>
  <div id="formenu" style="{$mnu_formenu}">
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-forums.php">{tr}List forums{/tr}</a><br />
  {/if}
  {if $feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-forum_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_forum eq 'y'}
  &nbsp;<a class="menu" title="" href="tiki-admin_forums.php">{tr}Admin forums{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_directory eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('dirmenu');"><img src="img/icons/fo.gif" name="dirmenuicn" alt="{tr}DirMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('dirmenu');">[-]</a>{/if} 
  <a href="tiki-directory_browse.php">{tr}Directory{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('dirmenu');">[+]</a>{/if}
  </div>
  <div id="dirmenu" style="{$mnu_dirmenu}">
	{if $tiki_p_submit_link eq 'y'}
	&nbsp;<a href="tiki-directory_add_site.php" class="menu">{tr}Submit a new link{/tr}</a><br />
	{/if}
  {if $tiki_p_view_directory eq 'y'}
  &nbsp;<a href="tiki-directory_browse.php" class="menu">{tr}Browse directory{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
  &nbsp;<a href="tiki-directory_admin.php" class="menu">{tr}Admin directory{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_file_galleries eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('filegalmenu');"><img src="img/icons/fo.gif" name="filegalmenuicn" alt="{tr}FileGalMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('filegalmenu');">[-]</a>{/if} 
  <a href="tiki-file_galleries.php">{tr}File Galleries{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('filegalmenu');">[+]</a>{/if}
  </div>
  <div id="filegalmenu" style="{$mnu_filegalmenu}">
  {if $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries.php" class="menu">{tr}List galleries{/tr}</a><br />
  {/if}
  {if $feature_file_galleries_rankings eq 'y' and $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries_rankings.php" class="menu">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  &nbsp;<a href="tiki-upload_file.php" class="menu">{tr}Upload file{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_faqs eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('faqsmenu');"><img src="img/icons/fo.gif" name="faqsmenuicn" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('faqsmenu');">[-]</a>{/if} 
  <a href="tiki-list_faqs.php">{tr}FAQs{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('faqsmenu');">[+]</a>{/if}
  </div>
  <div id="faqsmenu" style="{$mnu_faqsmenu}">
  {if $tiki_p_view_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="menu">{tr}List FAQs{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="menu">{tr}Admin FAQs{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_maps eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('mapsmenu');"><img src="img/icons/fo.gif" name="mapsmenuicn" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('mapsmenu');">[-]</a>{/if} 
  <a href="tiki-map.phtml">{tr}Maps{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('mapsmenu');">[+]</a>{/if}
  </div>
  <div id="mapsmenu" style="{$mnu_mapsmenu}">
  {if $tiki_p_map_view eq 'y'}
  &nbsp;<a href="tiki-map_edit.php" class="menu">{tr}Mapfiles{/tr}</a><br />
  {/if}
  {if $tiki_p_map_edit eq 'y'}
  &nbsp;<a href="tiki-map_upload.php" class="menu">{tr}Layer management{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_quizzes eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('quizmenu');"><img src="img/icons/fo.gif" name="quizmenuicn" alt="{tr}QuizMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('quizmenu');">[-]</a>{/if} 
  <a href="tiki-list_quizzes.php">{tr}Quizzes{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a href="javascript:toggle('quizmenu');">[+]</a>{/if}
  </div>
  <div id="quizmenu" style="{$mnu_quizmenu}">
  &nbsp;<a href="tiki-list_quizzes.php" class="menu">{tr}List quizzes{/tr}</a><br />
  {if $tiki_p_view_quiz_stats eq 'y'}
  &nbsp;<a href="tiki-quiz_stats.php" class="menu">{tr}Quiz stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_quizzes eq 'y'}
  &nbsp;<a href="tiki-edit_quiz.php" class="menu">{tr}Admin quiz{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_trackers eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('trkmenu');"><img src="img/icons/fo.gif" name="trkmenuicn" alt="{tr}TrkMenu{/tr}"/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('trkmenu');">[-]</a>{/if} 
  <a href="tiki-list_trackers.php" class="menu-subtitle">{tr}Trackers{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('trkmenu');">[+]</a>{/if}
  </div>
  <div id="trkmenu" style="{$mnu_trkmenu}">
  &nbsp;<a href="tiki-list_trackers.php" class="menu">{tr}List trackers{/tr}</a><br />
  {if $tiki_p_admin_trackers eq 'y'}
  &nbsp;<a href="tiki-admin_trackers.php" class="menu">{tr}Admin trackers{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_surveys eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('srvmenu');"><img src="img/icons/fo.gif" name="srvmenuicn" alt="{tr}SrvMenu{/tr}"/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('srvmenu');">[-]</a>{/if} 
  <a href="tiki-list_surveys.php" class="menu-subtitle">{tr}Surveys{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('srvmenu');">[+]</a>{/if}
  </div>
  <div id="srvmenu" style="{$mnu_srvmenu}">
  &nbsp;<a href="tiki-list_surveys.php" class="menu">{tr}List surveys{/tr}</a><br />
  {if $tiki_p_view_survey_stats eq 'y'}
  &nbsp;<a href="tiki-survey_stats.php" class="menu">{tr}Stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_surveys eq 'y'}
  &nbsp;<a href="tiki-admin_surveys.php" class="menu">{tr}Admin surveys{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_newsletters eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('nlmenu');"><img src="img/icons/fo.gif" name="nlmenuicn" alt=""/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('nlmenu');">[-]</a>{/if} 
  <a href="tiki-newsletters.php" class="menu-subtitle">{tr}Newsletters{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('nlmenu');">[+]</a>{/if}
  </div>
  <div id="nlmenu" style="{$mnu_nlmenu}">
  {if $tiki_p_admin_newsletters eq 'y'}
  &nbsp;<a href="tiki-send_newsletters.php" class="menu">{tr}Send newsletters{/tr}</a><br />
  &nbsp;<a href="tiki-admin_newsletters.php" class="menu">{tr}Admin newsletters{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_eph eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('ephmenu');"><img src="img/icons/fo.gif" name="ephmenuicn" alt="{tr}EphMenu{/tr}"/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('ephmenu');">[-]</a>{/if} 
  <a href="tiki-eph.php" class="menu-subtitle">{tr}Ephemerides{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('ephmenu');">[+]</a>{/if}
  </div>
  <div id="ephmenu" style="{$mnu_ephmenu}">
  {if $tiki_p_eph_admin eq 'y'}
  &nbsp;<a href="tiki-eph_admin.php" class="menu">{tr}Admin{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_charts eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('chartmenu');"><img src="img/icons/fo.gif" name="chartmenuicn" alt="{tr}ChartMenu{/tr}"/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('chartmenu');">[-]</a>{/if} 
  <a href="tiki-charts.php" class="menu-subtitle">{tr}Charts{/tr}</a>
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('chartmenu');">[+]</a>{/if}
  </div>
  <div id="chartmenu" style="{$mnu_chartmenu}">
  {if $tiki_p_admin_charts eq 'y'}
  &nbsp;<a href="tiki-admin_charts.php" class="menu">{tr}Admin{/tr}</a><br />
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
 
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a class="menu-subtitle" href="javascript:icntoggle('admmnu');"><img src="img/icons/fo.gif" name="admmnuicn" alt="{tr}AdmMenu{/tr}"/></a>&nbsp;
  {else}<a class="menu-subtitle" href="javascript:toggle('admmnu');">[-]</a>{/if}
  {if $tiki_p_admin eq 'y'}
  <a class="menu-subtitle" href='tiki-admin.php'>{tr}Admin (click!){/tr}</a>
  {else}
  {tr}Admin{/tr}
  {/if}
  {if $feature_menusfolderstyle ne 'y'}<a class="menu-subtitle" href="javascript:toggle('admmnu');">[+]</a>{/if}
  </div>
  <div id="admmnu" style="{$mnu_admmnu}">
  {sortlinks}
	{if $feature_debug_console eq 'y'}
		&nbsp;<a href="javascript:toggle('debugconsole');" class="menu">{tr}Debugger console{/tr}</a><br />
	{/if}

	{if $feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
  		&nbsp;<a href="tiki-live_support_admin.php" class="menu">{tr}Live support{/tr}</a><br />
	{/if}

	{if $feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  		&nbsp;<a href="tiki-admin_banning.php" class="menu">{tr}Banning{/tr}</a><br />
	{/if}

	{if $feature_calendar eq 'y' and ($tiki_p_admin_calendar eq 'y')}
  		&nbsp;<a href="tiki-admin_calendars.php" class="menu">{tr}Calendar{/tr}</a><br />
	{/if}

    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-adminusers.php" class="menu">{tr}Users{/tr}</a><br />
      &nbsp;<a href="tiki-admingroups.php" class="menu">{tr}Groups{/tr}</a><br />
      &nbsp;<a href="tiki-list_cache.php" class="menu">{tr}Cache{/tr}</a><br />
      &nbsp;<a href="tiki-admin_modules.php" class="menu">{tr}Modules{/tr}</a><br />
      {if $feature_featuredLinks eq 'y'}
      &nbsp;<a href="tiki-admin_links.php" class="menu">{tr}Links{/tr}</a><br />
      {/if}
      {if $feature_hotwords eq 'y'}
      &nbsp;<a href="tiki-admin_hotwords.php" class="menu">{tr}Hotwords{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_rssmodules.php" class="menu">{tr}RSS modules{/tr}</a><br />
      &nbsp;<a href="tiki-admin_menus.php" class="menu">{tr}Menus{/tr}</a><br />
      {if $feature_polls eq 'y'}
      &nbsp;<a href="tiki-admin_polls.php" class="menu">{tr}Polls{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-backup.php" class="menu">{tr}Backups{/tr}</a><br />
      &nbsp;<a href="tiki-admin_notifications.php" class="menu">{tr}Mail notifications{/tr}</a><br />
      {if $feature_search_stats eq 'y'}
      &nbsp;<a href="tiki-search_stats.php" class="menu">{tr}Search stats{/tr}</a><br />
	  {/if}
      {if $feature_theme_control eq 'y'}
      &nbsp;<a href="tiki-theme_control.php" class="menu">{tr}Theme control{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_quicktags.php" class="menu">{tr}QuickTags{/tr}</a><br />
    {/if}
    {if $feature_chat eq 'y' and $tiki_p_admin_chat eq 'y'}
      &nbsp;<a href="tiki-admin_chat.php" class="menu">{tr}Chat{/tr}</a><br />
    {/if}
    {if $feature_categories eq 'y' and $tiki_p_admin_categories eq 'y'}
      &nbsp;<a href="tiki-admin_categories.php" class="menu">{tr}Categories{/tr}</a><br />
    {/if}   
    {if $feature_banners eq 'y' and $tiki_p_admin_banners eq 'y'}
      &nbsp;<a href="tiki-list_banners.php" class="menu">{tr}Banners{/tr}</a><br />
    {/if}
    {if $feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
      &nbsp;<a href="tiki-edit_templates.php" class="menu">{tr}Edit templates{/tr}</a><br />
    {/if}
    {if $feature_drawings eq 'y' and $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a href="tiki-admin_drawings.php" class="menu">{tr}Drawings{/tr}</a><br />
    {/if}
    {if $feature_dynamic_content eq 'y' and $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a href="tiki-list_contents.php" class="menu">{tr}Dynamic content{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a href="tiki-admin_cookies.php" class="menu">{tr}Cookies{/tr}</a><br />
    {/if}
    {if $feature_webmail eq 'y' and $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a href="tiki-admin_mailin.php" class="menu">{tr}Mail-in{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a href="tiki-admin_content_templates.php" class="menu">{tr}Content templates{/tr}</a><br />
    {/if}
    {if $feature_html_pages eq 'y' and $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a href="tiki-admin_html_pages.php" class="menu">{tr}HTML pages{/tr}</a><br />
    {/if}
    {if $feature_shoutbox eq 'y' and $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a href="tiki-shoutbox.php" class="menu">{tr}Shoutbox{/tr}</a><br />
    {/if}
    {if $feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php" class="menu">{tr}Referer stats{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_languages eq 'y' && $lang_use_db eq 'y'}
      &nbsp;<a href="tiki-edit_languages.php" class="menu">{tr}Edit languages{/tr}</a><br />
    {/if}
    {if $feature_integrator eq 'y' and $tiki_p_admin_integrator eq 'y'}
          &nbsp;<a href="tiki-admin_integrator.php" class="menu">{tr}Integrator{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-import_phpwiki.php">{tr}Import PHPWiki Dump{/tr}</a><br />
      &nbsp;<a href="tiki-phpinfo.php" class="menu">{tr}phpinfo{/tr}</a><br />
      &nbsp;<a href="tiki-admin_dsn.php" class="menu">{tr}DSN{/tr}</a><br />
      &nbsp;<a href="tiki-admin_external_wikis.php" class="menu">{tr}External wikis{/tr}</a><br />
    {/if}
    {/sortlinks}
  </div>
{/if}


{if $feature_usermenu eq 'y'}
  <div class="menu-subtitle">

    
    {if $feature_menusfolderstyle eq 'y'}
      <a class="menu-subtitle" href="javascript:icntoggle('usrmenu');">
        <img src="img/icons/fo.gif" name="usrmenuicn" alt="{tr}UsrMenu{/tr}"/></a>&nbsp;
    {else}
      <a class="menu-subtitle" href="javascript:toggle('usrmenu');">[-]</a>
    {/if}

    <a title="{tr}Click here to manage your personal menu{/tr}"
       href="tiki-usermenu.php" class="menu-subtitle">{tr}User Menu{/tr}</a>

    {if $feature_menusfolderstyle ne 'y'}
      <a class="menu-subtitle" href="javascript:toggle('usrmenu');">[+]</a>
    {/if}

  </div>

  {* Show user menu contents only if user have smth to show *}

  <div id="usrmenu" style="{$mnu_usrmenu}">
  {if count($usr_user_menus) gt 0}
      {section name=ix loop=$usr_user_menus}
      &nbsp;<a {if $usr_user_menus[ix].mode eq 'n'}target="_blank"{/if} href="{$usr_user_menus[ix].url}" class="menu">{$usr_user_menus[ix].name}</a><br />
      {/section}
  {/if}
  </div>
{/if}
{/tikimodule}

{if $feature_menusfolderstyle eq 'y'}
<script type='text/javascript'>
{if $user}
  {if $feature_userPreferences eq 'y'}
    setfoldericonstate('mymenu');
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
