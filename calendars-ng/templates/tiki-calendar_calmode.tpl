<div style="position:relative;">
{if $calendarViewMode eq 'month'}
  <div id="month_title" style="position:relative;height:36px;width:100%;text-align:center;"><strong>{$currMonth|tiki_date_format:"%B %Y"}</strong></div>
{/if}
  <div style="position:relative;height:36px;width:100%">
{section name=dn loop=$daysnames}
    <div id="top_{$smarty.section.dn.index}" class="calHeading" style="position:absolute;top:0%;height:100%">{$daysnames[dn]}</div>
{/section}
  </div>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}<div id="row_{$smarty.section.w.index}" style="position:relative;min-height:150px;height:150px;width:100%;">
  {section name=d loop=$weekdays}
{if $cell[w][d].focus}
{cycle values="calodd,caleven" print=false advance=false}
{else}
{cycle values="caldark" print=false advance=false}
{/if}
  <div id="row_{$smarty.section.w.index}_{$smarty.section.d.index}" class="{cycle}" style="position:absolute;top:0px;min-height:150px;height:150px;background:none">
	<div align="center" class="calfocus{if $cell[w][d].day eq $focuscell}on{/if}">
	  <span style="float:left">
		<a href="{$myurl}?todate={$cell[w][d].day}" title="{tr}Change Focus{/tr}" style="font-size:11px">{$cell[w][d].day|tiki_date_format:$short_format_day}</a> {* day is unix timestamp *}
	  </span>
{if $tiki_p_add_events eq 'y' and count($listcals) > 0}
<a href="tiki-calendar_edit_item.php?todate={$cell[w][d].day}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}" title="{tr}Add Event{/tr}" class="addevent">{icon _id='calendar_add' alt="{tr}+{/tr}"}</a>
{/if}
</div>
<div class="calcontent" style="position:relative">
{if $cell[w][d].focus}
{section name=item loop=$cell[w][d].items}
	{assign var=over value=$cell[w][d].items[item].over}
	{assign var=calendarId value=$cell[w][d].items[item].calendarId}
	{if $cell[w][d].items[item].startTimeStamp >= $cell[w][d].day or $smarty.section.d.index eq '0' or $cell[w][d].firstDay}
<div class="Cal{$cell[w][d].items[item].type} calId{$cell[w][d].items[item].calendarId}" style="position:absolute;top:{$cell[w][d].items[item].top}px;left:0px;height:12px;width:{math equation="x*y" x=100 y=$cell[w][d].items[item].nbDaysLeftThisWeek}%;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};opacity:0.5;filter:Alpha(opacity=50);text-align:center;overflow:hidden;z-index:25">
	<a style="padding:1px 3px;"
{if $myurl eq "tiki-action_calendar.php"}
{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="{$cell[w][d].items[item].url}"{/if}
{else}
{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="tiki-calendar_edit_item.php?viewcalitemId={$cell[w][d].items[item].calitemId}"{/if}
{/if}
{if $prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[item].calitemId}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{else}
{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{/if}
>{$cell[w][d].items[item].name|truncate:$trunc:".."|default:"..."}</a>
{if $cell[w][d].items[item].head > '...'} {* not continued - is real time- starts on this day *}
{/if}
{if $cell[w][d].items[item].web}
<a href="{$cell[w][d].items[item].web}" target="_other" class="calweb" title="{$cell[w][d].items[item].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
{/if}
{if $cell[w][d].items[item].nl}
<a href="tiki-newsletters.php?nlId={$cell[w][d].items[item].nl}&info=1" class="calweb" title="Subscribe"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
{/if}
<br />
</div>
{/if}
{/section}
{/if}
</div>
</div>
{/section}
</div>{/section}
</div>
<script type="text/javascript">
	var calWidth = document.getElementById('calscreen').offsetWidth;
{if $calendarViewMode eq 'month'}
	document.getElementById('month_title').style.width = calWidth;
{/if}	
	calWidth = calWidth - (calWidth%7) - 14; // should be dividable by seven, so that we don't need to round the width of each cell...
	var rightWidth = calWidth / 7;
{section name=dn loop=$daysnames}
	document.getElementById('top_{$smarty.section.dn.index}').style.left=({$smarty.section.dn.index} * rightWidth) + "px";
	document.getElementById('top_{$smarty.section.dn.index}').style.width=rightWidth + "px";
{/section}
{section name=w loop=$cell}
  {section name=d loop=$weekdays}
	document.getElementById('row_{$smarty.section.w.index}_{$smarty.section.d.index}').style.left=({$smarty.section.d.index} * rightWidth) + "px";
	document.getElementById('row_{$smarty.section.w.index}_{$smarty.section.d.index}').style.width=rightWidth + "px";
  {/section}
{/section}
</script>