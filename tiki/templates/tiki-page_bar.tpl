<hr/>
<div id="page-bar">
<table>
<tr>
<td><div  class="button2"><a href="tiki-print.php?page={$page}" class="linkbut">{tr}print{/tr}</a></div></td>
{if !$lock}
{if $tiki_p_edit eq 'y' or $page eq 'SandBox'}
{if $beingEdited eq 'y'}
<td><div class="button2" ><a style="background: #FFAAAA;" href="tiki-editpage.php?page={$page}" class="linkbut">{tr}edit{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-editpage.php?page={$page}" class="linkbut">{tr}edit{/tr}</a></div></td>
{/if}
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_remove eq 'y'}
<td><div class="button2"><a href="tiki-removepage.php?page={$page}&amp;version=last" class="linkbut">{tr}remove{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_admin_wiki eq 'y'}
{if $lock}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div></td>
{/if}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page}" class="linkbut">{tr}perms{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $feature_history eq 'y'}
<td><div class="button2"><a href="tiki-pagehistory.php?page={$page}" class="linkbut">{tr}history{/tr}</a></div></td>
{/if}
{/if}
{if $feature_backlinks eq 'y'}
<td><div class="button2"><a href="tiki-backlinks.php?page={$page}" class="linkbut">{tr}backlinks{/tr}</a></div></td>
{/if}
{if $feature_likePages eq 'y'}
<td><div class="button2"><a href="tiki-likepages.php?page={$page}" class="linkbut">{tr}similar{/tr}</a></div></td>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;undo=1" class="linkbut">{tr}undo{/tr}</a></div></td>
{/if}
{if $show_slideshow eq 'y'}
<td><div class="button2"><a href="tiki-slideshow.php?page={$page}" class="linkbut">{tr}slides{/tr}</a></div></td>
{/if}
</tr>
</table>
</div>
