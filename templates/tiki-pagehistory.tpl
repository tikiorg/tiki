<h1>{tr}History of{/tr}: <a href="tiki-index.php?page={$page}" class="pagetitle">{$page}</a></h1>
{if $preview}
<h2>{tr}Version{/tr}: {$version}</h2>
<div  class="wikitext">{$preview.data}</div>
<br/> 
{/if}
{if $diff}
<h2>Diff</h2>
<table width="97%">
<tr>
  <td class="textbl">{tr}Actual_version{/tr}</td>
  <td class="textbl">{tr}Version{/tr}:{$version}</td>
</tr>
<tr>
  <td valign="top" width="50%"><div class="wikitext">{$parsed}</div></td>
  <td valign="top" width="50%"><div class="wikitext">{$diff}</div></td>
</tr>
</table>
{/if}
<br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0">
<tr>
<td class="heading">{tr}Date{/tr}</td>
<td class="heading">{tr}Version{/tr}</td>
<td class="heading">{tr}User{/tr}</td>
<td class="heading">{tr}Ip{/tr}</td>
<td class="heading">{tr}Comment{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
<tr>
<td class="odd">&nbsp;{$info.lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$info.version}&nbsp;</td>
<td class="odd">&nbsp;{$info.user}&nbsp;</td>
<td class="odd">&nbsp;{$info.ip}&nbsp;</td>
<td class="odd">&nbsp;{$info.comment}&nbsp;</td>
<td class="odd">&nbsp;[{tr}current_version{/tr}]&nbsp;</td>
</tr>
{section name=hist loop=$history}
<tr>
{if $smarty.section.hist.index % 2}
<td class="odd">&nbsp;{$history[hist].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$history[hist].version}&nbsp;</td>
<td class="odd">&nbsp;{$history[hist].user}&nbsp;</td>
<td class="odd">&nbsp;{$history[hist].ip}&nbsp;</td>
<td class="odd">&nbsp;{$history[hist].comment}&nbsp;</td>
<td class="odd">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page}&amp;preview={$history[hist].version}">{tr}view{/tr}</a>&nbsp;
{if $tiki_p_remove eq 'y'}
<a class="link" href="tiki-removepage.php?page={$page}&amp;version={$history[hist].version}">{tr}remove{/tr}</a>&nbsp;
{/if}
{if $tiki_p_rollback eq 'y'}
<a class="link" href="tiki-rollback.php?page={$page}&amp;version={$history[hist].version}">{tr}rollback{/tr}</a>&nbsp;
{/if}
<a class="link" href="tiki-pagehistory.php?page={$page}&amp;diff={$history[hist].version}">{tr}diff{/tr}</a>&nbsp;
</td>
{else}
<td class="even">&nbsp;{$history[hist].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$history[hist].version}&nbsp;</td>
<td class="even">&nbsp;{$history[hist].user}&nbsp;</td>
<td class="even">&nbsp;{$history[hist].ip}&nbsp;</td>
<td class="even">&nbsp;{$history[hist].comment}&nbsp;</td>
<td class="even">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page}&amp;preview={$history[hist].version}">{tr}view{/tr}</a>&nbsp;
{if $tiki_p_remove eq 'y'}
<a class="link" href="tiki-removepage.php?page={$page}&amp;version={$history[hist].version}">{tr}remove{/tr}</a>&nbsp;
{/if}
{if $tiki_p_rollback eq 'y'}
<a class="link" href="tiki-rollback.php?page={$page}&amp;version={$history[hist].version}">{tr}rollback{/tr}</a>&nbsp;
{/if}
<a class="link" href="tiki-pagehistory.php?page={$page}&amp;diff={$history[hist].version}">{tr}diff{/tr}</a>&nbsp;
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
