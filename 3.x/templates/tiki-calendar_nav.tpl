<div class="clearfix tabrow"><div class="tabrowRight"></div><div class="tabrowLeft"></div>
<div class="viewmode">{if $calendar_type eq "tiki_actions"}<div></div>{else}<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbuttonoff"><a href="{$myurl}?viewmode=day&focus={$now}" title="{tr}Today{/tr}">{tr}Today{/tr}</a></div><div class="calbuttonRight"></div></div>{/if}
<div id="prev">
{if $viewmode eq "day"}
<a href="{$myurl}?todate={$daybefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Day{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "week"}
<a href="{$myurl}?todate={$weekbefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Week{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "month"}
<a href="{$myurl}?todate={$monthbefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Month{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "quarter"}
<a href="{$myurl}?todate={$quarterbefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Quarter{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "semester"}
<a href="{$myurl}?todate={$semesterbefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Semester{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "year"}
<a href="{$myurl}?todate={$yearbefore}&amp;viewmode={$viewmode}" title="&laquo; {tr}Year{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{/if}
</div>
{if $calendar_type neq "tiki_actions"}
<div class="calbuttonBox calbutton{if $viewmode eq 'day'}on{else}off{/if}"><a href="{$myurl}?viewmode=day" title="{tr}Day{/tr}">{tr}Day{/tr}</a></div>
{/if}
<div class="calbuttonBox calbutton{if $viewmode eq 'week'}on{else}off{/if}"><a href="{$myurl}?viewmode=week" title="{tr}Week{/tr}">{tr}Week{/tr}</a></div>
<div class="calbuttonBox calbutton{if $viewmode eq 'month'}on{else}off{/if}"><a href="{$myurl}?viewmode=month" title="{tr}Month{/tr}">{tr}Month{/tr}</a></div>
<div class="calbuttonBox calbutton{if $viewmode eq 'quarter'}on{else}off{/if}"><a href="{$myurl}?viewmode=quarter" title="{tr}Quarter{/tr}">{tr}Quarter{/tr}</a></div>
<div class="calbuttonBox calbutton{if $viewmode eq 'semester'}on{else}off{/if}"><a href="{$myurl}?viewmode=semester" title="{tr}Semester{/tr}">{tr}Semester{/tr}</a></div>
<div class="calbuttonBox calbutton{if $viewmode eq 'year'}on{else}off{/if}"><a href="{$myurl}?viewmode=year" title="{tr}Year{/tr}">{tr}Year{/tr}</a></div>
<div id="next">
{if $viewmode eq "day"}
<a href="{$myurl}?todate={$dayafter}&amp;viewmode={$viewmode}" title="{tr}Day{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "week"}
<a href="{$myurl}?todate={$weekafter}&amp;viewmode={$viewmode}" title="{tr}Week{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "month"}
<a href="{$myurl}?todate={$monthafter}&amp;viewmode={$viewmode}" title="{tr}Month{/tr}&raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "quarter"}
<a href="{$myurl}?todate={$quarterafter}&amp;viewmode={$viewmode}" title="{tr}Quarter{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "semester"}
<a href="{$myurl}?todate={$semesterafter}&amp;viewmode={$viewmode}" title="{tr}Semester{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "year"}
<a href="{$myurl}?todate={$yearafter}&amp;viewmode={$viewmode}" title="{tr}Year{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{/if}
</div></div></div><br style="clear:both" />

{if $viewmode ne 'day'}
<div class="calnavigation">
	 {if $viewlist ne 'list' or $prefs.calendar_list_begins_focus ne 'y'}
		{if $calendarViewMode eq 'month'}
			{$currMonth|tiki_date_format:"%B %Y"}
		{elseif $calendarViewMode eq 'week'}
			{$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
		{else}
			{$daystart|tiki_date_format:"%B %Y"} - {$dayend|tiki_date_format:"%B %Y"}
		{/if}
	{else}
		{$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
	{/if}
</div>
{/if}
