<h2>{tr}User_versions_for{/tr}: {$ruser}</h2>
{if $preview}
<h2>{tr}Version{/tr}: {$version}</h2>
<div class="wikitext">{$preview.data}</div>
<br/> 
{/if}
<br/>
<div align="center">
<table  border="1" width="80%" cellpadding="0" cellspacing="0">
<tr>
<td id="heading">{tr}Date{/tr}</td>
<td id="heading">{tr}Page{/tr}</td>
<td id="heading">{tr}Version{/tr}</td>
<td id="heading">{tr}Ip{/tr}</td>
<td id="heading">{tr}Comment{/tr}</td>
<td id="heading">{tr}Action{/tr}</td>
</tr>
{section name=hist loop=$history}
<tr>
{if $smarty.section.hist.index % 2}
<td id="odd">&nbsp;{$history[hist].lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td id="odd">&nbsp;<a href="tiki-index.php?page={$history[hist].pageName}">{$history[hist].pageName}</a>&nbsp;</td>
<td id="odd">&nbsp;{$history[hist].version}&nbsp;</td>
<td id="odd">&nbsp;{$history[hist].ip}&nbsp;</td>
<td id="odd">&nbsp;{$history[hist].comment}&nbsp;</td>
<td id="odd">&nbsp;<a href="tiki-userversions.php?ruser={$ruser}&page={$history[hist].pageName}&preview=1&version={$history[hist].version}">{tr}view{/tr}</a>&nbsp;</td>
{else}
<td id="even">&nbsp;{$history[hist].lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td id="even">&nbsp;<a href="tiki-index.php?page={$history[hist].pageName}">{$history[hist].pageName}</a>&nbsp;</td>
<td id="even">&nbsp;{$history[hist].version}&nbsp;</td>
<td id="even">&nbsp;{$history[hist].ip}&nbsp;</td>
<td id="even">&nbsp;{$history[hist].comment}&nbsp;</td>
<td id="even">&nbsp;<a href="tiki-userversions.php?ruser={$ruser}&page={$history[hist].pageName}&preview=1&version={$history[hist].version}">{tr}view{/tr}</a>&nbsp;</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
