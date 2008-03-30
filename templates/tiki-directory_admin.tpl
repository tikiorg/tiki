{* $Id$ *}
<h1><a href="tiki-directory_admin.php" class="pagetitle">{tr}Directory Administration{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Directory{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-directory_admin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Directory tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

{include file=tiki-directory_admin_bar.tpl}

<b>{tr}Statistics{/tr}</b><br /><br />
{tr}There are{/tr} {$stats.invalid} {tr}invalid sites{/tr}<br />
{tr}There are{/tr} {$stats.valid} {tr}valid sites{/tr}<br />
{tr}There are{/tr} {$stats.categs} <a class="link" href="tiki-directory_admin_categories.php">{tr}Categories{/tr}</a><br />
{tr}Users have visited{/tr} {$stats.visits} {tr}sites from the directory{/tr}<br />
{tr}Users have searched{/tr} {$stats.searches} {tr}times from the directory{/tr}<br />
<br /><br />
<h2>{tr}Menu{/tr}</h2>
<ul>
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_categories.php">{tr}Admin Categories{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_sites eq 'y'}<li><a class="link" href="tiki-directory_admin_sites.php">{tr}Admin sites{/tr}</a></li>{/if}
 {if $tiki_p_admin_directory_cats eq 'y'}<li><a class="link" href="tiki-directory_admin_related.php">{tr}Admin category relationships{/tr}</a></li>{/if}
 {if $tiki_p_validate_links eq 'y'}<li><a class="link" href="tiki-directory_validate_sites.php">{tr}Validate links{/tr}</a></li>{/if}
 {if $tiki_p_admin eq 'y'}<li><a class="link" href="tiki-admin.php?page=directory">{tr}Settings{/tr}</a></li>{/if}
</ul>

