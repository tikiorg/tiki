<hr/>
<div id="page-bar">
<table>
<tr>
{if !$lock}

<td><div  class="button2"><a href="tiki-print.php?page={$page}" class="linkbut">{tr}printable{/tr}</a></div></td>

{if $tiki_p_edit eq 'y'}
<td><div class="button2"><a href="tiki-editpage.php?page={$page}" class="linkbut">{tr}edit{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_remove eq 'y'}
<td><div class="button2"><a href="tiki-removepage.php?page={$page}&amp;version=last" class="linkbut">{tr}remove page{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_admin eq 'y'}
{if $lock}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div></td>
{/if}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page}" class="linkbut">{tr}permissions{/tr}</a></div></td>
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
<td><div class="button2"><a href="tiki-likepages.php?page={$page}" class="linkbut">{tr}like pages{/tr}</a></div></td>
{/if}
</tr>
</table>
</div>
