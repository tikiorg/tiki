{strip}
{if !isset($ajax)}
	{assign var='ajax' value='y'}
{/if}
{if !isset($module)}
	{assign var='module' value='n'}
{/if}
{if empty($module_params.viewnavbar) || $module_params.viewnavbar eq 'y'}
	<div class="clearfix tabrow" {if $module eq 'y'}style="padding: 0"{/if}>
		<div class="viewmode clearfix">
			{*today*}
			{if !isset($calendar_type) or $calendar_type neq "tiki_actions"}
				{if $module neq 'y'}
					{button _auto_args="viewmode,focus" _text="{tr}Today{/tr}" _class="calbuttonoff margin-bottom-sm" _type="xs btn-default" viewmode='day' focus=$now todate=$now}
					<br>
				{else}
					{if empty($module_params.viewmode)}
						{button _auto_args="viewmode,focus" _keepall=y _text="{tr}Today{/tr}" _class="calbuttonoff margin-bottom-sm" _type="xs btn-default" viewmode='day' focus=$now todate=$now}
						<br>
					{else}
						{button _auto_args="focus" _keepall=y _text="{tr}Today{/tr}" _class="calbuttonoff margin-bottom-sm" _type="xs btn-default" focus=$now todate=$now}
						<br>
					{/if}
				{/if}
			{/if}

			<div>{strip}

	{*previous*}
				<div style="display:inline">
					<a class="tips"  href="{query _type='relative' _ajax=$ajax _class='prev' todate=$focus_prev}" title=":{tr}Previous {$viewmode|escape}{/tr}">
						{icon name="previous"}
					</a>
				</div>

	{*viewmodes*}<div class="btn-toolbar" style="display:inline-block;" role="toolbar" aria-label="...">
				{if !isset($calendar_type) or $calendar_type neq "tiki_actions"}
					{if $module neq 'y'}
						{button _ajax=$ajax href="?viewmode=day" _title=":{tr}Day{/tr}" _text="{tr}Day{/tr}" _selected_class="buttonon tips" _type="xs btn-default tips" _selected="{if $viewmode == 'day'}y{else}n{/if}"}
					{elseif empty($module_params.viewmode)}
						{button _ajax=$ajax viewmode='day' _auto_args="viewmode" _keepall='y' _title=":{tr}Day{/tr}" _text="{tr}D{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'day'}y{else}n{/if}"}
					{/if}
				{/if}
				{if $module neq 'y'}
					{button _ajax=$ajax href="?viewmode=week" _title=":{tr}Week{/tr}" _text="{tr}Week{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'week'}y{else}n{/if}"}
					{button _ajax=$ajax href="?viewmode=month" _title=":{tr}Month{/tr}" _text="{tr}Month{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'month'}y{else}n{/if}"}
				{elseif empty($module_params.viewmode)}
					{button _ajax=$ajax viewmode='week' _auto_args="viewmode" _keepall='y' _title=":{tr}Week{/tr}" _text="{tr}W{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'week'}y{else}n{/if}"}
					{button _ajax=$ajax viewmode='month' _auto_args="viewmode" _keepall='y' _title=":{tr}Month{/tr}" _text="{tr}M{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'month'}y{else}n{/if}"}
				{/if}

				{if $module neq 'y'}
					{button _ajax=$ajax href="?viewmode=quarter" _title=":{tr}Quarter{/tr}" _text="{tr}Quarter{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'quarter'}y{else}n{/if}"}
					{button href="?viewmode=semester" _title=":{tr}Semester{/tr}" _text="{tr}Semester{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'semester'}y{else}n{/if}"}
					{button href="?viewmode=year" _ajax=$ajax viewmode=year _title=":{tr}Year{/tr}" _text="{tr}Year{/tr}" _selected_class="buttonon" _type="xs btn-default tips" _selected="{if $viewmode == 'year'}y{else}n{/if}"}
				{/if}
				</div>

	{*next*}
				<div style="display:inline">
					<a class="tips" href="{query _type='relative' _ajax=$ajax _class='next' todate=$focus_next}" title=":{tr}Next {$viewmode|escape}{/tr}">
						{icon name="next"}
					</a>
				</div>
			{/strip}</div>
		</div>
	</div>
	{*<br style="clear:both" />*}
{/if}

{if $viewmode ne 'day'}
	<div class="calnavigation">
{*previous*}
		{if !empty($module_params.viewnavbar) && $module_params.viewnavbar eq 'partial'}
			{self_link _ajax=$ajax _class="prev tips" todate=$focus_prev _title=":{tr}Previous {$viewmode|escape}{/tr}" _icon_name="previous"}{/self_link}
		{/if}

		{if $viewlist ne 'list' or $prefs.calendar_list_begins_focus ne 'y'}
			{if $calendarViewMode eq 'month'}
				{$daystart|tiki_date_format:"%B %Y"}
			{elseif $calendarViewMode eq 'week'}
				{* test display_field_order and use %d/%m or %m/%d *}
				{if ($prefs.display_field_order eq 'DMY') || ($prefs.display_field_order eq 'DYM') || ($prefs.display_field_order eq 'YDM')}
					{$daystart|tiki_date_format:"{tr}%d/%m{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%d/%m{/tr}/%Y"}
				{else}
					{$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
				{/if}
			{else}
				{$daystart|tiki_date_format:"%B %Y"} - {$dayend|tiki_date_format:"%B %Y"}
			{/if}
		{else}
			{$daystart|tiki_date_format:"{tr}%m/%d{/tr}/%Y"} - {$dayend|tiki_date_format:"{tr}%m/%d{/tr}/%Y"}
		{/if}

{*next*}
		{if !empty($module_params.viewnavbar) && $module_params.viewnavbar eq 'partial'}
			{self_link _ajax=$ajax _class="next" todate=$focus_next _title=":{tr}Next {$viewmode|escape}{/tr}" _icon_name="next"}{/self_link}
		{/if}
	</div>
{/if}
{/strip}
