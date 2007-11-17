{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_actionlog.tpl,v 1.46.2.6 2007-11-17 13:13:44 xavidp Exp $ *}

<h1><a href="tiki-admin_actionlog.php" class="pagetitle">{tr}Action Log{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Action Log" target="tikihelp" class="tikihelp" title="{tr}Action Log{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_actionlog.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Groups Template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' />
</a>
{/if}
</h1>

{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2" print=false advance=false}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Report{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Setting{/tr}</a></span>
</div>
{/if}

{cycle name=content values="1,2" print=false advance=false reset=true}

{* -------------------------------------------------- tab with report --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<a name="Report" />
<form method="get" action="tiki-admin_actionlog.php#Report">
{* a get method to be able when you right click on the image to save it and not to save the tiki-admin_aqctionlog.php without param *}
<h2>{tr}Filter{/tr}</h2>
{if empty($nbViewedConfs)}
<div class="simplebox highlight">{tr}Please select some actions to view.{/tr}</div>
{else}
<table class="smallnormal">
<tr class="formcolor">
<td>{tr}Start date:{/tr}</td>
<td>{html_select_date time=$startDate prefix="startDate_" end_year="-10" field_order=$prefs.display_field_order}</td>
</tr><tr class="formcolor">
<td>{tr}End date:{/tr}</td>
<td>{html_select_date time=$endDate prefix="endDate_" end_year="-10" field_order=$prefs.display_field_order}</td>
</tr>
{if $tiki_p_admin eq 'y'}
<tr class="formcolor">
<td>{tr}User:{/tr}</td>
<td><select multiple="multiple" size="5" name="selectedUsers[]">
<option value="">&nbsp;</option>
{foreach  key=ix item=auser from=$users}
<option value="{$auser|escape}" {if $selectedUsers[$ix] eq 'y'}selected="selected"{/if}>{$auser|escape}</option>
{/foreach}
</select>
</td>
</tr>
{else}
<input type="hidden" name="selectedUsers[]" value="{$auser|escape}">
{/if}
{if $groups|@count >= 1}
<tr class="formcolor">
<td>{tr}Group:{/tr}</td>
<td><select multiple="multiple" size="{if $groups|@count < 5}{math equation=x+y x=$groups|@count y=1}{else}5{/if}" name="selectedGroups[]">
<option value="">&nbsp;</option>
{foreach from=$groups key=ix item=group}
<option value="{$group|escape}" {if $selectedGroups[$group] eq 'y'}selected="selected"{/if}>{$group}</option>
{/foreach}
</select>
</td>
</tr>
{/if}
<tr class="formcolor">
<td>{tr}Category:{/tr}</td>
<td><select name="categId">
<option value="" {if $reportCateg eq  '' or reportCateg eq 0}selected="selected"{/if}>* {tr}All{/tr} *</option>
 {section name=ix loop=$categories}
<option value="{$categories[ix].categId|escape}" {if $reportCateg eq  $categories[ix].name}selected="selected"{/if}>{$categories[ix].name|escape}</option>
{/section}
</select>
</td></tr><tr class="formcolor">
<td></td><td>{tr}bytes{/tr}<input type="radio" name="unit" value="bytes"{if $unit ne 'kb'} checked="checked"{/if} /> {tr}kb{/tr}<input type="radio" name="unit" value="kb"{if $unit eq 'kb'} checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td></td><td>{tr}Week{/tr}<input type="radio" name="contribTime" value="w"{if $contribTime ne 'd'} checked="checked"{/if} /> {tr}Day{/tr}<input type="radio" name="contribTime" value="d"{if $contribTime eq 'd'} checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td colspan="2" class="button"><input type="submit" name="list" value="{tr}Report{/tr}" /></td></tr>
{if $prefs.feature_contribution eq 'y'}
<tr class="formcolor"><td colspan="2" class="button"><input type="submit" name="graph" value="{tr}Graph Contributions{/tr}" />
{if $prefs.feature_jpgraph eq 'y'}
<br />{tr}Group Bar Plot:{/tr}<input type="radio" name="barPlot" value="group" /> {tr}Accumulated Bar Plot:{/tr}<input type="radio" name="barPlot" value="acc" checked="checked" />
<br />{tr}Background color:{/tr} <select name="bgcolor">{foreach item=color from=$bgcolors}<option value="{$color|escape}"{if $defaultBgcolor eq $color} selected="selected"{/if}>{tr}{$color}{/tr}</option>{/foreach}</select> 
{tr}Legend background color:{/tr} <select name="legendBgcolor">{foreach item=color from=$bgcolors}<option value="{$color|escape}"{if $defaultLegendBgcolor eq $color} selected="selected"{/if}>{tr}{$color}{/tr}</option>{/foreach}</select>
<br />{tr}Save graphs to image gallery:{/tr} <select name="galleryId"><option value="" selected="selected" />{section name=idx loop=$galleries}<option  value="{$galleries[idx].galleryId|escape}">{$galleries[idx].name}</option>{/section}</select>
{/if}</td></tr>
{/if}
{if $tiki_p_admin eq 'y'}
<tr class="formcolor"><td colspan="2" class="button"><input type="submit" name="export" value="{tr}Export{/tr}" /></td></tr>
{/if}
</table>
</form>
{/if}

{if $actionlogs}<a href="#Statistic" class="buttom">See Statictics</a>{/if}

<a name="List" />
<h2>{tr}List{/tr}
{if $selectedUsers}&nbsp;&mdash;&nbsp;{tr}User:{/tr}{foreach  key=ix item=auser from=$users}{if $selectedUsers[$ix] eq 'y'} {$auser|escape}{/if}{/foreach}{/if}
{if $selectedGroups}&nbsp;&mdash;&nbsp;{tr}Group:{/tr}{foreach  key=ix item=group from=$groups}{if $selectedGroups[$group] eq 'y'} {$group|escape}{/if}{/foreach}{/if}
{if $reportCategory}&nbsp;&mdash;&nbsp;{tr}Category:{/tr} {$reportCateg}{/if}
</h2>
{if $actionlogs}
<table class="smallnormal">
<tr>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=user_{if $sort_mode eq 'user_desc'}asc{else}desc{/if}{$url}">{tr}User{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=lastModif_{if $sort_mode eq 'lastModif_desc'}asc{else}desc{/if}{$url}">{tr}date{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=action_{if $sort_mode eq 'action_desc'}asc{else}desc{/if}{$url}">{tr}Action{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=objectType_{if $sort_mode eq 'objectType_desc'}asc{else}desc{/if}{$url}">{tr}Type{/tr}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=object_{if $sort_mode eq 'object_desc'}asc{else}desc{/if}{$url}">{tr}Object{/tr}</a></th>
{if !$reportCateg and $showCateg eq 'y'}<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=categName_{if $sort_mode eq 'categName_desc'}asc{else}desc{/if}{$url}">{tr}Category{/tr}</a></th>{/if}
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=add_{if $sort_mode eq 'add_desc'}asc{else}desc{/if}{$url}">+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a></th>
<th class="heading"><a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=del_{if $sort_mode eq 'del_desc'}asc{else}desc{/if}{$url}">-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a></th>
{if $prefs.feature_contribution eq 'y'}<th class="heading">{tr}contribution{/tr}</th>{/if}
{if $prefs.feature_contributor_wiki eq 'y'}<th class="heading">{tr}contributor{/tr}</th>{/if}
{if $prefs.feature_contribution eq 'y' and $tiki_p_admin eq 'y'}<th class="heading">&nbsp;</th>{/if}
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$actionlogs}
<tr>
<td class="{cycle advance=false}">{if $actionlogs[ix].user}{$actionlogs[ix].user}{else}{tr}Anonymous{/tr}{/if}</td>
<td class="{cycle advance=false}">{$actionlogs[ix].lastModif|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{tr}{$actionlogs[ix].action}{/tr}</td>
<td class="{cycle advance=false}">{tr}{$actionlogs[ix].objectType}{/tr}</td>
<td class="{cycle advance=false}">{if $actionlogs[ix].link}<a href="{$actionlogs[ix].link}" title="{tr}View{/tr}">{$actionlogs[ix].object|escape}</a>{else}{$actionlogs[ix].object|escape}{/if}</td>
{if !$reportCateg and $showCateg eq 'y'}
<td class="{cycle advance=false}">{$actionlogs[ix].categName|escape}</td>{/if}
<td class="{cycle advance=false}{if $actionlogs[ix].add} diffadded{/if}">{if $actionlogs[ix].add or $actionlogs[ix].add eq '0'}{$actionlogs[ix].add}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false}{if $actionlogs[ix].del} diffdeleted{/if}">{if $actionlogs[ix].del or $actionlogs[ix].del eq '0'}{$actionlogs[ix].del}{else}&nbsp;{/if}</td>
{if $prefs.feature_contribution eq 'y'}
<td class="{cycle advance=false}">
{section name=iy loop=$actionlogs[ix].contributions}
{if !$smarty.section.iy.first}, {/if}
{$actionlogs[ix].contributions[iy].name}
{/section}
</td>
{if $prefs.feature_contributor_wiki eq 'y'}
<td class="{cycle advance=false}">
{section name=iy loop=$actionlogs[ix].contributors}
{if !$smarty.section.iy.first}, {/if}
{$actionlogs[ix].contributors[iy].login}
{/section}
</td>
{/if}
{if $prefs.feature_contribution eq 'y' and $tiki_p_admin eq 'y'}<td class="{cycle advance=false}">{if $actionlogs[ix].actionId}<a class="link" href="tiki-admin_actionlog.php?actionId={$actionlogs[ix].actionId}&amp;startDate={$startDate}&amp;endDate={$endDate}#action" title="{tr}Edit Contribution{/tr}"><img src="pics/icons/page_edit.png" alt="{tr}Edit{/tr}" width="16" height="16" border="0"></a>{else}&nbsp;{/if}</td>{/if}
{/if}
<!-- {cycle} -->
</tr>
{/section}
</table>
{/if}

{if $action}
<a name="action">
<h2>{tr}Edit Action{/tr}</h2>
<form method="post" action="tiki-admin_actionlog.php">
<input type="hidden" name="actionId" value="{$action.actionId}" />
<input type="hidden" name="list" value="y" />
{if $selectedUsers}<input type="hidden" name="selectedUsers" value="{$selectedUsers}" />{/if}
{if $selectedGroups}<input type="hidden" name="selectedGroups" value="{$selectedGroups}" />{/if}
{if $startDate}<input type="hidden" name="startDate" value="{$startDate}" />{/if}
{if $endDate}<input type="hidden" name="endDate" value="{$endDate}" />{/if}
{$action.action} / {$action.objectType} / {$action.object} 
<table class="normal">
{include file="contribution.tpl"}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="saveAction" value="{tr}Save Action{/tr}" /></td></tr>
</table>
</form>
{/if}

<a name="Statistic" />
<h2>{tr}Statistic{/tr}
{if $selectedUsers}&nbsp;&mdash;&nbsp;{tr}User:{/tr}{foreach  key=ix item=auser from=$users}{if $selectedUsers[$ix] eq 'y'} {$auser|escape}{/if}{/foreach}{/if}
{if $selectedGroups}&nbsp;&mdash;&nbsp;{tr}Group:{/tr}{foreach  key=ix item=group from=$groups}{if $selectedGroups[$group] eq 'y'} {$group|escape}{/if}{/foreach}{/if}
{if $reportCategory}&nbsp;&mdash;&nbsp;{tr}Category:{/tr} {$reportCateg}{/if}
</h2>
<i>{tr}Volumes are equally distributed on each contributors/author{/tr}</i>

{if $showLogin eq 'y' and $logTimes|@count ne 0}
<table class="smallnormal">
<caption>{tr}Login{/tr}</caption>
<tr>
{if $selectedUsers|@count gt 1}<th class="heading">{tr}User{/tr}</th>{/if}
<th class="heading">{tr}connection time{/tr}</th>
<th class="heading">{tr}connection seconds{/tr}</th>
<th class="heading">{tr}Login{/tr}</th>
</tr>
{foreach key=auser item=time from=$logTimes}
<tr>
{if $selectedUsers|@count gt 1}<td class="{cycle advance=false}">{$auser}</td>{/if}
<td class="{cycle advance=false}">{$time.days} {tr}days{/tr} {$time.hours} {tr}hours{/tr} {$time.mins} {tr}mns{/tr}</td>
<td class="{cycle advance=false}">{$time.time}</td>
<td class="{cycle}">{$time.nbLogins}</td>
</tr>
{/foreach}
</table>
{/if}

{if $showCateg eq 'y' and $volCateg|@count ne 0 and tiki_p_admin eq 'y'}
<table class="smallnormal">
<caption>{tr}Volumn per category{/tr}</caption>
<tr>
<th class="heading">{tr}Category{/tr}</th>
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
<caption>{tr}Volumn per category and per user{/tr}</caption>
<tr>
<th class="heading">{tr}Category{/tr}</th>
<th class="heading">{tr}User{/tr}</th>
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

{if $userActions|@count ne 0}
<table class="normal">
<caption>{tr}Number of actions per user{/tr}</caption>
<tr>
<th class="heading">{tr}User{/tr}</th>
{foreach key=title item=nb from=$userActions[0]}
{if $title ne 'user'}<th class="heading">{$title|replace:"/":" "}</th>{/if}
{/foreach}
</tr>
{cycle values="even,odd" print=false}
{foreach item=stat from=$userActions}
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

{if $showCateg eq 'y' and tiki_p_admin eq 'y'}
<table class="normal">
<caption>{tr}Number of actions per category{/tr}</caption>
<tr>
<th class="heading">{tr}Category{/tr}</th>
{foreach  key=title item=nb from=$userActions[0]}
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
<caption>{tr}Number of actions per category and per user{/tr}</caption>
<tr>
<th class="heading">{tr}Category{/tr}</th>
<th class="heading">{tr}User{/tr}</th>
{foreach  key=title item=nb from=$userActions[0]}
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

{if $prefs.feature_contribution eq 'y' && isset($groupContributions) && $groupContributions|@count >= 1}
<table>
<caption>{if $selectedUsers}{tr}Volumn per the users'group and per contribution{/tr}{else}{tr}Volumn per group and per contribution{/tr}{/if}</caption>
<tr><th class="heading">{tr}Group{/tr}</th><th class="heading">{tr}Contribution{/tr}</th><th class="heading">+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th><th class="heading">-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th></tr>
{foreach from=$groupContributions key=group item=contributions}
{foreach from=$contributions key=contribution item=stat}
<tr><td class="{cycle advance=false}">{$group}</td><td class="{cycle advance=false}">{$contribution}</td><td class="{cycle advance=false}">{$stat.add}</td><td class="{cycle advance=false}">{$stat.del}</td></tr><!-- {cycle} -->
{/foreach}
{/foreach}
</table>
{/if}

{if $prefs.feature_contribution eq 'y' && isset($userContributions) && $userContributions|@count >= 1}
<table>
<caption>{tr}Volumn per user and per contribution{/tr}</caption>
<tr><th class="heading">{tr}User{/tr}</th><th class="heading">{tr}Contribution{/tr}</th><th class="heading">+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th><th class="heading">-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th></tr>
{foreach from=$userContributions key=user item=contributions}
{foreach from=$contributions key=contribution item=stat}
<tr><td class="{cycle advance=false}">{$user}</td><td class="{cycle advance=false}">{$stat.name}</td><td class="{cycle advance=false}">{$stat.stat.add}</td><td class="{cycle advance=false}">{$stat.stat.del}</td></tr><!-- {cycle} -->
{/foreach}
{/foreach}
</table>
{/if}

{if $prefs.feature_contribution eq 'y' && isset($contributionStat)}
<table>
<caption>{if $selectedUsers}{tr}Volumn per users'contribution and time{/tr}{else}{tr}Volumn per contribution and time{/tr}{/if}</caption>
<tr><th class="heading">{tr}Contribution{/tr}</th>
<th class="heading" colspan="{$contributionNbCols}">{if $contribTime eq 'd'}{tr}Days{/tr}{else}{tr}Weeks{/tr}{/if}</th></tr>
<tr><td class="heading"></td>
{section name=foo start=0 loop=`$contributionNbCols`}
<td class="heading">{$smarty.section.foo.index+1}</td>
{/section}
</tr>
{foreach from=$contributionStat key=contributionId item=contribution}
<tr><td class="{cycle advance=false}">{$contribution.name}</td>
{foreach from=$contribution.stat item=stat}
<td class="{cycle advance=false}">
{if !empty($stat.add)}<span class="diffadded">{$stat.add}</span>{/if}<br />
{if !empty($stat.del)}<span class="diffdeleted">{$stat.del}</span>{/if}<br />
{if !empty($stat.del) || !empty($stat.add)}{math equation=x-y x=$stat.add y=$stat.del}{/if}<br />
</td>
{/foreach}
<!--{cycle}--> 
</tr>
{/foreach}
</table>
{/if}

<a name="csv"></a>
{if $csv}
<div class="cbox">{$csv}</div>
{/if}

</div>{* tab *}

{* -------------------------------------------------- tab with setting --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<a name="Setting" />
<h2>{tr}Setting{/tr}</h2>
<form method="post" action="tiki-admin_actionlog.php">
{if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
<table class="smallnormal">
<tr>
{if $tiki_p_admin eq 'y'}
<th class="heading">{tr}recorded{/tr}</th>
{/if}
<th class="heading">{tr}viewed{/tr}</th>
<th class="heading">{tr}Action{/tr}</th><th class="heading">{tr}Type{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$actionlogConf}
<tr>
{if $tiki_p_admin eq 'y'}
<td class="{cycle advance=false}"><input type="checkbox" name="{$actionlogConf[ix].code}" {if $actionlogConf[ix].status eq 'y' or $actionlogConf[ix].status eq 'v'}checked="checked"{/if} /></td>
{/if}
{if $tiki_p_admin eq 'y' or $actionlogConf[ix].status eq 'y' or $actionlogConf[ix].status eq 'v'}
<td class="{cycle advance=false}"><input type="checkbox" name="v_{$actionlogConf[ix].code}" {if $actionlogConf[ix].status eq 'v'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}">{tr}{$actionlogConf[ix].action}{/tr}</td>
<td class="{cycle}">{tr}{$actionlogConf[ix].objectType}{/tr}</td>
{/if}
</tr>
{/section}
<tr><td colspan="4" class="button"><input type="submit" name="save" value="{tr}Set{/tr}" /></td></tr>
</table>
<div class="rbox">{tr}Wiki page actions except viewed will always be recorded but can be not reported{/tr}</div>
</form>
</div>{*tab*}
