<a href="tiki-lastchanges.php?days={$days}" class="pagetitle">{tr}Last Changes{/tr}</a><br/><br/>
[<a  class="link" href="tiki-lastchanges.php?days=1">{tr}Today{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=2">{tr}Last{/tr} 2 {tr}days{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=3">{tr}Last{/tr} 3 {tr}days{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=5">{tr}Last{/tr} 5 {tr}Days{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=7">{tr}Last{/tr} {tr}Week{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=14">{tr}Last{/tr} 2 {tr}Weeks{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=31">{tr}Last{/tr} {tr}Month{/tr}</a>|
<a class="link" href="tiki-lastchanges.php?days=0">{tr}All{/tr}</a>]
<br/><br/>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-lastchanges.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
     <input type="hidden" name="days" value="0" />
   </form>
   </td>
{if $findwhat<>""}
   <td>
   <a href="tiki-lastchanges.php" class="wiki">{tr}Search by Date{/tr}</a>
   </td>
{/if}   
</tr>
</table>
<br/>
{if $findwhat<>""}
{tr}Found{/tr} "<b>{$findwhat}</b>" {tr}in{/tr} {$cant_records} {tr}LastChanges{/tr} 
{/if}
<div align="left">
<table width="94%" cellpadding="0" cellspacing="0" border="1">
<tr>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'action_desc'} action_asc{else}action_desc{/if}">{tr}Action{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'ip_desc'}ip_asc{else}ip_desc{/if}">{tr}Ip{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
</tr>
{section name=changes loop=$lastchanges}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$lastchanges[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<td class="odd">&nbsp;<a href="tiki-index.php?page={$lastchanges[changes].pageName}" class="tablename">{$lastchanges[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].action}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].user}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].ip}&nbsp;</td>
<td class="odd">&nbsp;{$lastchanges[changes].comment}&nbsp;</td>
{else}
<td class="even">&nbsp;{$lastchanges[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<td class="even">&nbsp;<a href="tiki-index.php?page={$lastchanges[changes].pageName}" class="tablename">{$lastchanges[changes].pageName|truncate:20:"(...)":true}</a>&nbsp;</td>
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
<br/>
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
