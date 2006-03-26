{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_actionlog.tpl,v 1.12 2006-03-26 15:56:38 sylvieg Exp $ *}

<h1><a href="tiki-admin_actionlog.php" class="pagetitle">{tr}Admin Action Log{/tr}</a></h1>
<a name="Setting" />
<h2>{tr}Setting{/tr}</h2>
<form method="post" action="tiki-admin_actionlog.php">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="smallnormal">
<tr><th class="heading">{tr}recorded{/tr}</th><th class="heading">{tr}viewed{/tr}</th><th class="heading">{tr}action{/tr}</th><th class="heading">{tr}type{/tr}</th></tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$actionlogConf}
<tr><td class="{cycle advance=false}"><input type="checkbox" name="{$actionlogConf[ix].code}" {if $actionlogConf[ix].status eq 'y' or $actionlogConf[ix].status eq 'v'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="view_{$actionlogConf[ix].code}" {if $actionlogConf[ix].status eq 'v'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}">{tr}{$actionlogConf[ix].action}{/tr}</td>
<td class="{cycle}">{tr}{$actionlogConf[ix].objectType}{/tr}</td></tr>
{/section}
<tr><td colspan="3" class="button"><input type="submit" name="setConf" value="{tr}Set{/tr}" /></td></tr>
</table>
<div class="rbox">{tr}Wiki page actions except viewed will always be recorded but can be not reported{/tr}</div>
</form>

<a name="Report" />
<h2>{tr}Report{/tr}</h2>
<form method="post" action="tiki-admin_actionlog.php#Report">
<h3>{tr}Filter{/tr}</h3>
<table class="smallnormal">
<tr>
<td>{tr}User:{/tr}</td>
<td><select multiple="multiple" size="5" name="selectedUsers[]">
<option value="">&nbsp;</option>
{foreach  key=ix item=user from=$users}
<option value="{$user|escape}" {if $selectedUsers[$ix] eq 'y'}selected="selected"{/if}>{$user|escape}</option>
{/foreach}
</select>
</td>
<td>{tr}Start date:{/tr}</td>
<td>{html_select_date time=$startDate prefix="startDate_" end_year="-10" field_order=DMY}</td>
</tr>
<tr>
<td>{tr}Group:{/tr}</td>
<td><select multiple="multiple" size="5" name="selectedGroups[]">
<option value="">&nbsp;</option>
{section name=ix loop=$groups}
<option value="{$groups[ix]|escape}" {if $selectedGroups[ix] eq 'y'}selected="selected"{/if}>{$groups[ix]|escape}</option>
{/section}
</select>
</td>
<td>{tr}End date:{/tr}</td>
<td>{html_select_date time=$endDate prefix="endDate_" end_year="-10" field_order=DMY}</td>
</tr>
<tr>
<td>{tr}Category:{/tr}</td>
<td><select name="categId">
<option value="" {if $reportCateg eq  '' or reportCateg eq 0}selected="selected"{/if}>* {tr}All{/tr} *</option>
 {section name=ix loop=$categories}
<option value="{$categories[ix].categId|escape}" {if $reportCateg eq  $categories[ix].name}selected="selected"{/if}>{$categories[ix].name|escape}</option>
{/section}
</select>
</td>
<td>{tr}bytes{/tr}<input type="radio" name="unit" value="bytes"{if $unit ne 'kb'} checked="checked"{/if}> {tr}kb{/tr}<input type="radio" name="unit" value="kb"{if $unit eq 'kb'} checked="checked"{/if}></td></tr>
<tr><td colspan="4" class="button"><input type="submit" name="list" value="{tr}Report{/tr}" /><br />
<input type="submit" name="export" value="{tr}Export{/tr}" /></td></tr>
</table>
</form>

{if $actionlogs}<a href="#Statistic" class="buttom">See Statictics</a>{/if}

<a name="List" />
<h3>{tr}List{/tr}
{if $selectedUsers}&nbsp;&mdash;&nbsp;{tr}User:{/tr}{foreach  key=ix item=user from=$users}{if $selectedUsers[$ix] eq 'y'} {$user|escape}{/if}{/foreach}{/if}
{if $selectedGroups}&nbsp;&mdash;&nbsp;{tr}Group:{/tr}{foreach  key=ix item=group from=$groups}{if $selectedGroups[$ix] eq 'y'} {$group|escape}{/if}{/foreach}{/if}
{if $reportCategory}&nbsp;&mdash;&nbsp;{tr}Category:{/tr} {$reportCateg}{/if}
</h3>
{if $actionlogs}
<table class="smallnormal">
<tr>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=user_{if $sort_mode eq 'user_desc'}asc{else}desc{/if}{$url}">{tr}user{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=lastModif_{if $sort_mode eq 'lastModif_desc'}asc{else}desc{/if}{$url}">{tr}date{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=action_{if $sort_mode eq 'action_desc'}asc{else}desc{/if}{$url}">{tr}action{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=objectType_{if $sort_mode eq 'objectType_desc'}asc{else}desc{/if}{$url}">{tr}type{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=object_{if $sort_mode eq 'object_desc'}asc{else}desc{/if}{$url}">{tr}object{/tr}</a></th>
{if !$reportCateg and $showCateg eq 'y'}<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=categName_{if $sort_mode eq 'categName_desc'}asc{else}desc{/if}{$url}">{tr}category{/tr}</a></th>{/if}
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=add_{if $sort_mode eq 'add_desc'}asc{else}desc{/if}{$url}">+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?sort_mode=del_{if $sort_mode eq 'del_desc'}asc{else}desc{/if}{$url}">-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a></th>
{if $feature_contribution eq 'y'}<th class="heading">{tr}contribution{/tr}</th>{/if}
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$actionlogs}
<tr>
<td class="{cycle advance=false}">{if $actionlogs[ix].user}{$actionlogs[ix].user}{else}{tr}Anonymous{/tr}{/if}</td>
<td class="{cycle advance=false}">{$actionlogs[ix].lastModif|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$actionlogs[ix].action}</td>
<td class="{cycle advance=false}">{$actionlogs[ix].objectType}</td>
<td class="{cycle advance=false}">{if $actionlogs[ix].link}<a href="{$actionlogs[ix].link}" title="{tr}view{/tr}">{$actionlogs[ix].object|escape}</a>{else}{$actionlogs[ix].object|escape}{/if}</td>
{if !$reportCateg and $showCateg eq 'y'}<td class="{cycle advance=false}">{$actionlogs[ix].categName|escape}</td>{/if}
<td class="{cycle advance=false}{if $actionlogs[ix].add} diffadded{/if}">{if $actionlogs[ix].add or $actionlogs[ix].add eq '0'}{$actionlogs[ix].add}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false}{if $actionlogs[ix].del} diffdeleted{/if}">{if $actionlogs[ix].del or $actionlogs[ix].del eq '0'}{$actionlogs[ix].del}{else}&nbsp;{/if}</td>
{if $feature_contribution eq 'y'}
<td class="{cycle advance=false}">
{section name=iy loop=$actionlogs[ix].contributions}
{if !$smarty.section.iy.first}<br />{/if}
{$actionlogs[ix].contributions[iy].name}
{/section}
</td>
{/if}
<!-- {cycle} -->
</tr>
{/section}
</table>
{/if}

<a name="Statistic" />
<h3>{tr}Statistic{/tr}
{if $selectedUsers}&nbsp;&mdash;&nbsp;{tr}User:{/tr}{foreach  key=ix item=user from=$users}{if $selectedUsers[$ix] eq 'y'} {$user|escape}{/if}{/foreach}{/if}
{if $selectedGroups}&nbsp;&mdash;&nbsp;{tr}Group:{/tr}{foreach  key=ix item=group from=$groups}{if $selectedGroups[$ix] eq 'y'} {$group|escape}{/if}{/foreach}{/if}
{if $reportCategory}&nbsp;&mdash;&nbsp;{tr}Category:{/tr} {$reportCateg}{/if}
</h3>

{if $showLogin eq 'y' and $logTimes|@count ne 0}
<table class="smallnormal">
<tr>
<th class="heading">{tr}user{/tr}</th>
<th class="heading">{tr}connection time{/tr}</th>
<th class="heading">{tr}connection seconds{/tr}</th>
<th class="heading">{tr}login{/tr}</th>
</tr>
{foreach key=user item=time from=$logTimes}
<tr>
<td class="{cycle advance=false}">{$user}</td>
<td class="{cycle advance=false}">{$time.days} {tr}days{/tr} {$time.hours} {tr}hours{/tr} {$time.mins} {tr}mns{/tr}</td>
<td class="{cycle advance=false}">{$time.time}</td>
<td class="{cycle}">{$time.nbLogins}</td>
</tr>
{/foreach}
</table>
{/if}

{if $showCateg eq 'y' and $volCateg|@count ne 0}
<table class="smallnormal">
<tr>
<th class="heading">{tr}category{/tr}</th>
{foreach  item=type from=$typeVol}
<th class="heading">{$type} (+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th><th class="heading">{$type} (-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th><th class="heading">{$type} ({if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
{/foreach}
</tr>
{foreach key=categId item=vol from=$volCateg}
<tr>
<td class="{cycle advance=false}">{$vol.category}</td>
{foreach item=type from=$typeVol} {* math equation="round(a/b)" a=$vol[$type].del b=1024 *}
<td class="{cycle advance=false}{if $vol[$type].add} diffadded{/if}">{if $vol[$type].add}{$vol[$type].add}{else}0{/if}</td>
<td class="{cycle advance=false}{if $vol[$type].del} diffdeleted{/if}">{if $vol[$type].del}{$vol[$type].del}{else}0{/if}</td>
<td class="{cycle advance=false}{if $vol[$type].dif > 0} diffadded{elseif $vol[$type].dif < 0} diffdeleted{/if}">{if $vol[$type].dif}{$vol[$type].dif}{else}0{/if}</td>
{/foreach}
<!-- {cycle} -->
</tr>
{/foreach}
</table>
{/if}

{if $showCateg eq 'y' and $volUserCateg|@count ne 0}
<table class="smallnormal">
<tr>
<th class="heading">{tr}category{/tr}</th>
<th class="heading">{tr}user{/tr}</th>
{foreach  item=type from=$typeVol}
<th class="heading">{$type} (+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th><th class="heading">{$type} (-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th><th class="heading">{$type} ({if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
{/foreach}
</tr>
{foreach key=categId item=vol from=$volUserCateg}
<tr>
<td class="{cycle advance=false}">{$vol.category}</td>
<td class="{cycle advance=false}">{$vol.user}</td>
{foreach item=type from=$typeVol} {* math equation="round(a/b)" a=$vol[$type].del b=1024 *}
<td class="{cycle advance=false}{if $vol[$type].add} diffadded{/if}">{if $vol[$type].add}{$vol[$type].add}{else}0{/if}</td>
<td class="{cycle advance=false}{if $vol[$type].del} diffdeleted{/if}">{if $vol[$type].del}{$vol[$type].del}{else}0{/if}</td>
<td class="{cycle advance=false}{if $vol[$type].dif > 0} diffadded{elseif $vol[$type].dif < 0} diffdeleted{/if}">{if $vol[$type].dif}{$vol[$type].dif}{else}0{/if}</td>
{/foreach}
<!-- {cycle} -->
</tr>
{/foreach}
</table>
{/if}

{if $statUser|@count ne 0}
<table class="normal">
<tr>
<th class="heading">{tr}user{/tr}</th>
{foreach key=title item=nb from=$statUser[0]}
{if $title ne 'user'}<th class="heading">{$title|replace:"/":" "}</th>{/if}
{/foreach}
</tr>
{cycle values="even,odd" print=false}
{foreach item=stat from=$statUser}
<tr>
<td class="{cycle advance=false}">{$stat.user}</td>
{foreach key=a item=nb from=$stat}
{if $a ne 'user'}<td class="{cycle advance=false}">{$nb}</td>{/if}
{/foreach}
<!-- {cycle} -->
</tr>
{/foreach}
</table>
{/if}

{if $showCateg eq 'y'}
<table class="normal">
<tr>
<th class="heading">{tr}category{/tr}</th>
{foreach  key=title item=nb from=$statUser[0]}
{if $title ne 'user'}<th class="heading">{$title|replace:"/":" "}</th>{/if}
{/foreach}
</tr>
{foreach key=categId item=stat from=$statCateg}
<tr>
<td class="{cycle advance=false}">{$stat.category}</td>
{foreach  key=a item=nb from=$statCateg[$categId]}
{if $a ne 'category'}<td class="{cycle advance=false}">{$nb}</td>{/if}
{/foreach}
<!-- {cycle} -->
</tr>
{/foreach}
</table>
{/if}

{if $showCateg eq 'y' && $statUserCateg|@count ne 0}
<table class="normal">
<tr>
<th class="heading">{tr}category{/tr}</th>
<th class="heading">{tr}user{/tr}</th>
{foreach  key=title item=nb from=$statUser[0]}
{if $title ne 'user'}<th class="heading">{$title|replace:"/":" "}</th>{/if}
{/foreach}
</tr>
{foreach key=categUser item=stat from=$statUserCateg}
<tr>
<td class="{cycle advance=false}">{$stat.category}</td>
<td class="{cycle advance=false}">{$stat.user}</td>
{foreach key=a item=nb from=$stat}
{if $a ne 'category' and $a ne 'user'}<td class="{cycle advance=false}">{$nb}</td>{/if}
{/foreach}
<!-- {cycle} -->
</tr>
{/foreach}
</table>
{/if}

<a name="#csv"></a>
{if $csv}
<a name="#csv"></a>
<div class="cbox">{$csv}</div>
{/if}
