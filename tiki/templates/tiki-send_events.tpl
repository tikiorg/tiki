<a class="pagetitle" href="tiki-send_events.php">{tr}Send events{/tr}</a><br />
{if $emited eq 'y'}
{tr}The event was sent to {$sent} email addresses{/tr}<br /><br />
{/if}
{if $presend eq 'y'}
<div class="wikitext">{$subject}</div>
<div class="wikitext">{$dataparsed}</div>
{tr}This event will be sent to {$subscribers} email addresses.{/tr}
<form method="post" action="tiki-send_events.php">
<input type="hidden" name="evId" value="{$evId|escape}" />
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="submit" name="send" value="{tr}send{/tr}" />
<input type="submit" name="preview" value="{tr}cancel{/tr}" />
</form>
{else}
{if $preview eq 'y'}
<br />
<div class="wikitext">{$info.subject}</div>
<div class="wikitext">{$info.dataparsed}</div>
{if $txt}<div class="wikitext">{$txt}</div>{/if}
{/if}
<h2>{tr}Prepare a event to be sent{/tr}</h2>
<form action="tiki-send_events.php" method="post" id='editpageform'>
<table class="normal">
<tr><td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor"><input type="text" maxlength="250" size="40" name="subject" value="{$info.subject|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Event{/tr}:</td><td class="formcolor">
<select name="evId">
{section loop=$events name=ix}
<option value="{$events[ix].evId|escape}" {if $events[ix].evId eq $evId}selected="selected"{/if}>{$events[ix].name}</option>
{/section}
</select>
</td></tr>

{if $tiki_p_use_content_templates eq 'y'}
<tr><td class="formcolor">{tr}Apply template{/tr}</td><td class="formcolor">
<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{$templates[ix].name}</option>
{/section}
</select>
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Data{/tr}:<br /><br />{include file="textareasize.tpl" area_name='editnl' formId='editpageform'}</td>
<td class="formcolor"><textarea id='editnl' name="data" rows="{$rows}" cols="{$cols}">{$info.data|escape}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Send Events{/tr}" /></td></tr>
</table>
</form>
{/if}

{if $evId}
<h2>{tr}Sent editions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-send_events.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}">{tr}sent{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].subject}</td>
<td class="odd">{$channels[user].users}</td>
<td class="odd">{$channels[user].sent|tiki_short_datetime}</td>
<td class="odd">
<a class="link" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;evId={$evId}&amp;remove={$channels[user].editionId}">{tr}remove{/tr}</a>
<a class="link" href="tiki-send_events.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;evId={$evId}&amp;editionId={$channels[user].editionId}">{tr}use{/tr}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-send_events.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-send_events.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-send_events.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{/if}
