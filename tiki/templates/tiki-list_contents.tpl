<h1><a class="wiki" href="tiki-list_contents.php">{tr}Dynamic content system{/tr}</a></h1>
<h3>Create or edit content block</h3><a class="wiki" href="tiki-list_contents.php">{tr}create new block{/tr}</a>
<form action="tiki-list_contents.php" method="post">
<input type="hidden" name="contentId" value="{$contentId}" />
<table>
<tr><td>Description:</td>
<td>
<textarea rows="5" cols="40" name="description">{$description}</textarea>
</td></tr>
<tr><td colspan="2" align="center">
<input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Available content blocks{/tr}</h3>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_contents.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'contentId_desc'}contentId_asc{else}contentId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading">{tr}Current version{/tr}</td>
<td class="heading">{tr}Next version{/tr}</td>
<td class="heading">{tr}Programmed versions{/tr}</td>
<td class="heading">{tr}Old versions{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].contentId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].actual|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].next|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].future}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].old}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-list_contents.php?remove={$listpages[changes].contentId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-list_contents.php?edit={$listpages[changes].contentId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}">{tr}Program{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].contentId}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].actual|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].next|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].future}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].old}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-list_contents.php?remove={$listpages[changes].contentId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-list_contents.php?edit={$listpages[changes].contentId}">{tr}Edit desc{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}">{tr}Program{/tr}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a href="tiki-list_contents.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-list_contents.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
