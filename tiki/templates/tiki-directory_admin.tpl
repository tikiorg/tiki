<a href="tiki-directory_admin.php" class="pagetitle">{tr}Directory Administration{/tr}</a><br/><br/>
{include file=tiki-directory_admin_bar.tpl}
<br/><br/>
<b>{tr}Statistics{/tr}</b><br/><br/>
{tr}There are{/tr} {$stats.invalid} {tr}invalid sites{/tr}<br/>
{tr}There are{/tr} {$stats.valid} {tr}valid sites{/tr}<br/>
{tr}There are{/tr} {$stats.categs} <a class="link" href="tiki-directory_admin_categories.php">{tr}categories{/tr}</a><br/>
{tr}Users have visited{/tr} {$stats.visits} {tr}sites from the directory{/tr}<br/>
{tr}Users have searched{/tr} {$stats.searches} {tr}times from the directory{/tr}<br/>
<br/><br/>
<h2>{tr}Menu{/tr}</h2>
<ul>
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_categories.php">{tr}Admin categories{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_sites eq 'y'}<li><a class="link" href="tiki-directory_admin_sites.php">{tr}Admin sites{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_related.php">{tr}Admin category relationships{/tr}</a></li>{/if}
 {if $tiki_p_validate_links eq 'y'}<li><a class="link" href="tiki-directory_validate_sites.php">{tr}Validate links{/tr}</a></li>{/if}
 {if $tiki_p_admin eq 'y'}<li><a class="link" href="tiki-admin.php#directory">{tr}Settings{/tr}</a></li>{/if}
</ul>

