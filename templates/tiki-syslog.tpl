<a class="pagetitle" href="tiki-syslog.php">{tr}SysLog{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Syslog" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}system logs{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-syslog.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system logs tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt="{tr}edit tpl{/tr}" /></a>{/if}
<br /><br />

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}

<br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
<td class="findtable">
<form method="get" action="tiki-syslog.php">
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" value="{tr}find{/tr}" name="search" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
</td></tr></table>

<div class="simplebox">
<table class="normal">
<tr>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logid_{if $sort_mode eq 'logid_desc'}_asc{else}_desc{/if}" class="heading">{tr}Id{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logtype_{if $sort_mode eq 'logtype_desc'}_asc{else}_desc{/if}" class="heading">{tr}Type{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logtime_{if $sort_mode eq 'logtime_desc'}_asc{else}_desc{/if}" class="heading">{tr}Time{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logmessage_{if $sort_mode eq 'logmessage_desc'}_asc{else}_desc{/if}" class="heading">{tr}Message{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=loguser_{if $sort_mode eq 'loguser_desc'}_asc{else}_desc{/if}" class="heading">{tr}User{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logip_{if $sort_mode eq 'logip_desc'}_asc{else}_desc{/if}" class="heading">{tr}IP{/tr}</a></td>
<td class="heading"><a href="tiki-syslog.php?find={$find|escape}&amp;offset={$offset}&amp;sort_mode=logclient_{if $sort_mode eq 'logclient_desc'}_asc{else}_desc{/if}" class="heading">{tr}Client{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr class="{cycle}">
<td>{$list[ix].logId}</td>
<td>{$list[ix].logtype}</td>
<td>{$list[ix].logtime}</td>
<td>{$list[ix].logmessage}</td>
<td>{$list[ix].loguser}</td>
<td>{$list[ix].logip}</td>
<td>{$list[ix].logclient}</td>
</tr>
{/section}
</table>

{include file="tiki-pagination.tpl"}
</div>
