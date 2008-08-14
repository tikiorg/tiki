<div class="navbar" id="page-bar">
  <a class="linkbut" href="tiki-directory_browse.php" class="linkbut">{tr}Browse{/tr}</a>
  <a class="linkbut" href="tiki-directory_admin.php" class="linkbut">{tr}Admin{/tr}</a>
  {if $tiki_p_admin_directory_cats eq 'y'}<a class="linkbut" href="tiki-directory_admin_categories.php" class="linkbut">{tr}Categories{/tr}</a>{/if}
  {if $tiki_p_admin_directory_cats eq 'y'}<a class="linkbut" href="tiki-directory_admin_related.php" class="linkbut">{tr}Related{/tr}</a>{/if}
  {if $tiki_p_admin_directory_sites eq 'y'}<a class="linkbut" href="tiki-directory_admin_sites.php" class="linkbut">{tr}Sites{/tr}</a>{/if}
  {if $tiki_p_validate_links eq 'y'}<a class="linkbut" href="tiki-directory_validate_sites.php" class="linkbut">{tr}Validate{/tr}</a>{/if}
</div>
