{if $tiki_p_admin eq 'y'}
<div class="box">
<div class="box-title">
{tr}Admin{/tr}
</div>
<div class="box-data">
  <div class="button">&nbsp;<a href="tiki-admin.php" class="linkbut">{tr}admin{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-adminusers.php" class="linkbut">{tr}users{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-admingroups.php" class="linkbut">{tr}groups{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_cache.php" class="linkbut">{tr}cache{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-admin_modules.php" class="linkbut">{tr}modules{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-admin_links.php" class="linkbut">{tr}links{/tr}</a></div>
  <div class="button">&nbsp;<a href="tiki-list_gallery.php?galleryId=0" class="linkbut">{tr}system gallery{/tr}</a></div>
</div>
</div>
{/if}
