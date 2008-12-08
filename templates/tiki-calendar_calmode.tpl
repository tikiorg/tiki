{if $calendarViewMode eq 'month'}
<table border="0" cellpading="0" cellspacing="0" style="width:100%">
  <tr valign="middle" style="height:36px">
	<td id="month_title" style="text-align:center"><strong>{$currMonth|tiki_date_format:"%B %Y"}</strong></td>
  </tr>
</table>
{/if}
<table border="0" cellpading="0" cellspacing="0" style="width:100%;border-collapse:collapse">
  <tr valign="middle" style="height:36px">
{section name=dn loop=$daysnames}
    <td id="top_{$smarty.section.dn.index}" class="calHeading" width="14%">{$daysnames[dn]}</td>
{/section}
  </tr>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}
  <tr id="row_{$smarty.section.w.index}" style="height:80px">
  {section name=d loop=$weekdays}
	{if $cell[w][d].focus}
	{cycle values="calodd,caleven" print=false advance=false}
	{else}
	{cycle values="caldark" print=false advance=false}
	{/if}
	<td id="row_{$smarty.section.w.index}_{$smarty.section.d.index}" class="{cycle}" style="padding:0px">
	  <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
		<tr valign="top">
		  <td class="calfocus{if $cell[w][d].day eq $focuscell}on{/if}" style="width:50%;text-align:left">
			<a href="{$myurl}?todate={$cell[w][d].day}" title="{tr}Change Focus{/tr}" style="font-size:11px">{$cell[w][d].day|tiki_date_format:$short_format_day}</a>
		  </td>
		  <td class="calfocus{if $cell[w][d].day eq $focuscell}on{/if}" style="width:50%;text-align:right">
			{if $tiki_p_add_events eq 'y' and count($listcals) > 0}
			<a href="tiki-calendar_edit_item.php?todate={$cell[w][d].day}{if $displayedcals|@count eq 1}&amp;calendarId={$displayedcals[0]}{/if}" title="{tr}Add Event{/tr}" class="addevent">{icon _id='calendar_add' alt="{tr}+{/tr}"}</a>
			{/if}
		  </td>
		</tr>
	  </table>
	  <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
{if $cell[w][d].focus}
{section name=item loop=$cell[w][d].items}
	{assign var=over value=$cell[w][d].items[item].over}
	{assign var=calendarId value=$cell[w][d].items[item].calendarId}
		<tr valign="top">
{if is_array($cell[w][d].items[item])}
		  <td class="Cal{$cell[w][d].items[item].type} calId{$cell[w][d].items[item].calendarId}" style="padding:0px;height:14px;background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};border-width:1px {if $cell[w][d].items[item].endTimeStamp <= ($cell[w][d].day + 86400)}1{else}0{/if}px 1px {if $cell[w][d].items[item].startTimeStamp >= $cell[w][d].day}1{else}0{/if}px" nowrap>
			{if $cell[w][d].items[item].startTimeStamp >= $cell[w][d].day or $smarty.section.d.index eq '0' or $cell[w][d].firstDay}
			<a style="padding:1px 3px;"
			{if $myurl eq "tiki-action_calendar.php"}
			{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="{$cell[w][d].items[item].url}"{/if}
			{else}
			{if $cell[w][d].items[item].modifiable eq "y" || $cell[w][d].items[item].visible eq 'y'}href="tiki-calendar_edit_item.php?viewcalitemId={$cell[w][d].items[item].calitemId}"{/if}
			{/if}
			{if $prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[item].calitemId}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{else}
			{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{/if}
			>{$cell[w][d].items[item].name|truncate:$trunc:".."|default:"..."}</a>
			{if $cell[w][d].items[item].head > '...'} {* not continued - is real time- starts on this day *}
			{/if}
			{if $cell[w][d].items[item].web}
			<a href="{$cell[w][d].items[item].web}" target="_other" class="calweb" title="{$cell[w][d].items[item].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
			{/if}
			{if $cell[w][d].items[item].nl}
			<a href="tiki-newsletters.php?nlId={$cell[w][d].items[item].nl}&info=1" class="calweb" title="Subscribe"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
			{/if}
			{else}&nbsp;
			{/if}
		  </td>
{else}
		 <td class="Cal" style="height:14px;width:100%">&nbsp;</td>
{/if}
		</tr>
{/section}
{/if}
	  </table>
	</td>
{/section}
  </tr>
{/section}
</table>
