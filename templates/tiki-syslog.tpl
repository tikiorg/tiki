{* $Id$ *}
{title help="System+Log"}{tr}Tiki Logs{/tr}{/title}

<div class="navbar">
	 {button _text="{tr}Log SQL{/tr}" href="tiki-sqllog.php"}
</div>

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

{include file='find.tpl'}

<table class="normal">
<tr>
<th>{self_link _sort_arg="sort_mode" _sort_field="logid"}{tr}Id{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="logtype"}{tr}Type{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="logtime"}{tr}Time{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="loguser"}{tr}User{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="logmessage"}{tr}Message{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="logip"}{tr}IP{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="logclient"}{tr}Client{/tr}{/self_link}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr class="{cycle}">
<td>{$list[ix].logId}</td>
<td>{$list[ix].logtype|escape}</td>
<td><span title="{$list[ix].logtime|tiki_long_datetime}">{$list[ix].logtime|tiki_short_datetime}</span></td>
<td>{$list[ix].loguser|userlink}</td>
<td title="{$list[ix].logmessage|escape:'html'}">{$list[ix].logmessage|truncate:60|escape:'html'}</td>
<td>{$list[ix].logip|escape:"html"}</td>
<td><span title="{$list[ix].logclient|escape:'html'}">{$list[ix].logclient|truncate:30:"..."|escape:'html'}</span></td>
</tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

