<div id="page-bar">
<table>
<tr>
{if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
<td><div  class="button2"><a href="tiki-directory_admin.php" class="linkbut">{tr}admin{/tr}</a></div></td>
{/if}
<td><div  class="button2"><a href="tiki-directory_browse.php" class="linkbut">{tr}browse{/tr}</a></div></td>
<td><div  class="button2"><a href="tiki-directory_ranking.php?sort_mode=created_desc" class="linkbut">{tr}new sites{/tr}</a></div></td>
<td><div  class="button2"><a href="tiki-directory_ranking.php?sort_mode=hits_desc" class="linkbut">{tr}cool sites{/tr}</a></div></td>
{if $tiki_p_submit_link eq 'y' or $tiki_p_autosubmit_link eq 'y'}
<td><div  class="button2"><a href="tiki-directory_add_site.php" class="linkbut">{tr}add a site{/tr}</a></div></td>
{/if}
</tr>
</table>
</div>
