<a class="pagetitle" href="tiki-admin_poll_options.php?pollId={$pollId}">Admin Polls: {$menu_info.title}</a><br/><br/>
[<a href="tiki-admin_polls.php" class="link">{tr}List polls{/tr}</a>|
<a href="tiki-admin_polls.php?pollId={$pollId}" class="link">{tr}Edit this poll{/tr}</a>]
<h2>{tr}Preview poll{/tr}</h2>
<div align="center">
<div style="text-align:left;width:130px;" class="cbox">
<div class="cbox-title">{$menu_info.name}</div>
<div class="cbox-data">
{include file=tiki-poll.tpl}
</div>
</div>
</div>
<br/>


<h2>{tr}Edit or add poll options{/tr}</h2>
<form action="tiki-admin_poll_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId}" />
<input type="hidden" name="pollId" value="{$pollId}" />
<table>
<tr><td class="form">{tr}Option{/tr}:</td><td><input type="text" name="title" value="{$title}" /></td>
<td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>Poll options</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_poll_options.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}votes{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $channels[user].type eq 's'}
<tr>
<td class="odd">{$channels[user].title}</td>
<td class="odd">{$channels[user].votes}</td>
<td class="odd">
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
   
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].title}</td>
<td class="even">{$channels[user].votes}</td>
<td class="even">
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_poll_options.php?find={$find}&amp;pollId={$pollId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_poll_options.php?find={$find}&amp;pollId={$pollId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_poll_options.php?find={$find}&amp;pollId={$pollId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

