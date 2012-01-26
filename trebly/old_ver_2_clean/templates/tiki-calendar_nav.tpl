{strip}
{if !isset($ajax)}
	{assign var='ajax' value='y'}
{/if}
{if empty($module_params.viewnavbar) || $module_params.viewnavbar eq 'y'}
<div class="clearfix tabrow" {if $module eq 'y'}style="padding: 0pt"{/if}>
{if $module neq 'y'}
	<div class="tabrowRight"></div>
	<div class="tabrowLeft"></div>
{/if}
	<div class="viewmode">
	{if $calendar_type neq "tiki_actions"}
			{if $module neq 'y'}
				{button _auto_args="viewmode,focus" _title="{tr}Today{/tr}" _text="{tr}Today{/tr}" _class="calbuttonoff" viewmode='day' focus=$now}
			{else}
				{if empty($module_params.viewmode)}
					{button _auto_args="viewmode,focus" _keepall=y _title="{tr}Today{/tr}" _text="{tr}Today{/tr}" _class="calbuttonoff" viewmode='day' focus=$now}
				{else}
					{button _auto_args="focus" _keepall=y _title="{tr}Today{/tr}" _text="{tr}Today{/tr}" _class="calbuttonoff" focus=$now}
				{/if}
				<br class="clearfix" />
			{/if}
	{/if}
	
		<span style="display: inline-block">{strip}
{*previous*}
		<div>
		{if $viewmode eq "day"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "week"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "month"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "quarter"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "semester"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "year"}
			{self_link _ajax=$ajax _class="next" todate=$focus_prev _title="{tr}Year{/tr}" _alt="{tr}Year{/tr}" _icon=resultset_previous"}{/self_link}
		{/if}
		</div>

{*viewmodes*}
		{if $calendar_type neq "tiki_actions"}
			{if $module neq 'y'}
				{button _ajax=$ajax href="?viewmode=day" _title="{tr}Day{/tr}" _text="{tr}Day{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'day'"}
			{elseif empty($module_params.viewmode)}
				{button _ajax=$ajax viewmode='day' _auto_args="viewmode" _keepall='y' _title="{tr}Day{/tr}" _text="{tr}D{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'day'"}
			{/if}
		{/if}
		{if $module neq 'y'}
			{button _ajax=$ajax href="?viewmode=week" _title="{tr}Week{/tr}" _text="{tr}Week{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'week'"}
			{button _ajax=$ajax href="?viewmode=month" _title="{tr}Month{/tr}" _text="{tr}Month{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'month'"}
		{elseif empty($module_params.viewmode)}
			{button _ajax=$ajax viewmode='week' _auto_args="viewmode" _keepall='y' _title="{tr}Week{/tr}" _text="{tr}W{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'week'"}
			{button _ajax=$ajax viewmode='month' _auto_args="viewmode" _keepall='y' _title="{tr}Month{/tr}" _text="{tr}M{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'month'"}
		{/if}

		{if $module neq 'y'}
			{button _ajax=$ajax href="?viewmode=quarter" _title="{tr}Quarter{/tr}" _text="{tr}Quarter{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'quarter'"}
			{button href="?viewmode=semester" _title="{tr}Semester{/tr}" _text="{tr}Semester{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'semester'"}
			{button href="?viewmode=year" _ajax=$ajax viewmode=year _title="{tr}Year{/tr}" _text="{tr}Year{/tr}" _selected_class="buttonon" _selected="'$viewmode' == 'year'"}
		{/if}

{*next*}
		<div>
		{if $viewmode eq "day"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "week"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "month"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "quarter"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "semester"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "year"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Year{/tr}" _alt="{tr}Year{/tr}" _icon=resultset_next"}{/self_link}
		{/if}
		</div>
		{/strip}</span>
	</div>
</div>
<br style="clear:both" />
{/if}

{if $viewmode ne 'day'}
<div class="calnavigation">
{*previous*}
	 {if !empty($module_params.viewnavbar) && $module_params.viewnavbar eq 'partial'}
		{if $viewmode eq "day"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "week"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "month"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "quarter"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "semester"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
		{elseif $viewmode eq "year"}
			{self_link _ajax=$ajax todate=$focus_prev _title="{tr}Year{/tr}" _alt="{tr}Year{/tr}" _icon=resultset_previous"}{/self_link}
		{/if}
	{/if}

	{if $viewlist ne 'list' or $prefs.calendar_list_begins_focus ne 'y'}
		{if $calendarViewMode eq 'month'}
			{$daystart|tiki_date_format:"%B %Y"}
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

{*next*}
	{if !empty($module_params.viewnavbar) && $module_params.viewnavbar eq 'partial'}
		{if $viewmode eq "day"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "week"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "month"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "quarter"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "semester"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
		{elseif $viewmode eq "year"}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title="{tr}Year{/tr}" _alt="{tr}Year{/tr}" _icon=resultset_next"}{/self_link}
		{/if}
	{/if}
</div>
{/if}
{/strip}