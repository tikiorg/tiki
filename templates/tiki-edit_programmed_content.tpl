<h1><a class="pagetitle" href="tiki-edit_programmed_content.php?contentId={$contentId}">{tr}Program dynamic content for block{/tr}: {$contentId}</a></h1>
<h4{tr}>Block description: {/tr}{$description}</h4>
<h3>{tr}Create or edit content{/tr}</h3>
{if $pId}
{tr}You are editing block:{/tr}{$pId}<br/>
{/if}
[<a class="wiki" href="tiki-edit_programmed_content.php?contentId={$contentId}">{tr}create new block{/tr}</a>|
<a class="wiki" href="tiki-list_contents.php">{tr}Return to block listing{/tr}</a>]<br/>

<form action="tiki-edit_programmed_content.php" method="post">
<input type="hidden" name="contentId" value="{$contentId}" />
<input type="hidden" name="pId" value="{$pId}" />
<table>
<tr><td>Description:</td>
<td>
<textarea rows="5" cols="40" name="data">{$data}</textarea>
</td></tr>
<tr><td>{tr}Publishing date{/tr}</td>
<td>{html_select_date time=$publishDate end_year="+1"} at {html_select_time time=$publishDate display_seconds=false}</td></tr>
<tr><td colspan="2" align="center">
<input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Available content blocks{/tr}</h3>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-edit_programmed_content.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'contentId_desc'}contentId_asc{else}contentId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publishing Date{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'data_desc'}data_asc{else}data_desc{/if}">{tr}Data{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $actual eq $listpages[changes].publishDate}
{assign var=class value=heading}
{else}
{if $actual > $listpages[changes].publishDate}
{assign var=class value=odd}
{else}
{assign var=class value=even}
{/if}
{/if}
<td class="{$class}">&nbsp;{$listpages[changes].pId}&nbsp;</td>
<td width="34%" class="{$class}">&nbsp;{$listpages[changes].publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td width="40%" class="{$class}">&nbsp;{$listpages[changes].data}&nbsp;</td>
<td class="{$class}">
<a class="link" href="tiki-edit_programmed_content.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;contentId={$contentId}&amp;remove={$listpages[changes].pId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;contentId={$contentId}&amp;edit={$listpages[changes].pId}">{tr}Edit{/tr}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="4">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
