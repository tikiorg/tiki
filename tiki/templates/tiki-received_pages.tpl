<a class="pagetitle" href="tiki-received_pages.php">Received pages</a><br/><br/>
{if $receivedPageId > 0 or $view eq 'y'}
<h2>Preview</h2>
<div class="wikitext">{$parsed}</div>
{/if}
{if $receivedPageId > 0}
<h2>{tr}Edit received page{/tr}</h2>
<form action="tiki-received_pages.php" method="post">
<input type="hidden" name="receivedPageId" value="{$receivedPageId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="pageName" value="{$pageName}" /></td></tr>
<tr><td class="formcolor">{tr}Data{/tr}:</td><td class="formcolor"><textarea name="data" rows="10" cols="60">{$data}</textarea></td></tr>
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor">
<input type="text" name="comment" value="{$comment}" />
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>Received pages</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_pages.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}comment{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].receivedPageId}</td>
{if $channels[user].exists eq 'y'}
<td class="odd"><span class="warn">{$channels[user].pageName}</span></td>
{else}
<td class="odd">{$channels[user].pageName}</td>
{/if}
<td class="odd">{$channels[user].comment}</td>
<td class="odd">{$channels[user].receivedDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td>
<td class="odd">{$channels[user].receivedFromSite}</td>
<td class="odd">{$channels[user].receivedFromUser}</td>
<td class="odd">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedPageId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$channels[user].receivedPageId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedPageId}">{tr}view{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedPageId}">{tr}accept{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].receivedPageId}</td>
{if $channels[user].exists eq 'y'}
<td class="even"><span class="warn">{$channels[user].pageName}</span></td>
{else}
<td class="even">{$channels[user].pageName}</td>
{/if}
<td class="even">{$channels[user].comment}</td>
<td class="even">{$channels[user].receivedDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td>
<td class="even">{$channels[user].receivedFromSite}</td>
<td class="even">{$channels[user].receivedFromUser}</td>
<td class="even">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedPageId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$channels[user].receivedPageId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedPageId}">{tr}view{/tr}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedPageId}">{tr}accept{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-received_pages.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-received_pages.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-received_pages.php?offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
