<a class="pagetitle" href="tiki-admin_hotwords.php">{tr}Admin Hotwords{/tr}</a>
<h3>Add Hotword</h3>
<form method="post" action="tiki-admin_hotwords.php">
<table class="normal">
<tr><td class="formcolor">{tr}Word{/tr}</td><td class="formcolor"><input type="text" name="word"></td></tr>
<tr><td class="formcolor">{tr}URL{/tr}</td><td class="formcolor"><input type="text" name="url" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="add" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_hotwords.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
<br/><br/>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'word_desc'}word_asc{else}word_desc{/if}">{tr}Word{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$words}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$words[user].word}</td>
<td class="odd">{$words[user].url}</td>
<td class="odd">
<a class="link" href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word}">{tr}remove{/tr}</a>
</td>                                   
</tr>
{else}
<tr>
<td class="even">{$words[user].word}</td>
<td class="even">{$words[user].url}</td>
<td class="even">
<a class="link" href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word}">{tr}remove{/tr}</a>
</td>                                   
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" class="link" href="tiki-admin_hotwords.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" class="link" href="tiki-admin_hotwords.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>

