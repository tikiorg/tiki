{* $Id$ *}
{title help="System+Log"}{tr}SysLog{/tr}{/title}

{if $tikifeedback}
  <br />
  {section name=n loop=$tikifeedback}
    <div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>
  {/section}
{/if}

<br /><br />

<form method="get" action="tiki-syslog.php">
  {tr}Clean logs older than{/tr}&nbsp;
  <input type="text" name="months" size="4" /> {tr}months{/tr}
  <input type="submit" value="{tr}Clean{/tr}" name="clean" />
</form>

{include file='find.tpl' _sort_mode='y'}

<div class="simplebox">
<table class="normal">
<tr>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logid_{if $sort_mode eq 'logid_desc'}asc{else}desc{/if}">{tr}Id{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logtype_{if $sort_mode eq 'logtype_desc'}asc{else}desc{/if}">{tr}Type{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logtime_{if $sort_mode eq 'logtime_desc'}asc{else}desc{/if}">{tr}Time{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=loguser_{if $sort_mode eq 'loguser_desc'}asc{else}desc{/if}">{tr}User{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logmessage_{if $sort_mode eq 'logmessage_desc'}asc{else}desc{/if}">{tr}Message{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logip_{if $sort_mode eq 'logip_desc'}asc{else}desc{/if}">{tr}IP{/tr}</a></th>
<th><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logclient_{if $sort_mode eq 'logclient_desc'}asc{else}desc{/if}">{tr}Client{/tr}</a></th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr class="{cycle}">
<td>{$list[ix].logId}</td>
<td>{$list[ix].logtype}</td>
<td><span title="{$list[ix].logtime|tiki_long_datetime}">{$list[ix].logtime|tiki_short_datetime}</span></td>
<td>{$list[ix].loguser|userlink}</td>
<td title="{$list[ix].logmessage|escape:'html'}">{$list[ix].logmessage|truncate:60|escape:'html'}</td>
<td>{$list[ix].logip|escape:"html"}</td>
<td><span title="{$list[ix].logclient|escape:'html'}">{$list[ix].logclient|truncate:24:"..."|escape:'html'}</span></td>
</tr>
{/section}
</table>
<br />
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>
