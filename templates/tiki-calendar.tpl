{popup_init src="lib/overlib.js"}
<a class="pagetitle" href="tiki-calendar.php?view={$view}">{tr}Calendar{/tr}</a>
{if $tiki_p_admin eq 'y'}
<span class="mini"><a href="tiki-admin_calendars.php" class="link">{tr}admin{/tr}</a></span>
{/if}
<br/><br/>
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr><td class="heading">
<div id="morecal" class="heading" style="padding-top:4px;">
<a href="javascript:toggle('refreshopened');toggle('refreshclosed');toggle('morecal');toggle('lesscal');" class="tableheading">{tr}more{/tr}</a>
</div>
<div id="lesscal" class="heading" style="padding-top:4px;display:none;">
<a href="javascript:toggle('refreshopened');toggle('refreshclosed');toggle('morecal');toggle('lesscal');" class="tableheading">{tr}less{/tr}</a>
</div>
</td>
<td colspan="7">
<div id="refreshopened" style="display:none;">
<form class="box" method="get" action="tiki-calendar.php">
<table border="0" width="100%" style="border:1px dashed #666666;border-left:0;">
<tr>
<td>
<input type="submit" name="submit" value="{tr}Refresh{/tr}"><br/>
</td>
<td>
{tr}Group Calendars{/tr} :
</td>
<td>
{section name=lc loop=$listcals}
<div> 
<input type="checkbox" name="calIds[]" value="{$listcals[lc].calendarId}" id="cal{$listcals[lc].name}" {if $thiscal[lc]}checked="checked"{/if}/>
<a href="#" class="linkmenu" {popup text="$listcals[lc].description " hauto=1 vauto=1 background="decebe" sticky=1}>{$listcals[lc].name} ({$listcals[lc].groupname})</a>
</div>
{/section}
</td>
<td>
{tr}Tools Calendars{/tr} :
</td>
<td>
{if $feature_wiki eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="wiki" id="tikical_wiki" {if $tikical.wiki}checked="checked"{/if}/>
{tr}Wiki{/tr}</div>
{/if}
{if $feature_galleries eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="gal" id="tikical_gal" {if $tikical.gal}checked="checked"{/if}/>
{tr}Image Gallery{/tr}</div>
{/if}
{if $feature_articles eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="art" id="tikical_art" {if $tikical.art}checked="checked"{/if}/>
{tr}Articles{/tr}</div>
{/if}
{if $feature_blogs eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="blog" id="tikical_blog" {if $tikical.blog}checked="checked"{/if}/>
{tr}Blogs{/tr}</div>
{/if}
{if $feature_forums eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="forum" id="tikical_forum" {if $tikical.forum}checked="checked"{/if}/>
{tr}Forums{/tr}</div>
{/if}
{if $feature_directory eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="dir" id="tikical_dir" {if $tikical.dir}checked="checked"{/if}/>
{tr}Directory{/tr}</div>
{/if}
{if $feature_file_galleries eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="fgal" id="tikical_fgal" {if $tikical.fgal}checked="checked"{/if}/>
{tr}File Gallery{/tr}</div>
{/if}
{if $feature_faqs eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="faq" id="tikical_faq" {if $tikical.faq}checked="checked"{/if}/>
{tr}FAQs{/tr}</div>
{/if}
{if $feature_quizzes eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="quiz" id="tikical_quiz" {if $tikical.quiz}checked="checked"{/if}/>
{tr}Quizzes{/tr}</div>
{/if}
{if $feature_trackers eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="track" id="tikical_track" {if $tikical.track}checked="checked"{/if}/>
{tr}Trackers{/tr}</div>
{/if}
{if $feature_surveys eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="surv" id="tikical_surv" {if $tikical.surv}checked="checked"{/if}/>
{tr}Survey{/tr}</div>
{/if}
{if $feature_newsletters eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="nl" id="tikical_nl" {if $tikical.nl}checked="checked"{/if}/>
{tr}Newsletter{/tr}</div>
{/if}
{if $feature_eph eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="eph" id="tikical_eph" {if $tikical.eph}checked="checked"{/if}/>
{tr}Ephemerides{/tr}</div>
{/if}
{if $feature_charts eq 'y'}
<div>
<input type="checkbox" name="tikicals[]" value="chart" id="tikical_chart" {if $tikical.chart}checked="checked"{/if}/>
{tr}Charts{/tr}</div>
{/if}

</td>
</form>
</tr></table>
</div>
<div id="refreshclosed">
<table border="0" width="100%" style="border:1px dashed #666666;border-left:0;">
<tr><td>
<div class="mini">
{if $displayedcals}
<b>{tr}Group Calendars{/tr} :</b>
{section name=dc loop=$displayedcals}
{$displayedcals[dc].name}, 
{/section}
{/if}
{if $displayedtikicals}
<b>{tr}Tiki Calendars{/tr}:</b>
{section name=dc loop=$displayedtikicals}
<span class="Cal{$displayedtikicals[dc]}">={$displayedtikicals[dc]}</span>, 
{/section}
{/if}
</div>
</td></tr></table>
</div>
<div align="center" style="font-size:10px;">
<span style="float:right;">
{tr}today{tr}: <a href="tiki-calendar.php?todate={$now}" class="linkmodule" title="{$now|tiki_long_date}">{$now|tiki_long_date}</a>
</span>
<span style="float:left;">
<a href="tiki-calendar.php?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">-1m</a>
<a href="tiki-calendar.php?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">-7d</a>
<a href="tiki-calendar.php?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">-1d</a> 
<b>{$focusdate|tiki_long_date}</b>
<a href="tiki-calendar.php?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}+1d{/tr}</a>
<a href="tiki-calendar.php?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}+7d{/tr}</a>
<a href="tiki-calendar.php?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}+1m{/tr}</a>
</span>
{tr}browse by{/tr}
<a href="tiki-calendar.php?viewmode=day" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}">{tr}day{/tr}</a>
<a href="tiki-calendar.php?viewmode=week" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}">{tr}week{/tr}</a>
<a href="tiki-calendar.php?viewmode=month" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}">{tr}month{/tr}</a>
</div>
</td></tr>

{if $viewmode eq 'day'}

{else}
<tr><td></td>
{section name=dn loop=$daysnames}
<td class="heading">{$daysnames[dn]}</td>
{/section}
</tr>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}
<tr><td width="20" class="heading">{$weeks[w]}</td>
{section name=d loop=$weekdays}
{if $cell[w][d].day|date_format:"%m" eq $focusmonth}
{cycle values="odd,even" print=false advance=false}
{else}
{cycle values="odddark" print=false advance=false}
{/if}
<td class="{cycle}">
<div align="center" class="menu" style="font-size:7px;background-color:{if $cell[w][d].day eq $focusdate}#f3e498{else}none{/if};">
<span style="float:left;">
<a href="tiki-calendar.php?todate={$cell[w][d].day}" class="linkmenu" style="color:#666666;font-size:7px;">{$cell[w][d].day|date_format:"%d/%m"}</a>
</span>
<span style="float:right;margin-right:3px;">
<a href="tiki-calendar.php?todate={$cell[w][d].day}&editmode=add" class="linkmenu" style="color:#666666;font-size:7px;">{tr}+{/tr}</a>
</span>
.<br/>
</div>
{section name=items loop=$cell[w][d].items}
<div class="Cal{$cell[w][d].items[items].type}" id="{$cell[w][d].items[items].type}">
<a href="{$cell[w][d].items[items].url}" {popup text="<b>$cell[w][d].items[items].descriptionhead</b><br>$cell[w][d].items[items].descriptionbody"} 
class="linkmenu">{$cell[w][d].items[items].name|truncate:22:".."}</a><br/>
</div>
{/section}
</td>
{/section}
</tr>
{/section}
</tr>
{/if}
</table>

{if $editmode}
<form enctype="multipart/form-data" method="post" action="tiki-calendar.php" id="editcalitem" name="f" style="display:block;">
<input type="hidden" name="editmode" value="1">
<input type="hidden" name="calitemId" value="{$calitemId}">
<table class="normal" style="width:100%">
<tr><td class="formcolor">{tr}Calendrier{/tr}</td><td class="formcolor">
<select name="calendarId">
{section name=lc loop=$listcals}
{if $listcals[lc]}
<option value="{$listcals[lc].calendarId}" {if $calendarId eq $listcals[lc].calendarId}selected="selected"{/if} onchange="document.forms[f].submit();">{$listcals[lc].name}</option>
{/if}
{/section}
</select>
<input type="submit" name="refresh" value="{tr}refresh{/tr}" /><br/>
{tr}If you change the calendar selection, please refresh to get the appropriated list in Category, Location and people.{/tr}<br/>
</td></tr>
<tr><td class="form">{tr}Category{/tr}</td><td class="form">
<select name="categoryId">
{section name=t loop=$listcat}
{if $listcat[t]}
<option value="{$listcat[t].calcatId}" {if $categoryId eq $listcat[t].calcatId}selected="selected"{/if}>{$listcat[t].name}</option>
{/if}
{/section}
</select>
{tr}or create a new category{/tr} 
<input type="text" name="newcat" value="">
</td></tr>
<tr><td class="form">{tr}Location{/tr}</td><td class="form">
<select name="locationId">
{section name=l loop=$listloc}
{if $listloc[l]}
<option value="{$listloc[l].callocId}" {if $locationId eq $listloc[l].callocId}selected="selected"{/if}>{$listloc[l].name}</option>
{/if}
{/section}
</select>
{tr}or create a new location{/tr} 
<input type="text" name="newloc" value="">
</td></tr>

<tr><td class="form">{tr}Organized by{/tr}</td><td class="form">
<input type="text" name="organizer" value="" id="organizers">
{tr}comma separated of usernames from the list{/tr}:
<select name="organizer" onchange="javascript:document.getElementById('organizers').value+=this.options[this.selectedIndex].value+',';">
<option value="">{tr}choose{/tr}</option>
{section name=lp loop=$listpeople}
<option value="{$listpeople[lp]}">{$listpeople[lp]}</option>
{/section}
</select>
</td></tr>

<tr><td class="form">{tr}Participants{/tr}</td><td class="form">
<input type="text" name="participants" value="" id="participants">
{tr}comma separated of usernames:role from the list{/tr}:
<select name="roles" id="roles">
<option value="0">{tr}0: Chair{/tr}</option>
<option value="1">{tr}1: Required{/tr}</option>
<option value="2">{tr}2: Optionnal{/tr}</option>
<option value="3">{tr}3: None{/tr}</option>
</select>
<select name="participants" onchange="javascript:document.getElementById('participants').value+=this.options[this.selectedIndex].value+":"+document.getElementById('roles').options[document.getElementById('roles').selectedIndex].value+',';">
<option value="">{tr}choose{/tr}</option>
{section name=lp loop=$listpeople}
<option value="{$listpeople[lp]}">{$listpeople[lp]}</option>
{/section}
</select>
</td></tr>

<tr><td class="formcolor">{tr}Start{/tr}</td><td class="formcolor">
{html_select_date time=$start prefix="start_" field_order=DMY}
{html_select_time minute_interval=10 time=$start prefix="starth_" display_seconds=false use_24_hours=true}
</td></tr>

<tr><td class="formcolor">{tr}End{/tr}</td><td class="formcolor">
{html_select_date time=$end prefix="end_" field_order=DMY}
{html_select_time minute_interval=10 time=$end prefix="endh_" display_seconds=false use_24_hours=true}
</td></tr>

<tr><td class="formcolor">{tr}Name{/tr}</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}</td><td class="formcolor">
<textarea class="wikiedit" id="body" name="body" rows="8" cols="80" id='subbody' wrap="virtual">{$body}</textarea>
</td></tr>

<tr><td class="formcolor">{tr}Url{/tr}</td><td class="formcolor"><input type="text" name="url" value="{$url}" /></td></tr>

<tr><td class="formcolor">{tr}Priority{/tr}</td><td class="formcolor">
<select name="priority">
<option value="1" {if $priority eq 1}selected="selected"{/if}>1</option>
<option value="2" {if $priority eq 2}selected="selected"{/if}>2</option>
<option value="3" {if $priority eq 3}selected="selected"{/if}>3</option>
<option value="4" {if $priority eq 4}selected="selected"{/if}>4</option>
<option value="5" {if $priority eq 5}selected="selected"{/if}>5</option>
<option value="6" {if $priority eq 6}selected="selected"{/if}>6</option>
<option value="7" {if $priority eq 7}selected="selected"{/if}>7</option>
<option value="8" {if $priority eq 8}selected="selected"{/if}>8</option>
<option value="9" {if $priority eq 9}selected="selected"{/if}>9</option>
</select>
</td></tr>

<tr><td class="formcolor">{tr}Status{/tr}</td><td class="formcolor">
<select name="status">
<option value="0" {if $status eq 0}selected="selected"{/if}>{tr}Tentative{/tr}</option>
<option value="1" {if $status eq 1}selected="selected"{/if}>{tr}Confirmed{/tr}</option>
<option value="2" {if $status eq 2}selected="selected"{/if}>{tr}Cancelled{/tr}</option>
</select>
</td></tr>

<tr><td class="formcolor">{tr}Language{/tr}</td><td class="formcolor">
<select name="lang">
{section name=ix loop=$languages}
<option value="{$languages[ix]}" {if $lang eq $languages[ix]}selected="selected"{/if}>{$languages[ix]}</option>
{/section}
</select>
</td></tr>

<tr><td class="formcolor"></td><td class="formcolor">
<input type="submit" name="prev" value="{tr}preview{/tr}" />
<input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
{/if}
