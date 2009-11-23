{if empty($module_params.viewnavbar) || $module_params.viewnavbar ne 'n'}
<div class="clearfix tabrow"><div class="tabrowRight"></div><div class="tabrowLeft"></div>
<div class="viewmode">
	{if $calendar_type neq "tiki_actions"}
			{button _auto_args="viewmode,focus" _title="{tr}Today{/tr}" _text="{tr}Today{/tr}" _class="calbuttonoff" viewmode='day' focus=$now}
	{/if}
	
<div>
{if $viewmode eq "day"}
{self_link _class="next" todate=$daybefore _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "week"}
{self_link _class="next" todate=$weekbefore _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "month"}
{self_link _class="next" todate=$monthbefore _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "quarter"}
{self_link _class="next" todate=$quarterbefore _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "semester"}
{self_link _class="next" todate=$semesterbefore _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "year"}
{self_link _class="next" todate=$yearbefore _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
{/if}
</div>
{if $calendar_type neq "tiki_actions"}
{button href="?viewmode=day" _title="{tr}Day{/tr}" _text="{tr}Day{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'day'"}
{/if}
{button href="?viewmode=week" _title="{tr}Week{/tr}" _text="{tr}Week{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'week'"}
{button href="?viewmode=month" _title="{tr}Month{/tr}" _text="{tr}Month{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'month'"}
{button href="?viewmode=quarter" _title="{tr}Quarter{/tr}" _text="{tr}Quarter{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'quarter'"}
{button href="?viewmode=semester" _title="{tr}Semester{/tr}" _text="{tr}Semester{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'semester'"}
{button href="?viewmode=year" _title="{tr}Year{/tr}" _text="{tr}Year{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'year'"}
<div>
{if $viewmode eq "day"}
{self_link _class="next" todate=$dayafter _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "week"}
{self_link _class="next" todate=$weekafter _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "month"}
{self_link _class="next" todate=$monthafter _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "quarter"}
{self_link _class="next" todate=$quarterafter _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "semester"}
{self_link _class="next" todate=$semesterafter _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "year"}
{self_link _class="next" todate=$yearafter _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
{/if}
</div></div></div><br style="clear:both" />
{/if}

{if $viewmode ne 'day'}
<div class="calnavigation">
	 {if $viewlist ne 'list' or $prefs.calendar_list_begins_focus ne 'y'}
		{if $calendarViewMode eq 'month'}
			{$currMonth|tiki_date_format:"%B %Y"}
		{elseif $calendarViewMode eq 'week'}
{* test display_field_order and use %d/%m or %m/%d  *}
			{if ($prefs.display_field_order eq 'DMY') || ($prefs.display_field_order eq 'DYM') || ($prefs.display_field_order eq 'YDM')}		
			{$daystart|tiki_date_format:"{tr}%d/%m{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%d/%m{/tr}/%Y"}
			{else} {$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
			{/if}
		{else}
			{$daystart|tiki_date_format:"%B %Y"} - {$dayend|tiki_date_format:"%B %Y"}
		{/if}
	{else}
		{$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
	{/if}
</div>
{/if}
