{* $header$ *}
<table cellpadding="0" cellspacing="0" border="0" class="normal" width="100%">
<tr>
<th class="heading" width="20%"><a class="tableheading" href="{$myurl}?sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}Start{/tr}</a></th>
<th class="heading" width="20%"><a class="tableheading" href="{$myurl}?sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}End{/tr}</a></th>
<th class="heading"><a class="tableheading" href="{$myurl}?sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
<th class="heading">{tr}Action{/tr}</th>
</tr>
{if $listevents|@count eq 0}<tr><td colspan="5">{tr}No records found{/tr}</td></tr>{/if}
{cycle values="odd,even" print=false}
{section name=w loop=$listevents}
<tr class="{cycle}{if $listevents[w].start <= $smarty.now and $listevents[w].end >= $smarty.now} selected{/if} vevent">
<td>
<abbr class="dtstart" title="{$listevents[w].startTimeStamp|isodate}"><a href="{$myurl}?todate={$listevents[w].start}" title="{tr}Change Focus{/tr}">{$listevents[w].start|tiki_short_date}</a></abbr><br />
{$listevents[w].start|tiki_short_time}
</td>
<td>
{if $listevents[w].start|tiki_short_date ne $listevents[w].end|tiki_short_date}<a href="{$myurl}?todate={$listevents[w].end}" title="{tr}Change Focus{/tr}">{$listevents[w].end|tiki_short_date}</a>{/if}<br />
{if $listevents[w].start ne $listevents[w].end}{$listevents[w].end|tiki_short_time}{/if}
</td>
<td>
<a class="link" href="tiki-calendar_edit_item.php?viewcalitemId={$listevents[w].calitemId}" title="{tr}View{/tr}"><span class="summary">{$listevents[w].name|escape}</span></a><br />
<span class="description" style="font-style:italic">{$listevents[w].parsed}</span>
{if $listevents[w].web}
<br /><a href="{$listevents[w].web}" target="_other" class="calweb" title="{$listevents[w].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" /></a>
{/if}
</td>
<td>
{if $listevents[w].modifiable eq "y"}<a class="link" href="tiki-calendar_edit_item.php?calitemId={$listevents[w].calitemId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
<a class="link" href="tiki-calendar_edit_item.php?calitemId={$listevents[w].calitemId}&amp;delete=1" title="{tr}Remove{/tr}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>{/if}
</td></tr>
{/section}
</table>

