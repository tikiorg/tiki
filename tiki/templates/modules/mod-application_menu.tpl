<div class="box">
<div class="box-title">
<a class='flip' href="javascript:flip('mainmenu');">{tr}Menu{/tr}</a>
</div>
<div id='mainmenu' class="box-data">
&nbsp;<a href="{$tikiIndex}" class="linkmenu">{tr}home{/tr}</a></br>
{if $feature_chat eq 'y'}
{if $tiki_p_chat eq 'y'}
&nbsp;<a href="tiki-chat.php" class="linkmenu">{tr}chat{/tr}</a><br/>
{/if}
{/if}
{if $user}
{if $feature_userPreferences eq 'y'}
  &nbsp;<a href="tiki-user_preferences.php" class="linkmenu">{tr}user preferences{/tr}</a><br/>
{/if}
{if $feature_categories eq 'y'}
  &nbsp;<a href="tiki-browse_categories.php" class="linkmenu">{tr}categories{/tr}</a><br/>
{/if}
{/if}
{if $feature_wiki eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('wikimenu');">[-]</a> <a class='separator' href='tiki-index.php'>Wiki</a> <a class='separator' href="javascript:show('wikimenu');">[+]</a></div>
  <div id="wikimenu">
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
  {/if}
  {if $feature_sandbox eq 'y' and $tiki_p_view eq 'y'}
    &nbsp;<a href="tiki-editpage.php?page=SandBox" class="linkmenu">{tr}sandbox{/tr}</a><br/>
  {/if}
  {if $tiki_p_send_pages eq 'y'}
    &nbsp;<a href="tiki-send_objects.php" class="linkmenu">{tr}send{/tr}</a><br/>
  {/if}
  {if $tiki_p_admin_received_pages eq 'y'}
    &nbsp;<a href="tiki-received_pages.php" class="linkmenu">{tr}received pages{/tr}</a><br/>
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('galmenu');">[-]</a> 
  {if $home_gallery > 0}<a class='separator' href="tiki-browse_gallery.php?galleryId={$home_gallery}">{/if}{tr}Image Gals{/tr}{if $home_gallery > 0}</a>{/if} 
  <a class='separator' href="javascript:show('galmenu');">[+]</a></div>
  <div id='galmenu'>
  {if $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries.php" class="linkmenu">{tr}Galleries{/tr}</a><br/>
  {/if}
  {if $feature_gal_rankings eq 'y' and $tiki_p_view_image_gallery eq 'y'}
    &nbsp;<a href="tiki-galleries_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    &nbsp;<a href="tiki-upload_image.php" class="linkmenu">{tr}Upload image{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_articles eq 'y' or $feature_submissions eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('cmsmenu');">[-]</a> <a class='separator' href='tiki-view_articles.php'>{tr}CMS{/tr}</a> <a class='separator' href="javascript:show('cmsmenu');">[+]</a></div>
  <div id='cmsmenu'>
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
    {if $tiki_p_read_article eq 'y' and $tiki_p_submit_article eq 'y'}
    &nbsp;<a href="tiki-list_submissions.php" class="linkmenu">{tr}View submissions{/tr}</a><br/>
    {/if}
  {/if}
  </div>
{/if}
{if $feature_blogs eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('blogmenu');">[-]</a> 
  {if $home_blog > 0}<a class='separator' href="tiki-view_blog.php?blogId={$home_blog}">{/if}{tr}Blogs{/tr}{if $home_blog > 0}</a>{/if}
  <a class='separator' href="javascript:show('blogmenu');">[+]</a></div>
  <div id='blogmenu'>
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
  </div>
{/if}

{if $feature_forums eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('formenu');">[-]</a> 
  {if $home_forum > 0}<a class='separator' href="tiki-view_forum.php?forumId={$home_forum}">{/if}{tr}Forums{/tr}{if $home_forum > 0}</a>{/if}
  <a class='separator' href="javascript:show('formenu');">[+]</a></div>
  <div id='formenu'>
  {if $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forums.php" class="linkmenu">{tr}Forums{/tr}</a><br/>
  {/if}
  {if $feature_forum_rankings eq 'y' and $tiki_p_forum_read eq 'y'}
  &nbsp;<a href="tiki-forum_rankings.php" class="linkmenu">{tr}Rankings{/tr}</a><br/>
  {/if}
  </div>
{/if}

{if $feature_file_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('filegalmenu');">[-]</a> 
  {if $home_file_gallery > 0}<a class='separator' href="tiki-list_file_gallery.php?galleryId={$home_file_gallery}">{/if}{tr}File Galleries{/tr}{if $home_file_gallery > 0}</a>{/if}
  <a class='separator' href="javascript:show('filegalmenu');">[+]</a></div>
  <div id='filegalmenu'>
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

{if $tiki_p_admin eq 'y' or $tiki_p_edit_articles eq 'y'
 or $tiki_p_edit_submission eq 'y' 
 or $tiki_p_remove_submission eq 'y'
 or $tiki_p_remove_article eq 'y'
 or $tiki_p_approve_submission eq 'y'
 or $tiki_p_blog_admin eq 'y'
 or $tiki_p_edit_templates eq 'y'
 or $tiki_p_admin_dynamic eq 'y'
 or $tiki_p_admin_banners eq 'y'
 or $tiki_p_admin_chat eq 'y'
 or $tiki_p_admin_forum eq 'y'
 or $tiki_p_admin_categories eq 'y'
 }
 
  <div class="separator"><a class='separator' href="javascript:hide('admmnu');">[-]</a>{if $tiki_p_admin eq 'y'}<a class='separator' href='tiki-admin.php'>{/if} {tr}Admin{/tr}{if $tiki_p_admin eq 'y'}</a>{/if} <a class='separator' href="javascript:show('admmnu');">[+]</a></div>
  <div id='admmnu'>
    
    {if $tiki_p_admin eq 'y'}
    <div class="separator">{tr}General{/tr}</div>
      &nbsp;<a href="tiki-adminusers.php" class="linkmenu">{tr}Users{/tr}</a><br/>
      &nbsp;<a href="tiki-admingroups.php" class="linkmenu">{tr}Groups{/tr}</a><br/>
      &nbsp;<a href="tiki-list_cache.php" class="linkmenu">{tr}Cache{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_modules.php" class="linkmenu">{tr}Modules{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_links.php" class="linkmenu">{tr}Links{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_hotwords.php" class="linkmenu">{tr}Hotwords{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_rssmodules.php" class="linkmenu">{tr}RSS modules{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_menus.php" class="linkmenu">{tr}Menus{/tr}</a><br/>
      &nbsp;<a href="tiki-admin_polls.php" class="linkmenu">{tr}Polls{/tr}</a><br/>
    <div class="separator">Galleries</div>
      &nbsp;<a href="tiki-galleries.php" class="linkmenu">{tr}Galleries{/tr}</a><br/>
      &nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkmenu">{tr}System gallery{/tr}</a><br/>
    <div class="separator">Wiki</div>
      &nbsp;<a href="tiki-listpages.php" class="linkmenu">{tr}Pages{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_chat eq 'y'}
    <div class="separator">{tr}Chat{/tr}</div>
    &nbsp;<a href="tiki-admin_chat.php" class="linkmenu">{tr}Admin chat{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_article eq 'y' or 
        $tiki_p_edit_submission eq 'y' or 
        $tiki_p_remove_submission eq 'y' or  
        $tiki_p_remove_article eq 'y' or
        $tiki_p_approve_submission eq 'y'}
    <div class="separator">CMS</div>
    {/if}
      {if $tiki_p_admin eq 'y'}
      &nbsp;<a href="tiki-admin_topics.php" class="linkmenu">{tr}Topics{/tr}</a><br/>
      {/if}
      {if $tiki_p_edit_article eq 'y' or $tiki_p_remove_article eq 'y'}
      &nbsp;<a href="tiki-list_articles.php" class="linkmenu">{tr}Articles{/tr}</a><br/>
      &nbsp;<a href="tiki-edit_article.php" class="linkmenu">{tr}Edit article{/tr}</a><br/>
      {/if}
      {if $tiki_p_edit_submission eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
      &nbsp;<a href="tiki-list_submissions.php" class="linkmenu">{tr}Submissions{/tr}</a><br/>
      {/if}
    {if $tiki_p_blog_admin eq 'y'}
    <div class="separator">{tr}Blogs{/tr}</div>
      &nbsp;<a href="tiki-list_blogs.php" class="linkmenu">{tr}Blogs{/tr}</a><br/>
      &nbsp;<a href="tiki-list_posts.php" class="linkmenu">{tr}Posts{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_forum eq 'y'}
    <div class="separator">{tr}Forums{/tr}</div>
      &nbsp;<a href="tiki-admin_forums.php" class="linkmenu">{tr}Admin forums{/tr}</a><br/>
    {/if}
    {if $feature_categories eq 'y' and $tiki_p_admin_categories eq 'y'}
    <div class="separator">{tr}Categories{/tr}</div>
      &nbsp;<a href="tiki-admin_categories.php" class="linkmenu">{tr}Categories{/tr}</a><br/>
    {/if}   
    {if $tiki_p_admin_banners eq 'y'}
    <div class="separator">{tr}Banners{/tr}</div>
      &nbsp;<a href="tiki-list_banners.php" class="linkmenu">{tr}Banners{/tr}</a><br/>
    {/if}
    {if $tiki_p_edit_templates eq 'y'}
    <div class="separator">{tr}Templates{/tr}</div>
      &nbsp;<a href="tiki-edit_templates.php" class="linkmenu">{tr}Edit templates{/tr}</a><br/>
    {/if}
    {if $tiki_p_admin_dynamic eq 'y'}
    <div class="separator">{tr}Dynamic content system{/tr}</div>
      &nbsp;<a href="tiki-list_contents.php" class="linkmenu">{tr}Admin content{/tr}</a><br/>
    {/if}
  </div>
{/if}

</div>
</div>
