<a class="pagetitle" href="tiki-admin_poll_options.php?pollId={$pollId}">{tr}Admin Polls{/tr}: {$menu_info.title}</a><br/><br/>
<a href="tiki-admin_polls.php" class="linkbut">{tr}List polls{/tr}</a>
<a href="tiki-admin_polls.php?pollId={$pollId}" class="linkbut">{tr}Edit this poll{/tr}</a>
<h2>{tr}Preview poll{/tr}</h2>
<div style="text-align:left;width:130px;" class="tiki" align="center">
<div class="tiki-title">{$menu_info.name}</div>
<div class="tiki-content">{include file=tiki-poll.tpl}</div>
</div>
<br/>

<h2>{tr}Edit or add poll options{/tr}</h2>
<form action="tiki-admin_poll_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId|escape}" />
<input type="hidden" name="pollId" value="{$pollId|escape}" />
<table>
<tr><td>{tr}Option{/tr}:</td><td><input type="text" name="title" value="{$title|escape}" /></td>
<td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Poll options{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_poll_options.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table>
<tr>
<td class="heading"><a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading"><a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}votes{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $channels[user].type eq 's'}
<tr class="odd">
<td>{$channels[user].title}</td>
<td>{$channels[user].votes}</td>
<td>
   <a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
</td>
</tr>
{else}
<tr class="even">
<td>{$channels[user].title}</td>
<td>{$channels[user].votes}</td>
<td>
   <a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
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