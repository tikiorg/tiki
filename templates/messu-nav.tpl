<div class="messunav">
<table>
<tr>
<td><div class="button2"><a class="linkbut" href="messu-mailbox.php">{tr}Mailbox{/tr}</a></div></td>
<td><div class="button2"><a class="linkbut" href="messu-compose.php">{tr}Compose{/tr}</a></div></td>
{if $tiki_p_broadcast eq 'y'}
<td><div class="button2"><a class="linkbut" href="messu-broadcast.php">{tr}Broadcast{/tr}</a></div></td>
{/if}
<td><div class="button2"><a class="linkbut" href="messu-sent.php">{tr}Sent{/tr}</a></div></td>
<td><div class="button2"><a class="linkbut" href="messu-archive.php">{tr}Archive{/tr}</a></div></td>
<td>{if $mess_archiveAfter>0}({tr}Auto-archive age for read messages:{/tr} {$mess_archiveAfter} {tr}days{/tr}){/if}
</td>
</tr></table>
</div>
