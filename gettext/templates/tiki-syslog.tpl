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
  <label>{tr}Clean logs older than{/tr}&nbsp;
  <input type="text" name="months" size="4" /></label> {tr}months{/tr}
  <input type="submit" value="{tr}Clean{/tr}" name="clean" />
</form>

{include file='find.tpl'}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

<table class="normal">
<tr>
<th>{self_link _sort_arg="sort_mode" _sort_field="id"}{tr}Id{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="action"}{tr}Type{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="lastModif"}{tr}Time{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="user"}{tr}User{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="action"}{tr}Message{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="ip"}{tr}IP{/tr}{/self_link}</th>
<th>{self_link _sort_arg="sort_mode" _sort_field="client"}{tr}Client{/tr}{/self_link}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr class="{cycle}">
<td class="id">{$list[ix].actionId}</td>
<td class="text">{$list[ix].object|escape}</td>
<td class="date"><span title="{$list[ix].lastModif|tiki_long_datetime}">{$list[ix].lastModif|tiki_short_datetime}</span></td>
<td class="username">{$list[ix].user|userlink}</td>
<td class="text" title="{$list[ix].action|escape:'html'}">{$list[ix].action|truncate:60|escape:'html'}</td>
<td class="text">{$list[ix].ip|escape:"html"}</td>
<td class="text"><span title="{$list[ix].client|escape:'html'}">{$list[ix].client|truncate:30:"..."|escape:'html'}</span></td>
</tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

