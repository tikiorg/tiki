{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-application_menu.tpl,v 1.6 2004-01-26 04:04:05 musus Exp $ *}

{tikimodule title="<a class=\"flip\" href=\"javascript:flip('mainmenu');\">{tr}Menu{/tr}</a>" name="application_menu"}

<div class="{$menu}">
&nbsp;<a title="" href="{$tikiIndex}">{tr}Home{/tr}</a><br />
{if $feature_chat eq 'y' and $tiki_p_chat eq 'y'}
&nbsp;<a title="" href="tiki-chat.php">{tr}Chat{/tr}</a><br />
{/if}
{if $feature_contact eq 'y'}
  &nbsp;<a title="" href="tiki-contact.php">{tr}Contact us{/tr}</a><br />
{/if}
{if $feature_stats eq 'y' and $tiki_p_view_stats eq 'y'}
  &nbsp;<a title="" href="tiki-stats.php">{tr}Stats{/tr}</a><br />
{/if}
{if $feature_categories eq 'y' and $tiki_p_view_categories eq 'y'}
  &nbsp;<a title="" href="tiki-browse_categories.php">{tr}Categories{/tr}</a><br />
{/if}
{if $feature_games eq 'y' and $tiki_p_play_games eq 'y'}
  &nbsp;<a title="" href="tiki-list_games.php">{tr}Games{/tr}</a><br />
{/if}
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  &nbsp;<a title="" href="tiki-calendar.php">{tr}Calendar{/tr}</a><br />
{/if}
</div>
{if $user}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('mymenu');"><img src="img/icons/fo.gif" alt="" /></a>&nbsp;
  {else}<a title="" href="javascript:toggle('mymenu');">±</a>{/if}
  {if $feature_userPreferences eq 'y'}
  <a title="" href="tiki-my_tiki.php">{tr}MyTiki{/tr}</a>
  {else}{tr}MyTiki{/tr}
  {/if}
  </div>
  <div id="mymenu" style="{$menu}">
  {if $feature_userPreferences eq 'y'}
      &nbsp;<a title="" href="tiki-user_preferences.php">{tr}Preferences{/tr}</a><br />
  {/if}
  {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
     &nbsp;<a title="" href="messu-mailbox.php">{tr}Messages{/tr}</a><br /> 
  {/if}
  {if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      &nbsp;<a title="" href="tiki-user_tasks.php">{tr}Tasks{/tr}</a><br />
  {/if}
  
  {if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
      &nbsp;<a title="" href="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</a><br />
  {/if}
  {if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
      &nbsp;<a title="" href="tiki-user_assigned_modules.php">{tr}Modules{/tr}</a><br />
  {/if}
  {if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
    &nbsp;<a title="" href="tiki-newsreader_servers.php">{tr}Newsreader{/tr}</a><br />  
  {/if}
  {if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
    &nbsp;<a title="" href="tiki-webmail.php">{tr}Webmail{/tr}</a><br />  
  {/if}
  {if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
    &nbsp;<a title="" href="tiki-notepad_list.php">{tr}Notepad{/tr}</a><br />  
  {/if}
  {if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
    &nbsp;<a title="" href="tiki-userfiles.php">{tr}My files{/tr}</a><br />  
  {/if}
  {if $feature_usermenu eq 'y'}
     &nbsp;<a title="" href="tiki-usermenu.php">{tr}User menu{/tr}</a><br />    
  {/if}
  {if $feature_minical eq 'y'}
     &nbsp;<a title="" href="tiki-minical.php">{tr}Mini calendar{/tr}</a><br />    
  {/if}
  {if $feature_user_watches eq 'y'}
    &nbsp;<a title="" href="tiki-user_watches.php">{tr}My watches{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_workflow eq 'y' and $tiki_p_use_workflow eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('wfmenu');"><img src="img/icons/fo.gif" alt="{tr}WfMenu{/tr}" /></a>&nbsp;
  {else}<a title="" href="javascript:toggle('wfmenu');">±</a>{/if} 
  <a title="" href="tiki-g-user_processes.php">{tr}Workflow{/tr}</a>
  </div>
  <div id="wfmenu" style="{$menu}">
  {if $tiki_p_admin_workflow eq 'y'}
      &nbsp;<a title="" href="tiki-g-admin_processes.php">{tr}Admin processes{/tr}</a><br />  
      &nbsp;<a title="" href="tiki-g-monitor_processes.php">{tr}Monitor processes{/tr}</a><br />  
      &nbsp;<a title="" href="tiki-g-monitor_activities.php">{tr}Monitor activities{/tr}</a><br />  
      &nbsp;<a title="" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a><br />  
  {/if}
  &nbsp;<a title="" href="tiki-g-user_processes.php">{tr}User processes{/tr}</a><br />  
  &nbsp;<a title="" href="tiki-g-user_activities.php">{tr}User activities{/tr}</a><br />  
  &nbsp;<a title="" href="tiki-g-user_instances.php">{tr}User instances{/tr}</a><br />  
  </div>
{/if}
{if $feature_wiki eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('wikimenu');"><img src="img/icons/fo.gif" alt="{tr}WikiMenu{/tr}" /></a>&nbsp;
  {else}<a title="" href="javascript:toggle('wikimenu');">±</a>{/if}
  <a title="" href="tiki-index.php">{tr}Wiki{/tr}</a>
  </div>
  <div id="wikimenu" style="{$menu}">
  {if $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-index.php">{tr}Wiki Home{/tr}</a><br />
  {/if}
  {if $feature_lastChanges eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-lastchanges.php">{tr}Last changes{/tr}</a><br />
  {/if}
  {if $feature_dump eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="dump/{$tikidomain}new.tar">{tr}Dump{/tr}</a><br />
  {/if}
  {if $feature_wiki_rankings eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-wiki_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_listPages eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-listpages.php">{tr}List pages{/tr}</a><br />
    &nbsp;<a title="" href="tiki-orphan_pages.php">{tr}Orphan pages{/tr}</a><br />
  {/if}
  {if $feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-editpage.php?page=SandBox">{tr}Sandbox{/tr}</a><br />
  {/if}
  {if $feature_wiki_multiprint eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a title="" href="tiki-print_pages.php">{tr}Print{/tr}</a><br />
  {/if}
  {if $tiki_p_send_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a title="" href="tiki-send_objects.php">{tr}Send pages{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a title="" href="tiki-received_pages.php">{tr}Received pages{/tr}</a><br />
  {/if}
  {if $tiki_p_edit_structures eq 'y'}
   &nbsp;<a title="" href="tiki-admin_structures.php">{tr}Structures{/tr}</a><br />
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('galmenu');"><img src="img/icons/fo.gif" alt="{tr}GalMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('galmenu');">±</a>{/if} 
  <a title="" href="tiki-galleries.php">{tr}Image Galleries{/tr}</a>
  </div>
  <div id="galmenu" style="{$menu}">
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a title="" href="tiki-galleries.php">{tr}Galleries{/tr}</a><br />
  {/if}
  {if $feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a title="" href="tiki-galleries_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a title="" href="tiki-upload_image.php">{tr}Upload image{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_galleries eq 'y'}
  &nbsp;<a title="" href="tiki-list_gallery.php?galleryId=0">{tr}System gallery{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('cmsmenu');"><img src="img/icons/fo.gif" alt="" /></a>&nbsp;
  {else}<a href="javascript:toggle('cmsmenu');">±</a>{/if}
  <a href='tiki-view_articles.php'>{tr}Articles{/tr}</a>
  </div>
  <div id="cmsmenu" style="{$menu}">
  {if $tiki_p_read_article eq 'y'}
  &nbsp;<a title="" href="tiki-view_articles.php">{tr}Articles home{/tr}</a><br />
  &nbsp;<a title="" href="tiki-list_articles.php">{tr}List articles{/tr}</a><br />
  {/if}
  {if $feature_cms_rankings eq 'y' and $tiki_p_read_article eq 'y'}
  &nbsp;<a title="" href="tiki-cms_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y'}
    &nbsp;<a title="" href="tiki-edit_submission.php">{tr}Submit article{/tr}</a><br />
    {/if}
    {if $tiki_p_submit_article eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
    &nbsp;<a title="" href="tiki-list_submissions.php">{tr}View submissions{/tr}</a><br />
    {/if}
  {/if}
  {if $tiki_p_edit_article eq 'y'}
      &nbsp;<a title="" href="tiki-edit_article.php">{tr}Edit article{/tr}</a><br />
  {/if}
  {if $tiki_p_send_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a title="" href="tiki-send_objects.php">{tr}Send articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_received_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a title="" href="tiki-received_articles.php">{tr}Received articles{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_cms eq 'y'}
      &nbsp;<a title="" href="tiki-admin_topics.php">{tr}Admin topics{/tr}</a><br />
      &nbsp;<a title="" href="tiki-article_types.php">{tr}Admin types{/tr}</a><br />
  {/if}  
  </div>
{/if}

{if $feature_blogs eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('blogmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a title="" href="javascript:toggle('blogmenu');">±</a>{/if}
  <a title="" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a>
  </div>
  <div id="blogmenu" style="{$menu}">
  {if $tiki_p_read_blog eq 'y'}
  &nbsp;<a title="" href="tiki-list_blogs.php">{tr}List blogs{/tr}</a><br />
  {/if}
  {if $feature_blog_rankings eq 'y' and $tiki_p_read_blog eq 'y'}
  &nbsp;<a title="" href="tiki-blog_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  &nbsp;<a title="" href="tiki-edit_blog.php">{tr}Create/Edit blog{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  &nbsp;<a title="" href="tiki-blog_post.php">{tr}Post{/tr}</a><br />
  {/if}
  {if $tiki_p_blog_admin eq 'y'}
  &nbsp;<a title="" href="tiki-list_posts.php">{tr}Admin posts{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_forums eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('formenu');"><img src="img/icons/fo.gif" alt="{tr}ForMenu{/tr}"/></a>&nbsp;
  {else}<a title="" href="javascript:toggle('formenu');">±</a>{/if} 
  <a title="" href="tiki-forums.php">{tr}Forums{/tr}</a>
  </div>
  <div id="formenu" style="{$menu}">
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a title="" href="tiki-forums.php">{tr}List forums{/tr}</a><br />
  {/if}
  {if $feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a title="" href="tiki-forum_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_forum eq 'y'}
  &nbsp;<a title="" href="tiki-admin_forums.php">{tr}Admin forums{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_directory eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('dirmenu');"><img src="img/icons/fo.gif" alt="{tr}DirMenu{/tr}"/></a>&nbsp;
  {else}<a title="" href="javascript:toggle('dirmenu');">±</a>{/if} 
  <a title="" href="tiki-directory_browse.php">{tr}Directory{/tr}</a>
  </div>
  <div id="dirmenu" style="{$menu}">
	{if $tiki_p_submit_link eq 'y'}
	&nbsp;<a title="" href="tiki-directory_add_site.php">{tr}Submit a new link{/tr}</a><br />
	{/if}
  {if $tiki_p_view_directory eq 'y'}
  &nbsp;<a title="" href="tiki-directory_browse.php">{tr}Browse directory{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
  &nbsp;<a title="" href="tiki-directory_admin.php">{tr}Admin directory{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_file_galleries eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('filegalmenu');"><img src="img/icons/fo.gif" alt="{tr}FileGalMenu{/tr}" /></a>&nbsp;
  {else}<a title="" href="javascript:toggle('filegalmenu');">±</a>{/if} 
  <a title="" href="tiki-file_galleries.php">{tr}File Galleries{/tr}</a>
  </div>
  <div id="filegalmenu" style="{$menu}">
  {if $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a title="" href="tiki-file_galleries.php">{tr}List galleries{/tr}</a><br />
  {/if}
  {if $feature_file_galleries_rankings eq 'y' and $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a title="" href="tiki-file_galleries_rankings.php">{tr}Rankings{/tr}</a><br />
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  &nbsp;<a title="" href="tiki-upload_file.php">{tr}Upload file{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_faqs eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a title="" href="javascript:icntoggle('faqsmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a title="" href="javascript:toggle('faqsmenu');">±</a>{/if} 
  <a title="" href="tiki-list_faqs.php">{tr}FAQs{/tr}</a>
  </div>
  <div id="faqsmenu" style="{$menu}">
  {if $tiki_p_view_faqs eq 'y'}
  &nbsp;<a title="" href="tiki-list_faqs.php">{tr}List FAQs{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_faqs eq 'y'}
  &nbsp;<a title="" href="tiki-list_faqs.php">{tr}Admin FAQs{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_maps eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('mapsmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('mapsmenu');">±</a>{/if} 
  <a href="tiki-map.phtml">{tr}Maps{/tr}</a>
  </div>
  <div id="mapsmenu" style="{$menu}">
  {if $tiki_p_map_view eq 'y'}
  &nbsp;<a title="" href="tiki-map_edit.php">{tr}Mapfiles{/tr}</a><br />
  {/if}
  {if $tiki_p_map_edit eq 'y'}
  &nbsp;<a title="" href="tiki-map_upload.php">{tr}Layer management{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_quizzes eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('quizmenu');"><img src="img/icons/fo.gif" alt="{tr}QuizMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('quizmenu');">±</a>{/if} 
  <a href="tiki-list_quizzes.php">{tr}Quizzes{/tr}</a>
  </div>
  <div id="quizmenu" style="{$menu}">
  &nbsp;<a href="tiki-list_quizzes.php">{tr}List quizzes{/tr}</a><br />
  {if $tiki_p_view_quiz_stats eq 'y'}
  &nbsp;<a href="tiki-quiz_stats.php">{tr}Quiz stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_quizzes eq 'y'}
  &nbsp;<a href="tiki-edit_quiz.php">{tr}Admin quiz{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_trackers eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('trkmenu');"><img src="img/icons/fo.gif" alt="{tr}TrkMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('trkmenu');">±</a>{/if}
  <a href="tiki-list_trackers.php">{tr}Trackers{/tr}</a>
  </div> <!--end menu-subtitle-->
  <div id="trkmenu" style="{$menu}">
  &nbsp;<a href="tiki-list_trackers.php">{tr}List trackers{/tr}</a><br />
  {if $tiki_p_admin_trackers eq 'y'}
  &nbsp;<a href="tiki-admin_trackers.php">{tr}Admin trackers{/tr}</a><br />
  {/if}
  </div> <!--end trkmenu-->
{/if}

{if $feature_surveys eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('srvmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('srvmenu');">±</a>{/if} 
  <a href="tiki-list_surveys.php">{tr}Surveys{/tr}</a>
  </div> <!--end menu-subtitle-->
  <div id="srvmenu" style="{$menu}">
  &nbsp;<a href="tiki-list_surveys.php">{tr}List surveys{/tr}</a><br />
  {if $tiki_p_view_survey_stats eq 'y'}
  &nbsp;<a href="tiki-survey_stats.php">{tr}Stats{/tr}</a><br />
  {/if}
  {if $tiki_p_admin_surveys eq 'y'}
  &nbsp;<a href="tiki-admin_surveys.php">{tr}Admin surveys{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_newsletters eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('nlmenu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('nlmenu');">±</a>{/if} 
  <a href="tiki-newsletters.php">{tr}Newsletters{/tr}</a>
  </div>
  <div id="nlmenu" style="{$menu}">
  {if $tiki_p_admin_newsletters eq 'y'}
  &nbsp;<a href="tiki-send_newsletters.php">{tr}Send newsletters{/tr}</a><br />
  &nbsp;<a href="tiki-admin_newsletters.php">{tr}Admin newsletters{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_eph eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('ephmenu');"><img src="img/icons/fo.gif" alt="" /></a>&nbsp;
  {else}<a href="javascript:toggle('ephmenu');">±</a>{/if} 
  <a href="tiki-eph.php">{tr}Ephemerides{/tr}</a>
  </div>
  <div id="ephmenu" style="{$menu}">
  {if $tiki_p_eph_admin eq 'y'}
  &nbsp;<a href="tiki-eph_admin.php">{tr}Admin{/tr}</a><br />
  {/if}
  </div>
{/if}

{if $feature_charts eq 'y'}
  <div class="menu-subtitle">
  {if $feature_menusfolderstyle eq 'y'}
  <a href="javascript:icntoggle('chartmenu');"><img src="img/icons/fo.gif" alt="{tr}ChartMenu{/tr}"/></a>&nbsp;
  {else}<a href="javascript:toggle('chartmenu');">±</a>{/if} 
  <a href="tiki-charts.php">{tr}Charts{/tr}</a>
  </div> <!--end menu-subtitle-->
  <div id="chartmenu" style="{$menu}">
  {if $tiki_p_admin_charts eq 'y'}
  &nbsp;<a href="tiki-admin_charts.php">{tr}Admin{/tr}</a><br />
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
  <a href="javascript:icntoggle('admmnu');"><img src="img/icons/fo.gif" alt=""/></a>&nbsp;
  {else}<a href="javascript:toggle('admmnu');">±</a>{/if}
  {if $tiki_p_admin eq 'y'}
  <a href='tiki-admin.php'>{tr}Admin{/tr}</a>
  {else}
  {tr}Admin{/tr}
  {/if}
  </div>
  <div id="admmnu" style="{$menu}">
  {sortlinks}
	{if $feature_debug_console eq 'y'}
		&nbsp;<a href="javascript:toggle('debugconsole');">{tr}Debugger console{/tr}</a><br />
	{/if}
	{if $feature_live_support eq 'y' and ($tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y')}
  		&nbsp;<a href="tiki-live_support_admin.php">{tr}Live support{/tr}</a><br />
	{/if}
	{if $feature_banning eq 'y' and ($tiki_p_admin_banning eq 'y')}
  		&nbsp;<a href="tiki-admin_banning.php">{tr}Banning{/tr}</a><br />
	{/if}
	{if $feature_calendar eq 'y' and ($tiki_p_admin_calendar eq 'y')}
  		&nbsp;<a href="tiki-admin_calendars.php">{tr}Calendar{/tr}</a><br />
	{/if}
    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-adminusers.php">{tr}Users{/tr}</a><br />
      &nbsp;<a href="tiki-admingroups.php">{tr}Groups{/tr}</a><br />
      &nbsp;<a href="tiki-list_cache.php">{tr}Cache{/tr}</a><br />
      &nbsp;<a href="tiki-admin_modules.php">{tr}Modules{/tr}</a><br />
      {if $feature_featuredLinks eq 'y'}
      &nbsp;<a href="tiki-admin_links.php">{tr}Links{/tr}</a><br />
      {/if}
      {if $feature_hotwords eq 'y'}
      &nbsp;<a href="tiki-admin_hotwords.php">{tr}Hotwords{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_rssmodules.php">{tr}RSS modules{/tr}</a><br />
      &nbsp;<a href="tiki-admin_menus.php">{tr}Menus{/tr}</a><br />
      {if $feature_polls eq 'y'}
      &nbsp;<a href="tiki-admin_polls.php">{tr}Polls{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-backup.php">{tr}Backups{/tr}</a><br />
      &nbsp;<a href="tiki-admin_notifications.php">{tr}Mail notifications{/tr}</a><br />
      {if $feature_search_stats eq 'y'}
      &nbsp;<a href="tiki-search_stats.php">{tr}Search stats{/tr}</a><br />
	  {/if}
      {if $feature_theme_control eq 'y'}
      &nbsp;<a href="tiki-theme_control.php">{tr}Theme control{/tr}</a><br />
      {/if}
      &nbsp;<a href="tiki-admin_quicktags.php">{tr}QuickTags{/tr}</a><br />
    {/if}
    {if $feature_chat eq 'y' and $tiki_p_admin_chat eq 'y'}
      &nbsp;<a href="tiki-admin_chat.php">{tr}Chat{/tr}</a><br />
    {/if}
    {if $feature_categories eq 'y' and $tiki_p_admin_categories eq 'y'}
      &nbsp;<a href="tiki-admin_categories.php">{tr}Categories{/tr}</a><br />
    {/if}   
    {if $feature_banners eq 'y' and $tiki_p_admin_banners eq 'y'}
      &nbsp;<a href="tiki-list_banners.php">{tr}Banners{/tr}</a><br />
    {/if}
    {if $feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
      &nbsp;<a href="tiki-edit_templates.php">{tr}Edit templates{/tr}</a><br />
    {/if}
    {if $feature_drawings eq 'y' and $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a href="tiki-admin_drawings.php">{tr}Drawings{/tr}</a><br />
    {/if}
    {if $feature_dynamic_content eq 'y' and $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a href="tiki-list_contents.php">{tr}Dynamic content{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a href="tiki-admin_cookies.php">{tr}Cookies{/tr}</a><br />
    {/if}
    {if $feature_webmail eq 'y' and $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a href="tiki-admin_mailin.php">{tr}Mail-in{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a href="tiki-admin_content_templates.php">{tr}Content templates{/tr}</a><br />
    {/if}
    {if $feature_html_pages eq 'y' and $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a href="tiki-admin_html_pages.php">{tr}HTML pages{/tr}</a><br />
    {/if}
    {if $feature_shoutbox eq 'y' and $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a href="tiki-shoutbox.php">{tr}Shoutbox{/tr}</a><br />
    {/if}
    {if $feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php">{tr}Referer stats{/tr}</a><br />
    {/if}
    {if $tiki_p_edit_languages eq 'y' && $lang_use_db eq 'y'}
      &nbsp;<a href="tiki-edit_languages.php">{tr}Edit languages{/tr}</a><br />
    {/if}
    {if $feature_integrator eq 'y' and $tiki_p_admin_integrator eq 'y'}
          &nbsp;<a href="tiki-admin_integrator.php">{tr}Integrator{/tr}</a><br />
    {/if}
    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-import_phpwiki.php">{tr}Import PHPWiki Dump{/tr}</a><br />
      &nbsp;<a href="tiki-phpinfo.php">{tr}phpinfo{/tr}</a><br />
      &nbsp;<a href="tiki-admin_dsn.php">{tr}DSN{/tr}</a><br />
      &nbsp;<a href="tiki-admin_external_wikis.php">{tr}External wikis{/tr}</a><br />
    {/if}
    {/sortlinks}
  </div>
{/if}

{if $feature_usermenu eq 'y'}
  <div class="menu-subtitle">    
    {if $feature_menusfolderstyle eq 'y'}
      <a href="javascript:icntoggle('usrmenu');">
        <img src="img/icons/fo.gif" alt="" /></a>&nbsp;
    {else}
      <a href="javascript:toggle('usrmenu');">±</a>
    {/if}
    <a title="{tr}Click here to manage your personal menu{/tr}"
       href="tiki-usermenu.php" class="menu-subtitle">{tr}User Menu{/tr}</a>
  </div>

  {* Show user menu contents only if user have smth to show *}

  <div id="usrmenu" style="{$menu}">
  {if count($usr_user_menus) gt 0}
      {section name=ix loop=$usr_user_menus}
      &nbsp;<a {if $usr_user_menus[ix].mode eq 'n'}target="_blank"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a><br />
      {/section}
  {/if}
  </div>
{/if}
{/tikimodule}

{if $feature_menusfolderstyle eq 'y'}
<script type="text/javascript">
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
