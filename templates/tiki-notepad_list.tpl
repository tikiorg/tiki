{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_list.php">{tr}Notes{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<a class="link" href="tiki-notepad_write.php">{tr}Write a note{/tr}</a>
<br/><br/>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-notepad_list.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<form action="tiki-notepad_list.php" method="post">
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading" width="80%"><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading" width="10%"><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
<td class="heading" width="10%"><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="note[{$channels[user].noteId}]" />
</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-notepad_read.php?notekId={$channels[user].noteId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].lastModif|tiki_short_datetime}</td>
<td class="{cycle}">{$channels[user].size}</td>
</tr>
{/section}
</table>
</form>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>


<h3>{tr}Add or edit a task{/tr}</h3>
<form action="tiki-notepad_list.php" method="post">
<input type="hidden" name="taskId" value="{$taskId}" />
<input type="hidden" name="tasks_useDates" value="{$tasks_useDates}" />
<table class="normal">
  <tr><td class="formcolor">{tr}Title{/tr}</td>
      <td class="formcolor"><input type="text" name="title" value="{$info.title}" /></td>
  </tr>
  <tr><td class="formcolor">{tr}Description{/tr}</td>
      <td class="formcolor">
        <textarea rows="10" cols="80" name="description">{$info.description}</textarea>
      </td>
  </tr>
  {if $tasks_useDates eq 'y'}
  
  <tr><td class="formcolor">{tr}Start date{/tr}</td>
      <td class="formcolor">{html_select_date time=$info.date end_year="+1"}</td>
  </tr>
  
  {if $info.status eq 'c'}
  <tr><td class="formcolor">{tr}Completed{/tr}</td>
      <td class="formcolor">{$info.completed|tiki_short_date}</td>
  </tr>
  {/if}
  {/if}
  
  <tr><td class="formcolor">{tr}Status{/tr}</td>
      <td class="formcolor">
        <select name="status">
          <option value="o" {if $info.status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
          <option value="c" {if $info.status eq 'c'}selected="selected"{/if}>{tr}completed{/tr}</option>
        </select>
      </td>
  </tr>
  <tr><td class="formcolor">{tr}Priority{/tr}</td>
      <td class="formcolor">
        <select name="priority">
          <option value="1" {if $info.priority eq 1}selected="selected"{/if}>1</option>
          <option value="2" {if $info.priority eq 2}selected="selected"{/if}>2</option>
          <option value="3" {if $info.priority eq 3}selected="selected"{/if}>3</option>
          <option value="4" {if $info.priority eq 4}selected="selected"{/if}>4</option>
          <option value="5" {if $info.priority eq 5}selected="selected"{/if}>5</option>
        </select>
      </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Percentage completed{/tr}</td>
    <td class="formcolor">
      <select name="percentage">
      {html_options values="$comp_array" output="$comp_array_p" selected="$info.percentage"}
      </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>

