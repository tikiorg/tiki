<div id="page-bar">
<table>
<tr>
<td><div  class="button2"><a href="tiki-directory_admin.php" class="linkbut">{tr}Admin{/tr}</a></div></td>
<td><div  class="button2"><a href="tiki-directory_browse.php" class="linkbut">{tr}Browse{/tr}</a></div></td>
{if $tiki_p_admin_directory_cats eq 'y'}<td><div  class="button2"><a href="tiki-directory_admin_categories.php" class="linkbut">{tr}Categories{/tr}</a></div></td>{/if}
{if $tiki_p_admin_directory_cats eq 'y'}<td><div  class="button2"><a href="tiki-directory_admin_related.php" class="linkbut">{tr}Related{/tr}</a></div></td>{/if}
{if $tiki_p_admin_directory_sites eq 'y'}<td><div  class="button2"><a href="tiki-directory_admin_sites.php" class="linkbut">{tr}Sites{/tr}</a></div></td>{/if}
{if $tiki_p_validate_links eq 'y'}<td><div  class="button2"><a href="tiki-directory_validate_sites.php" class="linkbut">{tr}Validate{/tr}</a></div></td>{/if}
</tr>
</table>
</div>
