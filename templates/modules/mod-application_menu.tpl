<div class="box">
<div class="box-title">
<a class='flip' href="javascript:flip('mainmenu');">{tr}Menu{/tr}</a>
</div>
<div id='mainmenu' class="box-data">
&nbsp;<a href="{$tikiIndex}" class="linkmenu">{tr}home{/tr}</a><br/>
{if $feature_chat eq 'y'}
{if $tiki_p_chat eq 'y'}
&nbsp;<a href="tiki-chat.php" class="linkmenu">{tr}chat{/tr}</a><br/>
{/if}
{/if}

{if $user}
{if $feature_userPreferences eq 'y' and $user}
  &nbsp;<a href="tiki-user_preferences.php" class="linkmenu">{tr}user preferences{/tr}</a><br/>
{/if}
{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
  &nbsp;<a href="messu-mailbox.php" class="linkmenu">{tr}messages{/tr}</a><br/>
{/if}
{if $feature_contact eq 'y'}
  &nbsp;<a href="tiki-contact.php" class="linkmenu">{tr}contact us{/tr}</a><br/>
{/if}
{/if}

{if $feature_stats eq 'y' and $tiki_p_view_stats eq 'y'}
  &nbsp;<a href="tiki-stats.php" class="linkmenu">{tr}stats{/tr}</a><br/>
{/if}

{if $feature_categories eq 'y'}
  &nbsp;<a href="tiki-browse_categories.php" class="linkmenu">{tr}categories{/tr}</a><br/>
{/if}

{if $feature_games eq 'y' and $tiki_p_play_games eq 'y'}
  &nbsp;<a href="tiki-list_games.php" class="linkmenu">{tr}games{/tr}</a><br/>
{/if}

{if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
  &nbsp;<a href="tiki-webmail.php" class="linkmenu">{tr}webmail{/tr}</a><br/>
{/if}

{if $feature_wiki eq 'y'}
  <div class="separator"><a class='separator' href="javascript:setCookie('wikimenu','c');hide('wikimenu');">[-]</a> <a class='separator' href='tiki-index.php'>{tr}Wiki{/tr}</a> <a class='separator' href="javascript:setCookie('wikimenu','o');show('wikimenu');">[+]</a></div>
  <div id="wikimenu" style="{$mnu_wikimenu}">
  {if $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-index.php" class="linkmenu">{tr}home{/tr}</a><br/>
  {/if}
  {if $feature_lastChanges eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-lastchanges.php" class="linkmenu">{tr}last changes{/tr}</a><br/>
  {/if}
  {if $feature_dump eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="dump/new.tar" class="linkmenu">{tr}dump{/tr}</a><br/>
  {/if}
  {if $feature_wiki_rankings eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-wiki_rankings.php" class="linkmenu">{tr}rankings{/tr}</a><br/>
  {/if}
  {if $feature_listPages eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-listpages.php" class="linkmenu">{tr}list pages{/tr}</a><br/>
    &nbsp;<a href="tiki-orphan_pages.php" class="linkmenu">{tr}orphan pages{/tr}</a><br/>
  {/if}
  {if $feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-editpage.php?page=SandBox" class="linkmenu">{tr}sandbox{/tr}</a><br/>
  {/if}
  {if $feature_wiki_multiprint eq 'y'}
    &nbsp;<a href="tiki-print_pages.php" class="linkmenu">{tr}print{/tr}</a><br/>
  {/if}
  {if $tiki_p_send_pages eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}send{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_received_pages eq 'y'}
    &nbsp;<a href="tiki-received_pages.php" class="linkmenu">{tr}received pages{/tr}</a><br/>
  {/if}
  {if $tiki_p_edit_structures eq 'y'}
   &nbsp;<a href="tiki-admin_structures.php" class="linkmenu">{tr}structures{/tr}</a><br/>
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('galmenu','c');hide('galmenu');">[-]</a> 
  <a class='separator' href="tiki-galleries.php">{tr}Image Gals{/tr}</a> 
  <a class='separator' href="javascript:setCookie('galmenu','o');show('galmenu');">[+]</a></div>
  <div id='galmenu' style="{$mnu_galmenu}">
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries.php" class="linkmenu">{tr}Galleries{/tr}</a><br/>
  {/if}
  {if $feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a href="tiki-upload_image.php" class="linkmenu">{tr}Upload image{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_galleries eq 'y'}
  &nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkmenu">{tr}System gallery{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('cmsmenu','c');hide('cmsmenu');">[-]</a> <a class='separator' href='tiki-view_articles.php'>{tr}CMS{/tr}</a> <a class='separator' href="javascript:setCookie('cmsmenu','o');show('cmsmenu');">[+]</a></div>
  <div id='cmsmenu' style="{$mnu_cmsmenu}">
  {if $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-view_articles.php" class="linkmenu">{tr}Articles Home{/tr}</a><br/>
  &nbsp;<a href="tiki-list_articles.php" class="linkmenu">{tr}List articles{/tr}</a><br/>
  {/if}
  {if $feature_cms_rankings eq 'y' and $tiki_p_read_article eq 'y'}
  &nbsp;<a href="tiki-cms_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y'}
    &nbsp;<a href="tiki-edit_submission.php" class="linkmenu">{tr}Submit article{/tr}</a><br/>
    {/if}
    {if $tiki_p_submit_article eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
    &nbsp;<a href="tiki-list_submissions.php" class="linkmenu">{tr}View submissions{/tr}</a><br/>
    {/if}
  {/if}
  {if $tiki_p_edit_article eq 'y'}
      &nbsp;<a href="tiki-edit_article.php" class="linkmenu">{tr}Edit article{/tr}</a><br/>
  {/if}
  {if $tiki_p_send_articles eq 'y' and $feature_comm eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}Send articles{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_received_articles eq 'y'}
    &nbsp;<a href="tiki-received_articles.php" class="linkmenu">{tr}Received articles{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_cms eq 'y'}
      &nbsp;<a href="tiki-admin_topics.php" class="linkmenu">{tr}Admin topics{/tr}</a><br/>
  {/if}  
  </div>
{/if}

{if $feature_blogs eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('blogmenu','c');hide('blogmenu');">[-]</a> 
  <a class='separator' href="tiki-list_blogs.php">{tr}Blogs{/tr}</a>
  <a class='separator' href="javascript:setCookie('blogmenu','o');show('blogmenu');">[+]</a></div>
  <div id='blogmenu' style="{$mnu_blogmenu}">
  {if $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-list_blogs.php" class="linkmenu">{tr}List blogs{/tr}</a><br/>
  {/if}
  {if $feature_blog_rankings eq 'y' and $tiki_p_read_blog eq 'y'}
  &nbsp;<a href="tiki-blog_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  &nbsp;<a href="tiki-edit_blog.php" class="linkmenu">{tr}Create/Edit Blog{/tr}</a><br/>
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  &nbsp;<a href="tiki-blog_post.php" class="linkmenu">{tr}Post{/tr}</a><br/>
  {/if}
  {if $tiki_p_blog_admin eq 'y'}
  &nbsp;<a href="tiki-list_posts.php" class="linkmenu">{tr}Admin posts{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_forums eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('formenu','c');hide('formenu');">[-]</a> 
  <a class='separator' href="tiki-forums.php">{tr}Forums{/tr}</a>
  <a class='separator' href="javascript:setCookie('formenu','o');show('formenu');">[+]</a></div>
  <div id='formenu' style="{$mnu_formenu}">
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forums.php" class="linkmenu">{tr}Forums{/tr}</a><br/>
  {/if}
  {if $feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forum_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_forum eq 'y'}
  &nbsp;<a href="tiki-admin_forums.php" class="linkmenu">{tr}Admin forums{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_directory eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('dirmenu','c');hide('dirmenu');">[-]</a> 
  <a class='separator' href="tiki-directory_browse.php">{tr}Directory{/tr}</a>
  <a class='separator' href="javascript:setCookie('dirmenu','o');show('dirmenu');">[+]</a></div>
  <div id='dirmenu' style="{$mnu_dirmenu}">
  {if $tiki_p_view_directory eq 'y'}
  &nbsp;<a href="tiki-directory_browse.php" class="linkmenu">{tr}Directory{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
  &nbsp;<a href="tiki-directory_admin.php" class="linkmenu">{tr}Admin{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_file_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('filegalmenu','c');hide('filegalmenu');">[-]</a> 
  <a class='separator' href="tiki-file_galleries.php">{tr}File Galleries{/tr}</a>
  <a class='separator' href="javascript:setCookie('filegalmenu','o');show('filegalmenu');">[+]</a></div>
  <div id='filegalmenu' style="{$mnu_filegalmenu}">
  {if $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries.php" class="linkmenu">{tr}List galleries{/tr}</a><br/>
  {/if}
  {if $feature_file_galleries_rankings eq 'y' and $tiki_p_view_file_gallery eq 'y'}
  &nbsp;<a href="tiki-file_galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  &nbsp;<a href="tiki-upload_file.php" class="linkmenu">{tr}Upload file{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_faqs eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('faqsmenu','c');hide('faqsmenu');">[-]</a> 
  <a href="tiki-list_faqs.php" class="separator">{tr}FAQs{/tr}</a>
  <a class='separator' href="javascript:setCookie('faqsmenu','o');show('faqsmenu');">[+]</a></div>
  <div id='faqsmenu' style="{$mnu_faqsmenu}">
  {if $tiki_p_view_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="linkmenu">{tr}List FAQs{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_faqs eq 'y'}
  &nbsp;<a href="tiki-list_faqs.php" class="linkmenu">{tr}Admin FAQs{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_quizzes eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('quizmenu','c');hide('quizmenu');">[-]</a> 
  <a href="tiki-list_quizzes.php" class="separator">{tr}Quizzes{/tr}</a>
  <a class='separator' href="javascript:setCookie('quizmenu','o');show('quizmenu');">[+]</a></div>
  <div id='quizmenu' style="{$mnu_quizmenu}">
  &nbsp;<a href="tiki-list_quizzes.php" class="linkmenu">{tr}List Quizzes{/tr}</a><br/>
  {if $tiki_p_view_quiz_stats eq 'y'}
  &nbsp;<a href="tiki-quiz_stats.php" class="linkmenu">{tr}Quiz stats{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_quizzes eq 'y'}
  &nbsp;<a href="tiki-edit_quiz.php" class="linkmenu">{tr}Admin{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_trackers eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('trkmenu','c');hide('trkmenu');">[-]</a> 
  <a href="tiki-list_trackers.php" class="separator">{tr}Trackers{/tr}</a>
  <a class='separator' href="javascript:setCookie('trkmenu','o');show('trkmenu');">[+]</a></div>
  <div id='trkmenu' style="{$mnu_trkmenu}">
  &nbsp;<a href="tiki-list_trackers.php" class="linkmenu">{tr}List Trackers{/tr}</a><br/>
  {if $tiki_p_admin_trackers eq 'y'}
  &nbsp;<a href="tiki-admin_trackers.php" class="linkmenu">{tr}Admin{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_surveys eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('srvmenu','c');hide('srvmenu');">[-]</a> 
  <a href="tiki-list_surveys.php" class="separator">{tr}Surveys{/tr}</a>
  <a class='separator' href="javascript:setCookie('srvmenu','o');show('srvmenu');">[+]</a></div>
  <div id='srvmenu' style="{$mnu_srvmenu}">
  &nbsp;<a href="tiki-list_surveys.php" class="linkmenu">{tr}List Surveys{/tr}</a><br/>
  {if $tiki_p_view_survey_stats eq 'y'}
  &nbsp;<a href="tiki-survey_stats.php" class="linkmenu">{tr}Stats{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_surveys eq 'y'}
  &nbsp;<a href="tiki-admin_surveys.php" class="linkmenu">{tr}Admin{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_newsletters eq 'y'}
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('nlmenu','c');hide('nlmenu');">[-]</a> 
  <a href="tiki-newsletters.php" class="separator">{tr}Newsletters{/tr}</a>
  <a class='separator' href="javascript:setCookie('nlmenu','o');show('nlmenu');">[+]</a></div>
  <div id='nlmenu' style="{$mnu_nlmenu}">
  {if $tiki_p_admin_newsletters eq 'y'}
  &nbsp;<a href="tiki-admin_newsletters.php" class="linkmenu">{tr}Admin{/tr}</a><br/>
  &nbsp;<a href="tiki-send_newsletters.php" class="linkmenu">{tr}Send newsletters{/tr}</a><br/>
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
 $tiki_p_admin_shoutbox eq 'y'
 }
 
  <div class="separator"><a class='separator' href="javascript:javascript:setCookie('admmnu','c');hide('admmnu');">[-]</a>{if $tiki_p_admin eq 'y'}<a class='separator' href='tiki-admin.php'>{/if} {tr}Admin (click!){/tr}{if $tiki_p_admin eq 'y'}</a>{/if} <a class='separator' href="javascript:setCookie('admmnu','o');show('admmnu');">[+]</a></div>
  <div id='admmnu' style="{$mnu_admmnu}">

    {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-adminusers.php" class="linkmenu">{tr}Users{/tr}</a><br/>
      &nbsp;<a href="tiki-admingroups.php" class="linkmenu">{tr}Groups{/tr}</a><br/>
      &nbsp;<a href="tiki-list_cache.php" class="linkmenu">{tr}Cache{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_links.php" class="linkmenu">{tr}Links{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_hotwords.php" class="linkmenu">{tr}Hotwords{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_rssmodules.php" class="linkmenu">{tr}RSS modules{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_menus.php" class="linkmenu">{tr}Menus{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_polls.php" class="linkmenu">{tr}Polls{/tr}</a><br/>
      &nbsp;<a href="tiki-backup.php" class="linkmenu">{tr}Backups{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_notifications.php" class="linkmenu">{tr}Mail notifications{/tr}</a><br/>
      &nbsp;<a href="tiki-search_stats.php" class="linkmenu">{tr}Search stats{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_chat eq 'y'}
      &nbsp;<a href="tiki-admin_chat.php" class="linkmenu">{tr}Chat{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_categories eq 'y'}
      &nbsp;<a href="tiki-admin_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br/>
    {/if}   
    {if $tiki_p_admin_banners eq 'y'}
      &nbsp;<a href="tiki-list_banners.php" class="linkmenu">{tr}Banners{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_templates eq 'y'}
      &nbsp;<a href="tiki-edit_templates.php" class="linkmenu">{tr}Edit templates{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_drawings eq 'y'}
      &nbsp;<a href="tiki-admin_drawings.php" class="linkmenu">{tr}Admin drawings{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_dynamic eq 'y'}
      &nbsp;<a href="tiki-list_contents.php" class="linkmenu">{tr}Dynamic content{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_cookies eq 'y'}
      &nbsp;<a href="tiki-admin_cookies.php" class="linkmenu">{tr}Cookies{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_mailin eq 'y'}
      &nbsp;<a href="tiki-admin_mailin.php" class="linkmenu">{tr}Mail-in{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_content_templates eq 'y'}
      &nbsp;<a href="tiki-admin_content_templates.php" class="linkmenu">{tr}Content templates{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_html_pages eq 'y'}
      &nbsp;<a href="tiki-admin_html_pages.php" class="linkmenu">{tr}HTML pages{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_shoutbox eq 'y'}
      &nbsp;<a href="tiki-shoutbox.php" class="linkmenu">{tr}Shoutbox{/tr}</a><br/>
    {/if}
    {if $tiki_p_view_referer_stats eq 'y'}
    &nbsp;<a href="tiki-referer_stats.php" class="linkmenu">{tr}Referer stats{/tr}</a><br/>
    {/if}
    &nbsp;<a href="tiki-import_phpwiki.php" class="linkmenu">{tr}Wiki Import dump{/tr}</a><br/>
    &nbsp;<a href="tiki-phpinfo.php" class="linkmenu">{tr}phpinfo{/tr}</a><br/>
  </div>
{/if}

</div>
</div>
