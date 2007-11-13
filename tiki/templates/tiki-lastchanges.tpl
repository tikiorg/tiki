<h1><a href="tiki-lastchanges.php?days={$days}" class="pagetitle">{tr}Last Changes{/tr}</a></h1>
<div class="navbar">
<a class="linkbut" href="tiki-lastchanges.php?days=1">{tr}Today{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=2">{tr}Last{/tr} 2 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=3">{tr}Last{/tr} 3 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=5">{tr}Last{/tr} 5 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=7">{tr}Last{/tr} {tr}week{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=14">{tr}Last{/tr} 2 {tr}Weeks{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=31">{tr}Last{/tr} {tr}month{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=0">{tr}All{/tr}</a>
</div>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-lastchanges.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="days" value="0" />
   </form>
   </td>
{if $findwhat != ""}
   <td>
   <a href="tiki-lastchanges.php" class="wiki">{tr}Search by Date{/tr}</a>
   </td>
{/if}   
</tr>
</table>
<br />
{if $findwhat!=""}
{tr}Found{/tr} "<b>{$findwhat}</b>" {tr}in{/tr} {$cant_records} {tr}LastChanges{/tr} 
{/if}
<div align="left">
<table class="normal">
<tr>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'object_desc'}object_asc{else}object_desc{/if}">{tr}Page{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'action_desc'}action_asc{else}action_desc{/if}">{tr}Action{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'ip_desc'}ip_asc{else}ip_desc{/if}">{tr}Ip{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$lastchanges}
<tr class="{cycle}">
<td>&nbsp;{$lastchanges[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<td>&nbsp;<a href='tiki-index.php?page={$lastchanges[changes].pageName|escape:"url"}' class="tablename" title="{$lastchanges[changes].pageName}">{$lastchanges[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true}</a> 
{if $lastchanges[changes].version}
(<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{tr}hist{/tr}</a> {tr}v{/tr}{$lastchanges[changes].version})
&nbsp;<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;preview={$lastchanges[changes].version}' title="{tr}View{/tr}">v</a>&nbsp;
{if $tiki_p_rollback eq 'y'}
<a class="link" href='tiki-rollback.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;version={$lastchanges[changes].version}' title="{tr}rollback{/tr}">b</a>&nbsp;
{/if}
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff={$lastchanges[changes].version}' title="{tr}compare{/tr}">c</a>&nbsp;
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff2={$lastchanges[changes].version}' title="{tr}diff{/tr}">d</a>&nbsp;
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;source={$lastchanges[changes].version}' title="{tr}Source{/tr}">s</a>
{elseif $lastchanges[changes].versionlast}
(<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{tr}hist{/tr}</a>)
{/if}

</td>

<td>{tr}{$lastchanges[changes].action}{/tr}</td>
<td>&nbsp;{$lastchanges[changes].user}&nbsp;</td>
<td>&nbsp;{$lastchanges[changes].ip}&nbsp;</td>
<td>&nbsp;{$lastchanges[changes].comment}&nbsp;</td>

</tr>
{sectionelse}
<tr><td class="even" colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
