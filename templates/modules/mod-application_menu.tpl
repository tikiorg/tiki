{literal}
<script language="javascript">
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
</script>
{/literal}
<div class="box">
<div class="box-title">
<a class='flip' href="javascript:flip('mainmenu');">{tr}Menu{/tr}</a>
</div>
<div id='mainmenu' class="box-data">
<div class="button">&nbsp;<a href="{$tikiIndex}" class="linkbut">{tr}home{/tr}</a></div>
{if $feature_userPreferences eq 'y'}
  <div class="button">&nbsp;<a href="tiki-user_preferences.php" class="linkbut">{tr}user preferences{/tr}</a></div>
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
  <div class="separator"><a class='separator' href="javascript:hide('galmenu');">[-]</a> <a class='separator' href="tiki-browse_gallery?galleryId={$home_gallery}">{tr}Galleries{/tr}</a> <a class='separator' href="javascript:show('galmenu');">[+]</a></div>
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
  <div class="separator"><a class='separator' href="javascript:hide('blogmenu');">[-]</a> <a class='separator' href="tiki-view_blog?blogId={$home_blog}">{tr}Blogs{/tr}</a> <a class='separator' href="javascript:show('blogmenu');">[+]</a></div>
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
</div>
</div>
