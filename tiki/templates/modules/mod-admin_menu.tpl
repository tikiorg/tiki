{if $tiki_p_admin eq 'y'}
<div class="box">
<div class="box-title">
<a class='flip' href="javascript:flip('adminmenu');">{tr}Admin{/tr}</a>
</div>
<div id='adminmenu' class="box-data">
  <div class="button">&nbsp;<a href="tiki-admin.php" class="linkbut">{tr}Admin{/tr}</a></div>
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
  <div class="separator">CMS</div>
  <div class="button">&nbsp;<a href="tiki-admin_topics.php" class="linkbut">{tr}Topics{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_articles.php" class="linkbut">{tr}Articles{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-edit_article.php" class="linkbut">{tr}Edit article{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_submissions.php" class="linkbut">{tr}Submissions{/tr}</a></div>
  <div class="separator">Blogs</div>
  <div class="button">&nbsp;<a href="tiki-list_blogs.php" class="linkbut">{tr}Blogs{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_posts.php" class="linkbut">{tr}Posts{/tr}</a></div>
  <div class="separator">Banners</div>
  <div class="button">&nbsp;<a href="tiki-list_banners.php" class="linkbut">{tr}Banners{/tr}</a></div>
  <div class="separator">Templates</div>
  <div class="button">&nbsp;<a href="tiki-edit_templates.php" class="linkbut">{tr}Edit templates{/tr}</a></div>
  <div class="separator">Dynamic content system</div>
  <div class="button">&nbsp;<a href="tiki-list_contents.php" class="linkbut">{tr}Admin content{/tr}</a></div>
</div>
</div>
{/if}

