{* $Id$ *}

{title admpage="calendar"}
	{if $displayedcals|@count eq 1}
		{tr}Calendar:{/tr} {assign var=x value=$displayedcals[0]}{$infocals[$x].name}
	{else}
		{tr}Calendar{/tr}
	{/if}
{/title}
{if $prefs.javascript_enabled != 'y'}
	{$js = 'n'}
{else}
	{$js = 'y'}
{/if}

<div id="calscreen">
	<div class="t_navbar margin-bottom-md">
		<div class="btn-group pull-right">
			{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
			<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
				{icon name='menu-extra'}
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="dropdown-title">
					{tr}Monitoring{/tr}
				</li>
				<li class="divider"></li>
				{if $displayedcals|@count eq 1 and $user and $prefs.feature_user_watches eq 'y'}
					<li>
						{if $user_watching eq 'y'}
							<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=remove" hspace="1">
								{icon name="stop-watching"} {tr}Stop monitoring{/tr}
							</a>
						{else}
							<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=add" hspace="1">
								{icon name="watch"} {tr}Monitor{/tr}
							</a>
						{/if}
					</li>
				{/if}
				{if $displayedcals|@count eq 1 and $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
					<li>
						<a href="tiki-object_watches.php?objectId={$displayedcals[0]|escape:"url"}&amp;watch_event=calendar_changed&amp;objectType=calendar&amp;objectName={$infocals[$x].name|escape:"url"}&amp;objectHref={'tiki-calendar.php?calIds[]='|cat:$displayedcals[0]|escape:"url"}" hspace="1">
							{icon name="watch-group"} {tr}Group Monitor{/tr}
						</a>
					</li>
				{/if}
			</ul>
			{if $js == 'n'}</li></ul>{/if}
		</div>
		{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
			{if $displayedcals|@count eq 1}
				{button href="tiki-admin_calendars.php?calendarId={$displayedcals[0]}" _type="link" _text="{tr}Edit{/tr}" _icon_name="edit"}
			{/if}
			{button href="tiki-admin_calendars.php?cookietab=1" _type="link" _text="{tr}Admin{/tr}" _icon_name="admin"}
		{/if}

{* avoid Add Event being shown if no calendar is displayed *}
		{if $tiki_p_add_events eq 'y'}
			{button href="tiki-calendar_edit_item.php" _type="link" _text="{tr}Add Event{/tr}" _icon_name="create"}
		{/if}

		{if $tiki_p_view_events eq 'y' and $prefs.calendar_export eq 'y'}
			{button href="#" _onclick="toggle('exportcal');return false;" _text="{tr}Export{/tr}" _icon_name="import"}
		{/if}

		{if $viewlist eq 'list'}
			{capture name=href}?viewlist=table{if $smarty.request.todate}&amp;todate={$smarty.request.todate}{/if}{/capture}
			{button href=$smarty.capture.href _text="{tr}Calendar View{/tr}" _icon_name="calendar"}
		{else}
			{capture name=href}?viewlist=list{if $smarty.request.todate}&amp;todate={$smarty.request.todate}{/if}{/capture}
			{button href=$smarty.capture.href _text="{tr}List View{/tr}" _icon_name="list"}
		{/if}

		{if count($listcals) >= 1}
			{button href="#" _onclick="toggle('filtercal');return false;" _text="{tr}Visible Calendars{/tr}" _icon_name="eye"}

			{if count($thiscal)}
				<div id="configlinks" class="form-group text-right">
					{assign var='maxCalsForButton' value=20}
					{if count($checkedCals) > $maxCalsForButton}<select size="5">{/if}
					{foreach item=k from=$listcals name=listc}
						{if $thiscal.$k}
							{assign var=thiscustombgcolor value=$infocals.$k.custombgcolor}
							{assign var=thiscustomfgcolor value=$infocals.$k.customfgcolor}
							{assign var=thisinfocalsname value=$infocals.$k.name|escape}
							{if count($checkedCals) > $maxCalsForButton}
								<option style="background:#{$thiscustombgcolor};color:#{$thiscustomfgcolor};" onclick="toggle('filtercal')">{$thisinfocalsname}</option>
							{else}
								{button href="#" _style="background:#$thiscustombgcolor;color:#$thiscustomfgcolor;border:1px solid #$thiscustomfgcolor;" _onclick="toggle('filtercal');return false;" _text="$thisinfocalsname"}
							{/if}
						{/if}
					{/foreach}
					{if count($checkedCals) > $maxCalsForButton}</select>{/if}
				</div>
			{else}
				{button href="" _style="background-color:#fff;padding:0 4px;" _text="{tr}None{/tr}"}
			{/if}
		{/if}
	</div>
{* show jscalendar if set *}
		{if $prefs.feature_jscalendar eq 'y'}
			<div class="jscalrow" style="display: inline-block">
				<form action="{$myurl}" method="post" name="f">
					{jscalendar date="$focusdate" id="trig" goto="$jscal_url" align="Bc"}
				</form>
			</div>
		{/if}




	<div class="categbar" align="right">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if isset($category_watched) and $category_watched eq 'y'}
				{tr}Watched by categories:{/tr}
				{section name=i loop=$watching_categories}
					{assign var=thiswatchingcateg value=$watching_categories[i].categId}
					{button href="tiki-browse_categories.php?parentId=$thiswatchingcateg" _text=$watching_categories[i].name|escape}
					&nbsp;
				{/section}
			{/if}
		{/if}
	</div>

	{if count($listcals) >= 1}
		<form class="modal-content" id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
			<div class="modal-header caltitle">{tr}Group Calendars{/tr}</div>
			<div class="modal-body">
				<div class="caltoggle">
					{select_all checkbox_names='calIds[]' label="{tr}Check / Uncheck All{/tr}"}
				</div>
				{foreach item=k from=$listcals}
					<div class="calcheckbox">
						<input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if}>
						<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name|escape} (id #{$k})</label>
					</div>
				{/foreach}
				<div class="calinput">
					<input type="hidden" name="todate" value="{$focusdate}">
					<input type="submit" class="btn btn-default btn-sm" name="refresh" value="{tr}Refresh{/tr}">
				</div>
			</div>
		</form>
	{/if}

	{if $tiki_p_view_events eq 'y'}
		<form id="exportcal" class="modal-content" method="post" action="{$exportUrl}" name="f" style="display:none;">
			<input type="hidden" name="export" value="y">
			<div class="caltitle">{tr}Export calendars{/tr}</div>
			<div class="caltoggle">
				{select_all checkbox_names='calendarIds[]' label="{tr}Check / Uncheck All{/tr}"}
			</div>
			{foreach item=k from=$listcals}
				<div class="calcheckbox">
					<input type="checkbox" name="calendarIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if}>
					<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name|escape}</label>
				</div>
			{/foreach}
			<div class="calcheckbox">
				<a href="{$iCalAdvParamsUrl}">{tr}advanced parameters{/tr}</a>
			</div>
			<div class="calinput">
				<input type="submit" class="btn btn-default btn-sm" name="ical" value="{tr}Export as iCal{/tr}">
				<input type="submit" class="btn btn-default btn-sm" name="csv" value="{tr}Export as CSV{/tr}">
			</div>
		</form>
	{/if}

	{if $prefs.display_12hr_clock eq 'y'}
		{assign var="timeFormat" value="h(:mm)TT"}
	{else}
		{assign var="timeFormat" value="HH:mm"}
	{/if}
	{if $prefs.calendar_fullcalendar neq 'y' or $viewlist eq 'list'}
		{include file='tiki-calendar_nav.tpl'}
		{if $viewlist eq 'list'}
			{include file='tiki-calendar_listmode.tpl'}
		{elseif $viewmode eq 'day'}
			{include file='tiki-calendar_daymode.tpl'}
		{elseif $viewmode eq 'week'}
			{include file='tiki-calendar_weekmode.tpl'}
		{else}
			{include file='tiki-calendar_calmode.tpl'}
		{/if}
	{else}
		{jq}
			$('#calendar').fullCalendar({
				timeFormat: {
					'': '{{$timeFormat}}'
				},
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				editable: true,
				events: 'tiki-calendar_json.php',
				year: {{$viewyear}},
				month: {{$viewmonth}}-1,
				day: {{$viewday}},
				minTime: {{$minHourOfDay}},
				maxTime: {{$maxHourOfDay}},
				monthNames: [ "{tr}January{/tr}", "{tr}February{/tr}", "{tr}March{/tr}", "{tr}April{/tr}", "{tr}May{/tr}", "{tr}June{/tr}", "{tr}July{/tr}", "{tr}August{/tr}", "{tr}September{/tr}", "{tr}October{/tr}", "{tr}November{/tr}", "{tr}December{/tr}"],
				monthNamesShort: [ "{tr}Jan.{/tr}", "{tr}Feb.{/tr}", "{tr}Mar.{/tr}", "{tr}Apr.{/tr}", "{tr}May{/tr}", "{tr}June{/tr}", "{tr}July{/tr}", "{tr}Aug.{/tr}", "{tr}Sep.{/tr}", "{tr}Oct.{/tr}", "{tr}Nov.{/tr}", "{tr}Dec.{/tr}"],
				dayNames: ["{tr}Sunday{/tr}", "{tr}Monday{/tr}", "{tr}Tuesday{/tr}", "{tr}Wednesday{/tr}", "{tr}Thursday{/tr}", "{tr}Friday{/tr}", "{tr}Saturday{/tr}"],
				dayNamesShort: ["{tr}Sun{/tr}", "{tr}Mon{/tr}", "{tr}Tue{/tr}", "{tr}Wed{/tr}", "{tr}Thu{/tr}", "{tr}Fri{/tr}", "{tr}Sat{/tr}"],
				buttonText: {
					today: "{tr}today{/tr}",
					month: "{tr}month{/tr}",
					week: "{tr}week{/tr}",
					day: "{tr}day{/tr}"
				},
				allDayText: "{tr}all-day{/tr}",
				firstDay: {{$firstDayofWeek}},
				slotMinutes: {{$prefs.calendar_timespan}},
				defaultView: {{if $prefs.calendar_view_mode === 'week'}}'agendaWeek'{{else if $prefs.calendar_view_mode === 'day'}}'agendaDay'{{else}}'month'{{/if}},
				eventAfterRender : function( event, element, view ) {
					element.attr('title',event.title);
					element.data('content', event.description);
					element.popover({ trigger: 'hover', html: true, 'container': 'body' });
				},
				eventClick: function(event) {
					if (event.url && event.editable) {
						var $this = $(this).tikiModal(" ");
						$.ajax({
							dataType: 'html',
							url: event.url + '&isModal=1',
							success: function(data){
								var $dialog = $( "#calendar_dialog" ).remove()
								$( "#calendar_dialog_content", $dialog ).html(data);
								$( "#calendar_dialog h1, #calendar_dialog .navbar", $dialog ).remove();
								$( "#calendar_dialog .modal-title", $dialog ).html(event.title);
								$dialog.appendTo("body").modal({backdrop:"static"});
								$this.tikiModal();
							}
						});
			//						$('#calendar_dialog').load(event.url + ' .wikitext');
			//						$( "#calendar_dialog" ).dialog({ modal: true, title: event.title, width: 'auto', height: 'auto', position: 'center' });
						return false;
					}
				},
				dayClick: function(date, allDay, jsEvent, view) {
					$.ajax({
						dataType: 'html',
						url: 'tiki-calendar_edit_item.php?fullcalendar=y&todate=' + date.getTime()/1000 + '&isModal=1',
						success: function(data){
							//$( "#calendar_dialog" ).html(data);
							$( "#calendar_dialog_content" ).html(data);
							$( "#calendar_dialog h1, #calendar_dialog .navbar" ).remove();
							$( "#calendar_dialog .modal-title" ).html('{tr}Add Event{/tr}');
							$( "#calendar_dialog" ).modal();
							//$( "#calendar_dialog" ).dialog({ modal: true, title: '{tr}Add Event{/tr}', width: 'auto', height: 'auto', position: 'center' });
						}
					});
					return false;
				},
				eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
					$.post($.service('calendar', 'resize'), {
						calitemId: event.id,
						delta: (dayDelta*86400+minuteDelta*60)
					});
				},
				eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
					$.post($.service('calendar', 'move'), {
						calitemId: event.id,
						delta: (dayDelta*86400+minuteDelta*60)
					});
				}
			});
		{/jq}

		<style type='text/css'>
			#calendar {
				width: 90%;
				margin: 0 auto;
			}
			/* Fix pb with DatePicker */
			.ui-datepicker {
				z-index:9999 !important;
			}
		</style>
		<div id='calendar'></div>

		<!--<div id='calendar_dialog'></div>-->

		<div id="calendar_dialog" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content" id="calendar_dialog_content">
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	{/if}
	<p>&nbsp;</p>
</div>
{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
	{js_insert_icon type="jscalendar"}
{/if}