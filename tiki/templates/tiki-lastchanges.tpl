<h1>{tr}Last Changes{/tr}</h1>
<a  class="wiki" href="tiki-lastchanges.php?days=1">{tr}Today{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=2">{tr}Last{/tr} 2 {tr}days{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=3">{tr}Last{/tr} 3 {tr}days{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=5">{tr}Last{/tr} 5 {tr}Days{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=7">{tr}Last{/tr} {tr}Week{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=14">{tr}Last{/tr} 2 {tr}Weeks{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=31">{tr}Last{/tr} {tr}Month{/tr}</a>|
<a class="wiki" href="tiki-lastchanges.php?days=0">{tr}All{/tr}</a>
<br/>
<br/>
<div align="center">
<table width="94%" cellpadding="0" cellspacing="0" border="1">
<tr>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'action_desc'} action_asc{else}action_desc{/if}">{tr}Action{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'ip_desc'}ip_asc{else}ip_desc{/if}">{tr}Ip{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="link" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
</tr>
{section name=changes loop=$lastchanges}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$lastchanges[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;<a href="tiki-index.php?page={$lastchanges[changes].pageName}" class="wiki">{$lastchanges[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].action}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].user}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].ip}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].comment}&nbsp;</td>
{else}
<td class="even">&nbsp;{$lastchanges[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;<a href="tiki-index.php?page={$lastchanges[changes].pageName}" class="wiki">{$lastchanges[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
<td class="even">&nbsp;{$lastchanges[changes].action}&nbsp;</td>
<td class="even">&nbsp;{$lastchanges[changes].user}&nbsp;</td>
<td class="even">&nbsp;{$lastchanges[changes].ip}&nbsp;</td>
<td class="even">&nbsp;{$lastchanges[changes].comment}&nbsp;</td>
{/if}
</tr>
{sectionelse}
<tr><td class="even" colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a href="tiki-lastchanges.php?days={$days}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-lastchanges.php?days={$days}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
