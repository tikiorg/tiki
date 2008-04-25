{* $Id$ *}
<h1><a class="pagetitle" href="tiki-syslog.php">{tr}SysLog{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}System+Log" target="tikihelp" class="tikihelp" title="{tr}System log help{/tr}: {tr}system logs{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-syslog.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system logs tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}
</h1>

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}

<br /><br />

<form method="get" action="tiki-syslog.php">
{tr}Clean logs older than{/tr}&nbsp;
<input type="text" name="months" size="4" /> {tr}months{/tr}
<input type="submit" value="{tr}Clean{/tr}" name="clean" />
</form>

<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
<td class="findtable">
<form method="get" action="tiki-syslog.php">
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" value="{tr}Find{/tr}" name="search" />
<input type="text" name="max" value="{$prefs.maxRecords|escape}" size="4" /> {tr}Rows{/tr}
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
</td></tr></table>
</div>

<div class="simplebox">
<table class="normal">
<tr>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logid_{if $sort_mode eq 'logid_desc'}asc{else}desc{/if}" class="tableheading">{tr}Id{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logtype_{if $sort_mode eq 'logtype_desc'}asc{else}desc{/if}" class="tableheading">{tr}Type{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logtime_{if $sort_mode eq 'logtime_desc'}asc{else}desc{/if}" class="tableheading">{tr}Time{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=loguser_{if $sort_mode eq 'loguser_desc'}asc{else}desc{/if}" class="tableheading">{tr}User{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logmessage_{if $sort_mode eq 'logmessage_desc'}asc{else}desc{/if}" class="tableheading">{tr}Message{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logip_{if $sort_mode eq 'logip_desc'}asc{else}desc{/if}" class="tableheading">{tr}IP{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;max={$prefs.maxRecords}&amp;offset={$offset}&amp;sort_mode=logclient_{if $sort_mode eq 'logclient_desc'}asc{else}desc{/if}" class="tableheading">{tr}Client{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr class="{cycle}">
<td>{$list[ix].logId}</td>
<td>{$list[ix].logtype}</td>
<td><span title="{$list[ix].logtime|tiki_long_datetime}">{$list[ix].logtime|tiki_short_datetime}</span></td>
<td>{$list[ix].loguser}</td>
<td title="{$list[ix].logmessage|escape:'html'}">{$list[ix].logmessage|truncate:60|escape:'html'}</td>
<td>{$list[ix].logip|escape:"html"}</td>
<td><span title="{$list[ix].logclient|escape:'html'}">{$list[ix].logclient|truncate:24:"..."|escape:'html'}</span></td>
</tr>
{/section}
</table>
<br />
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>
