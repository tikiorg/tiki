<a href="tiki-directory_admin.php" class="pagetitle">{tr}Directory Administration{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Directory" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Directory{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-directory_admin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin directory tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->










<br /><br />
{include file=tiki-directory_admin_bar.tpl}
<br /><br />
<b>{tr}Statistics{/tr}</b><br /><br />
{tr}There are{/tr} {$stats.invalid} {tr}invalid sites{/tr}<br />
{tr}There are{/tr} {$stats.valid} {tr}valid sites{/tr}<br />
{tr}There are{/tr} {$stats.categs} <a class="link" href="tiki-directory_admin_categories.php">{tr}categories{/tr}</a><br />
{tr}Users have visited{/tr} {$stats.visits} {tr}sites from the directory{/tr}<br />
{tr}Users have searched{/tr} {$stats.searches} {tr}times from the directory{/tr}<br />
<br /><br />
<h2>{tr}Menu{/tr}</h2>
<ul>
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_categories.php">{tr}Admin Categories{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_sites eq 'y'}<li><a class="link" href="tiki-directory_admin_sites.php">{tr}Admin Sites{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_related.php">{tr}Admin Category Relationships{/tr}</a></li>{/if}
 {if $tiki_p_validate_links eq 'y'}<li><a class="link" href="tiki-directory_validate_sites.php">{tr}Validate Links{/tr}</a></li>{/if}
 {if $tiki_p_admin eq 'y'}<li><a class="link" href="tiki-admin.php?page=directory">{tr}Settings{/tr}</a></li>{/if}
</ul>

