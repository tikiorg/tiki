{literal}
<script type="text/javascript">
<!--
function flip(foo) {
  //document.getElementById(foo).style.visibility="hidden";
  if( document.getElementById(foo).style.display == "none") {
    document.getElementById(foo).style.display="block";
  } else {
    document.getElementById(foo).style.display="none";
  }
}
function show(foo) {
  
    document.getElementById(foo).style.display="block";

}
function hide(foo) {
  
    document.getElementById(foo).style.display="none";

}
// -->
</script>
{/literal}
<div class="box">
<div class="box-title">
<a class='flip' href="javascript:flip('mainmenu');">{tr}Menu{/tr}</a>
</div>
<div id='mainmenu' class="box-data">
<div class="button">&nbsp;<a href="{$tikiIndex}" class="linkbut">{tr}home{/tr}</a></div>
{if $user}
{if $feature_userPreferences eq 'y'}
  <div class="button">&nbsp;<a href="tiki-user_preferences.php" class="linkbut">{tr}user preferences{/tr}</a></div>
{/if}
{/if}
{if $feature_wiki eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('wikimenu');">[-]</a> <a class='separator' href='tiki-index.php'>Wiki</a> <a class='separator' href="javascript:show('wikimenu');">[+]</a></div>
  <div id="wikimenu">
  <div class="button">&nbsp;<a href="tiki-index.php" class="linkbut">{tr}home{/tr}</a></div>
  {if $feature_lastChanges eq 'y'}
    <div class="button">&nbsp;<a href="tiki-lastchanges.php" class="linkbut">{tr}last changes{/tr}</a></div>
  {/if}
  {if $feature_dump eq 'y'}
    <div class="button">&nbsp;<a href="dump/new.tar" class="linkbut">{tr}dump{/tr}</a></div>
  {/if}
  {if $feature_wiki_rankings eq 'y'}
    <div class="button">&nbsp;<a href="tiki-wiki_rankings.php" class="linkbut">{tr}rankings{/tr}</a></div>
  {/if}
  {if $feature_listPages eq 'y'}
    <div class="button">&nbsp;<a href="tiki-listpages.php" class="linkbut">{tr}list pages{/tr}</a></div>
  {/if}
  {if $feature_sandbox eq 'y'}
    <div class="button">&nbsp;<a href="tiki-editpage.php?page=SandBox" class="linkbut">{tr}sandbox{/tr}</a></div>
  {/if}
  </div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('galmenu');">[-]</a> 
  {if $home_gallery > 0}<a class='separator' href="tiki-browse_gallery.php?galleryId={$home_gallery}">{/if}{tr}Image Gals{/tr}{if $home_gallery > 0}</a>{/if} 
  <a class='separator' href="javascript:show('galmenu');">[+]</a></div>
  <div id='galmenu'>
  <div class="button">&nbsp;<a href="tiki-galleries.php" class="linkbut">{tr}Galleries{/tr}</a></div>
  {if $feature_gal_rankings eq 'y'}
    <div class="button">&nbsp;<a href="tiki-galleries_rankings.php" class="linkbut">{tr}Rankings{/tr}</a></div>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    <div class="button">&nbsp;<a href="tiki-upload_image.php" class="linkbut">{tr}Upload image{/tr}</a></div>
  {/if}
  </div>
{/if}

{if $feature_articles}
  <div class="separator"><a class='separator' href="javascript:hide('cmsmenu');">[-]</a> <a class='separator' href='tiki-view_articles.php'>{tr}CMS{/tr}</a> <a class='separator' href="javascript:show('cmsmenu');">[+]</a></div>
  <div id='cmsmenu'>
  <div class="button">&nbsp;<a href="tiki-view_articles.php" class="linkbut">{tr}Articles Home{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_articles.php" class="linkbut">{tr}List articles{/tr}</a></div>
  {if $feature_cms_rankings eq 'y'}
  <div class="button">&nbsp;<a href="tiki-cms_rankings.php" class="linkbut">{tr}Rankings{/tr}</a></div>
  {/if}
  {if $feature_submissions eq 'y'}
    {if $tiki_p_submit_article eq 'y'}
    <div class="button">&nbsp;<a href="tiki-edit_submission.php" class="linkbut">{tr}Submit article{/tr}</a></div>
    {/if}
    <div class="button">&nbsp;<a href="tiki-list_submissions.php" class="linkbut">{tr}View submissions{/tr}</a></div>
  {/if}
  </div>
{/if}
{if $feature_blogs eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('blogmenu');">[-]</a> 
  {if $home_blog > 0}<a class='separator' href="tiki-view_blog.php?blogId={$home_blog}">{/if}{tr}Blogs{/tr}{if $home_blog > 0}</a>{/if}
  <a class='separator' href="javascript:show('blogmenu');">[+]</a></div>
  <div id='blogmenu'>
  <div class="button">&nbsp;<a href="tiki-list_blogs.php" class="linkbut">{tr}List blogs{/tr}</a></div>
  {if $feature_blog_rankings eq 'y'}
  <div class="button">&nbsp;<a href="tiki-blog_rankings.php" class="linkbut">{tr}Rankings{/tr}</a></div>
  {/if}
  {if $tiki_p_create_blogs eq 'y'}
  <div class="button">&nbsp;<a href="tiki-edit_blog.php" class="linkbut">{tr}Create/Edit Blog{/tr}</a></div>
  {/if}
  {if $tiki_p_blog_post eq 'y'}
  <div class="button">&nbsp;<a href="tiki-blog_post.php" class="linkbut">{tr}Post{/tr}</a></div>
  {/if}
  </div>
{/if}
{if $feature_file_galleries eq 'y'}
  <div class="separator"><a class='separator' href="javascript:hide('filegalmenu');">[-]</a> 
  {if $home_file_gallery > 0}<a class='separator' href="tiki-list_file_gallery.php?galleryId={$home_file_gallery}">{/if}{tr}File Galleries{/tr}{if $home_file_gallery > 0}</a>{/if}
  <a class='separator' href="javascript:show('filegalmenu');">[+]</a></div>
  <div id='filegalmenu'>
  <div class="button">&nbsp;<a href="tiki-file_galleries.php" class="linkbut">{tr}List galleries{/tr}</a></div>
  {if $feature_file_galleries_rankings eq 'y'}
  <div class="button">&nbsp;<a href="tiki-file_galleries_rankings.php" class="linkbut">{tr}Rankings{/tr}</a></div>
  {/if}
  {if $tiki_p_upload_files eq 'y'}
  <div class="button">&nbsp;<a href="tiki-upload_file.php" class="linkbut">{tr}Upload file{/tr}</a></div>
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
 }
 
  <div class="separator"><a class='separator' href="javascript:hide('admmnu');">[-]</a>{if $tiki_p_admin eq 'y'}<a class='separator' href='tiki-admin.php'>{/if}{tr}Admin{/tr}{if $tiki_p_admin eq 'y'}</a>{/if} <a class='separator' href="javascript:show('admmnu');">[+]</a></div>
  <div id='admmnu'>
    {if $tiki_p_admin eq 'y'}
    <div class="separator">General</div>
      <div class="button">&nbsp;<a href="tiki-adminusers.php" class="linkbut">{tr}Users{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-admingroups.php" class="linkbut">{tr}Groups{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-list_cache.php" class="linkbut">{tr}Cache{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-admin_modules.php" class="linkbut">{tr}Modules{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-admin_links.php" class="linkbut">{tr}Links{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-admin_hotwords.php" class="linkbut">{tr}Hotwords{/tr}</a></div>
    <div class="separator">Galleries</div>
      <div class="button">&nbsp;<a href="tiki-galleries.php" class="linkbut">{tr}Galleries{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkbut">{tr}System gallery{/tr}</a></div>
    <div class="separator">Wiki</div>
      <div class="button">&nbsp;<a href="tiki-listpages.php" class="linkbut">{tr}Pages{/tr}</a></div>
    {/if}
    {if $tiki_p_edit_article eq 'y' or 
        $tiki_p_edit_submission eq 'y' or 
        $tiki_p_remove_submission eq 'y' or  
        $tiki_p_remove_article eq 'y' or
        $tiki_p_approve_submission eq 'y'}
    <div class="separator">CMS</div>
    {/if}
      {if $tiki_p_admin eq 'y'}
      <div class="button">&nbsp;<a href="tiki-admin_topics.php" class="linkbut">{tr}Topics{/tr}</a></div>
      {/if}
      {if $tiki_p_edit_article eq 'y' or $tiki_p_remove_article eq 'y'}
      <div class="button">&nbsp;<a href="tiki-list_articles.php" class="linkbut">{tr}Articles{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-edit_article.php" class="linkbut">{tr}Edit article{/tr}</a></div>
      {/if}
      {if $tiki_p_edit_submission eq 'y' or $tiki_p_approve_submission eq 'y' or $tiki_p_remove_submission eq 'y'}
      <div class="button">&nbsp;<a href="tiki-list_submissions.php" class="linkbut">{tr}Submissions{/tr}</a></div>
      {/if}
    {if $tiki_p_blog_admin eq 'y'}
    <div class="separator">Blogs</div>
      <div class="button">&nbsp;<a href="tiki-list_blogs.php" class="linkbut">{tr}Blogs{/tr}</a></div>
      <div class="button">&nbsp;<a href="tiki-list_posts.php" class="linkbut">{tr}Posts{/tr}</a></div>
    {/if}
    {if $tiki_p_admin_banners eq 'y'}
    <div class="separator">Banners</div>
      <div class="button">&nbsp;<a href="tiki-list_banners.php" class="linkbut">{tr}Banners{/tr}</a></div>
    {/if}
    {if $tiki_p_edit_templates eq 'y'}
    <div class="separator">Templates</div>
      <div class="button">&nbsp;<a href="tiki-edit_templates.php" class="linkbut">{tr}Edit templates{/tr}</a></div>
    {/if}
    {if $tiki_p_admin_dynamic eq 'y'}
    <div class="separator">Dynamic content system</div>
      <div class="button">&nbsp;<a href="tiki-list_contents.php" class="linkbut">{tr}Admin content{/tr}</a></div>
    {/if}
  </div>
{/if}

</div>
</div>
