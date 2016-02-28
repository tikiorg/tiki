{title admpage="calendar"}{tr}Calendar event : {/tr}{$calitem.name|escape}{/title}

{if isset($smarty.get.isModal) && $smarty.get.isModal}
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"></h4>
	</div>
{/if}
<form action="{$myurl|escape}" method="post" name="f" id="editcalitem" class="form-horizontal">
	<div class="modal-body">
		{if !$smarty.get.isModal}
			<div class="t_navbar form-group">
				{if $tiki_p_view_calendar eq 'y'}
					{button href="tiki-calendar.php" _text="{tr}View Calendars{/tr}" _icon_name="view"}
				{/if}
				{if $tiki_p_admin_calendar eq 'y'}
					{button href="tiki-admin_calendars.php?calendarId=$calendarId" _icon_name="edit" _text="{tr}Edit Calendar{/tr}"}
				{/if}
				{if $tiki_p_add_events eq 'y' and $id}
					{button href="tiki-calendar_edit_item.php" _icon_name="add" _text="{tr}New event{/tr}"}
				{/if}
				{if $id}
					{if $edit}
						{button href="tiki-calendar_edit_item.php?viewcalitemId=$id" _icon_name="view" _text="{tr}View event{/tr}"}
					{elseif $tiki_p_change_events eq 'y'}
						{button href="tiki-calendar_edit_item.php?calitemId=$id" _icon_name="edit" _text="{tr}Edit/Delete event{/tr}"}
					{/if}
				{/if}
				{if $tiki_p_admin_calendar eq 'y'}
					{button href="tiki-admin_calendars.php" _icon_name="admin" _text="{tr}Admin Calendars{/tr}"}
				{/if}
				{if $prefs.calendar_fullcalendar neq 'y' or not $edit}
					{if $prefs.calendar_export_item == 'y' and $tiki_p_view_calendar eq 'y'}
						{button href='tiki-calendar_export_ical.php? export=y&calendarItem='|cat:$id _icon_name="export" _text="{tr}Export Event as iCal{/tr}"}
					{/if}
				{/if}
			</div>
		{/if}

		<div class="wikitext">
			{if $edit}
				{if $preview}
					<h2>
						{tr}Preview{/tr}
					</h2>
					{$calitem.parsedName}
					<div class="preview">
						{$calitem.parsed}
					</div>
					<h2>
						{if $id}
							{tr}Edit Calendar Item{/tr}
						{else}
							{tr}New Calendar Item{/tr}
						{/if}
					</h2>
				{/if}
				<input type="hidden" name="save[user]" value="{$calitem.user|escape}">
				{if $id}
					<input type="hidden" name="save[calitemId]" value="{$id|escape}">
				{/if}
			{/if}
			{if $prefs.calendar_addtogooglecal == 'y'}
				{wikiplugin _name="addtogooglecal" calitemid=$id}{/wikiplugin}
			{/if}
			<div class="form-group">
				<label for="calid" class="control-label col-md-3">{tr}Calendar{/tr}</label>
				<div class="col-md-9">
					{if $edit}
						{if $prefs.javascript_enabled eq 'n'}
							{$calendar.name|escape}<br>{tr}or{/tr}&nbsp;
							<input type="submit" class="btn btn-default btn-sm" name="changeCal" value="{tr}Go to{/tr}">
						{/if}
						<select name="save[calendarId]" id="calid" onchange="needToConfirm=false;document.getElementById('editcalitem').submit();" class="form-control">
							{foreach item=it key=itid from=$listcals}
								{if $it.tiki_p_add_events eq 'y'}
									{$calstyle = ''}
									{if not empty($it.custombgcolor)}
										{$calstyle='background-color:#'|cat:$it.custombgcolor}
									{/if}
									{if not empty($it.customfgcolor)}
										{$calstyle='color:#'|cat:$it.customfgcolor}
									{/if}
									{if $calstyle}
										{$calstyle = ' style="'|cat:$calstyle|cat:'"'}
									{/if}
									<option value="{$it.calendarId}"{$calstyle}
										{if isset($calitem.calendarId)}
											{if $calitem.calendarId eq $itid}
												selected="selected"
											{/if}
										{elseif $calendarView}
											{if $calendarView eq $itid}
												selected="selected"
											{/if}
										{else}
											{if $calendarId}
												{if $calendarId eq $itid}
													selected="selected"
												{/if}
											{/if}
										{/if}
									>
										{$it.name|escape}
									</option>
								{/if}
							{/foreach}
						</select>
					{else}
						{$listcals[$calitem.calendarId].name|escape}
					{/if}
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3">{tr}Title{/tr}</label>
				<div class="col-md-9">
					{if $edit}
						<input type="text" name="save[name]" value="{$calitem.name|escape}" size="32" class="form-control">
					{else}
						<span class="summary">
							{$calitem.name|escape}
						</span>
					{/if}
				</div>
			</div>
			{if $edit or $recurrence.id gt 0}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Recurrence{/tr}</label>
					<div class="col-md-9">
						{if $edit}
							{if $recurrence.id gt 0}
								<input type="hidden" name="recurrent" value="1">
								{tr}This event depends on a recurrence rule{/tr}
							{else}
								<div class="checkbox">
									<label>
										<input type="checkbox" id="id_recurrent" name="recurrent" value="1"{if $calitem.recurrenceId gt 0 or $recurrent eq 1} checked="checked" {/if}>
										{tr}This event depends on a recurrence rule{/tr}
										{jq}
$("#id_recurrent").click(function () {
	if ($(this).prop("checked")) {
		$("#recurrenceRules").show();
		$(".date").hide();
	} else {
		$("#recurrenceRules").hide();
		$(".date").show();
	}
});
										{/jq}
									</label>
								</div>
							{/if}
						{else}
							<span class="summary">
								{if $calitem.recurrenceId gt 0}
									{tr}This event depends on a recurrence rule{/tr}
								{else}
									{tr}This event is not recurrent{/tr}
								{/if}
							</span>
						{/if}
					</div>
				</div> <!-- / .form-group -->
				<div class="row">
					<div class="col-md-9 col-md-push-3">
						{if $edit}
							<div id="recurrenceRules" style=" {if ( !($calitem.recurrenceId gt 0) and $recurrent neq 1 ) && $prefs.javascript_enabled eq 'y'} display:none; {/if}" >
								{if $calitem.recurrenceId gt 0}
									<input type="hidden" name="recurrenceId" value="{$recurrence.id}">
								{/if}
								{if $recurrence.id gt 0}
									{if $recurrence.weekly}
										<input type="hidden" name="recurrenceType" value="weekly">{tr}On a weekly basis{/tr}<br>
									{/if}
								{else}
									<input type="radio" id="id_recurrenceTypeW" name="recurrenceType" value="weekly" {if $recurrence.weekly or $calitem.calitemId eq 0} checked="checked" {/if} >
									<label for="id_recurrenceTypeW">
										{tr}On a weekly basis{/tr}
									</label>
								{/if}
								{if $recurrence.id eq 0 or $recurrence.weekly}
									<div class="form-group">
										<div class="col-md-offset-1 col-md-4 input-group">
											<span class="input-group-addon">{tr}Each{/tr}</span>
											<select name="weekday" class="form-control">
												<option value="0" {if $recurrence.weekday eq '0'} selected="selected" {/if} >
													{tr}Sunday{/tr}
												</option>
												<option value="1"
														{if $recurrence.weekday eq '1'} selected="selected" {/if} >
													{tr}Monday{/tr}
												</option>
												<option value="2" {if $recurrence.weekday eq '2'} selected="selected" {/if} >
													{tr}Tuesday{/tr}
												</option>
												<option value="3" {if $recurrence.weekday eq '3'} selected="selected" {/if} >
													{tr}Wednesday{/tr}
												</option>
												<option value="4" {if $recurrence.weekday eq '4'} selected="selected" {/if} >
													{tr}Thursday{/tr}
												</option>
												<option value="5" {if $recurrence.weekday eq '5'} selected="selected" {/if} >
													{tr}Friday{/tr}
												</option>
												<option value="6" {if $recurrence.weekday eq '6'} selected="selected" {/if} >
													{tr}Saturday{/tr}
												</option>
											</select>
											<span class="input-group-addon">{tr}of the week{/tr}</span>
										</div>
										<hr/>
									</div>
								{/if}
								{if $recurrence.id gt 0}
									{if $recurrence.monthly}
										<input type="hidden" name="recurrenceType" value="monthly">{tr}On a monthly basis{/tr}<br>
									{/if}
								{else}
									<input type="radio" id="id_recurrenceTypeM" name="recurrenceType" value="monthly" {if $recurrence.monthly} checked="checked" {/if} >
									<label for="id_recurrenceTypeM">
										{tr}On a monthly basis{/tr}
									</label>
								{/if}
								{if $recurrence.id eq 0 or $recurrence.monthly}
								<div class="form-group">
									<div class="col-md-offset-1 col-md-4 input-group">
										<span class="input-group-addon">{tr}Each{/tr}</span>
										<select name="dayOfMonth" class="form-control">
											{section name=k start=1 loop=32}
												<option value="{$smarty.section.k.index}" {if $recurrence.dayOfMonth eq $smarty.section.k.index} selected="selected" {/if} >
													{if $smarty.section.k.index lt 10}
														0
													{/if}
													{$smarty.section.k.index}
												</option>
											{/section}
										</select>
										<span class="input-group-addon">{tr}of the month{/tr}</span>
									</div>
									<hr/>
								</div>
								{/if}
								{if $recurrence.id gt 0}
									{if $recurrence.yearly}
										<input type="hidden" name="recurrenceType" value="yearly">{tr}On a yearly basis{/tr}<br>
									{/if}
								{else}
									<input type="radio" id="id_recurrenceTypeY" name="recurrenceType" value="yearly" {if $recurrence.yearly} checked="checked" {/if}
									>
									<label for="id_recurrenceTypeY">
										{tr}On a yearly basis{/tr}
									</label>
									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								{/if}
								{if $recurrence.id eq 0 or $recurrence.yearly}
								<div class="form-group">
									<div class="col-md-offset-1 col-md-4 input-group">
										<span class="input-group-addon">{tr}Each{/tr}</span>
										<select name="dateOfYear_day" class="form-control" onChange="checkDateOfYear(this.options[this.selectedIndex].value,document.forms['f'].elements['dateOfYear_month'].options[document.forms['f'].elements['dateOfYear_month'].selectedIndex].value);">
											{section name=k start=1 loop=32}
												<option value="{$smarty.section.k.index}" {if $recurrence.dateOfYear_day eq $smarty.section.k.index} selected="selected" {/if} >
													{if $smarty.section.k.index lt 10}
														0
													{/if}
													{$smarty.section.k.index}
												</option>
											{/section}
										</select>
										<span class="input-group-addon">{tr}of{/tr}</span>
										<select name="dateOfYear_month" class="form-control" onChange="checkDateOfYear(document.forms['f'].elements['dateOfYear_day'].options[document.forms['f'].elements['dateOfYear_day'].selectedIndex].value,this.options[this.selectedIndex].value);">
											<option value="1" {if $recurrence.dateOfYear_month eq '1'} selected="selected" {/if} >
												{tr}January{/tr}
											</option>
											<option value="2" {if $recurrence.dateOfYear_month eq '2'} selected="selected" {/if} >
												{tr}February{/tr}
											</option>
											<option value="3" {if $recurrence.dateOfYear_month eq '3'} selected="selected" {/if} >
												{tr}March{/tr}
											</option>
											<option value="4" {if $recurrence.dateOfYear_month eq '4'} selected="selected" {/if} >
												{tr}April{/tr}
											</option>
											<option value="5" {if $recurrence.dateOfYear_month eq '5'} selected="selected" {/if} >
												{tr}May{/tr}
											</option>
											<option value="6" {if $recurrence.dateOfYear_month eq '6'} selected="selected" {/if} >
												{tr}June{/tr}
											</option>
											<option value="7" {if $recurrence.dateOfYear_month eq '7'} selected="selected" {/if} >
												{tr}July{/tr}
											</option>
											<option value="8" {if $recurrence.dateOfYear_month eq '8'} selected="selected" {/if} >
												{tr}August{/tr}
											</option>
											<option value="9" {if $recurrence.dateOfYear_month eq '9'} selected="selected" {/if} >
												{tr}September{/tr}
											</option>
											<option value="10" {if $recurrence.dateOfYear_month eq '10'} selected="selected" {/if} >
												{tr}October{/tr}</option>
											<option value="11" {if $recurrence.dateOfYear_month eq '11'} selected="selected" {/if} >
												{tr}November{/tr}
											</option>
											<option value="12" {if $recurrence.dateOfYear_month eq '12'} selected="selected" {/if} >
												{tr}December{/tr}
											</option>
										</select>
									</div>
								</div>
								<span id="errorDateOfYear"></span>
								<hr>
								{/if}
								{if $recurrence.id gt 0}
									<input type="hidden" name="startPeriod" value="{$recurrence.startPeriod}">
									<input type="hidden" name="nbRecurrences" value="{$recurrence.nbRecurrences}">
									<input type="hidden" name="endPeriod" value="{$recurrence.endPeriod}">
									{tr}Starting on{/tr} {$recurrence.startPeriod|tiki_long_date},&nbsp;
									{if $recurrence.endPeriod gt 0}
										{tr}ending by{/tr} {$recurrence.endPeriod|tiki_long_date}
									{else}
										{tr}ending after{/tr} {$recurrence.nbRecurrences} {tr}events{/tr}
									{/if}.
								{else}
									{tr}Start period{/tr}<br>
									{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
										<div class="col-md-offset-1 col-md-5">
											{jscalendar id="startPeriod" date=$recurrence.startPeriod fieldname="startPeriod" align="Bc" showtime='n'}
										</div>
									{else}
									<div class="col-md-offset-1">
										{html_select_date prefix="startPeriod_" time=$recurrence.startPeriod field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
									</div>
									{/if}
									<br><br><hr/>
									{tr}End Period{/tr}<br><br>
									<input type="radio" id="id_endTypeNb" name="endType" value="nb" {if $recurrence.nbRecurrences or $calitem.calitemId eq 0} checked="checked" {/if} >
									<label for="id_endTypeNb">
										&nbsp;{tr}End after{/tr}
									</label>
									<div class="col-md-offset-1 col-md-3 input-group">
										<input type="text" name="nbRecurrences" size="3" class="form-control" style="z-index: 0" value="
										{if $recurrence.nbRecurrences gt 0}
											{$recurrence.nbRecurrences}
											{assign var='occurnumber' value="{tr}occurrences{/tr}"}
										{elseif $calitem.calitemId eq 0 or $recurrence.nbRecurrences eq 0}
											1
											{assign var='occurnumber' value="{tr}occurrence{/tr}"}
										{else}
											{assign var='occurnumber' value="{tr}occurrences{/tr}"}
										{/if}
										">
										<span class="input-group-addon">{$occurnumber}</span>
									</div>
									<br>
									<input type="radio" id="id_endTypeDt" name="endType" value="dt" {if $recurrence.endPeriod gt 0} checked="checked" {/if} >
									<label for="id_endTypeDt">
										&nbsp;{tr}End before{/tr}
									</label><br>
									{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
										<div class="col-md-offset-1 col-md-5">
											{jscalendar id="endPeriod" date=$recurrence.endPeriod fieldname="endPeriod" align="Bc" showtime='n'}
										</div>
									{else}
									<div class="col-md-offset-1">
										{html_select_date prefix="endPeriod_" time=$recurrence.endPeriod field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
									</div>
									{/if}
									<br><br><hr>
								{/if}
							{else}
								{if $recurrence.id > 0}
									{if $recurrence.nbRecurrences eq 1}
										{tr}Event occurs once on{/tr}&nbsp;{$recurrence.startPeriod|tiki_long_date}
									{/if}
									{if $recurrence.nbRecurrences gt 1 or $recurrence.endPeriod gt 0}
										{tr}Event is repeated{/tr}&nbsp;
										{if $recurrence.nbRecurrences gt 1}
											{$recurrence.nbRecurrences} {tr}times{/tr},&nbsp;
										{/if}
										{if $recurrence.weekly}
											{tr}on{/tr}&nbsp;{tr}{$daysnames[$recurrence.weekday]}s{/tr},
										{elseif $recurrence.monthly}
											{tr}on{/tr}&nbsp;{$recurrence.dayOfMonth} {tr}of every month{/tr}
										{else}
											{tr}on each{/tr}&nbsp;{$recurrence.dateOfYear_day} {tr}of{/tr} {tr}{$monthnames[$recurrence.dateOfYear_month]}{/tr}
										{/if}
										<br>
										{tr}starting{/tr} {$recurrence.startPeriod|tiki_long_date}
										{if $recurrence.endPeriod gt 0}
											, {tr}ending{/tr}&nbsp;{$recurrence.endPeriod|tiki_long_date}
										{/if}.
									{/if}
								{/if}
							{/if}
						</div>
					</div>
				</div> <!-- / .row -->
			{/if}{* end recurrence *}
			<div class="form-group col-md-12 date">
				<label class="control-label col-md-3">{tr}Start{/tr}</label>
				{if $edit}
					<div class="col-md-4 start">
						{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
							{jscalendar id="start" date=$calitem.start fieldname="save[date_start]" align="Bc" showtime='n'}
						{else}
							{html_select_date prefix="start_date_" time=$calitem.start field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
						{/if}
					</div>
					<div class="col-md-3 start time">
						{html_select_time prefix="start_" display_seconds=false time=$calitem.start minute_interval=$prefs.calendar_timespan use_24_hours=$use_24hr_clock}
					</div>
					<label class="col-md-2">
						<input type="checkbox" name="allday" id="allday" value="true" {if $calitem.allday} checked="checked"{/if}>
						{tr}All day{/tr}
					</label>
				{else}
					<div class="col-md-9">
						{if $calitem.allday}
							<abbr class="dtstart" title="{$calitem.start|tiki_short_date}">
								{$calitem.start|tiki_long_date}
							</abbr>
						{else}
							<abbr class="dtstart" title="{$calitem.start|isodate}">
								{$calitem.start|tiki_long_datetime}
							</abbr>
						{/if}
					</div>
				{/if}
			</div> <!-- / .form-group -->
			<div class="form-group col-md-12 date">
				<label class="control-label col-md-3">{tr}End{/tr}</label>
				{if $edit}
					<input type="hidden" name="save[end_or_duration]" value="end" id="end_or_duration">
					<div class="col-md-4 end ">
							{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
								{jscalendar id="end" date=$calitem.end fieldname="save[date_end]" align="Bc" showtime='n'}
							{else}
								{html_select_date prefix="end_date_" time=$calitem.end field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
							{/if}
					</div>
					<div class="col-md-3 end time">
						{html_select_time prefix="end_" display_seconds=false time=$calitem.end minute_interval=$prefs.calendar_timespan use_24_hours=$use_24hr_clock}
					</div>
					<div class="col-md-3 duration time" style="display:none;">
						{html_select_time prefix="duration_" display_seconds=false time=$calitem.duration|default:'01:00' minute_interval=$prefs.calendar_timespan}
					</div>
					<div class="col-md-2 time">
						<a href="#" id="durationBtn" class="btn btn-xs">
							{tr}Show duration{/tr}
						</a>
					</div>
				{else}
					<div class="col-md-9">
						{if $calitem.allday}
							{if $calitem.end}
								<abbr class="dtend" title="{$calitem.end|tiki_short_date}">
							{/if}
							{$calitem.end|tiki_long_date}
							{if $calitem.end}
								</abbr>
							{/if}
						{else}
							{if $calitem.end}
								<abbr class="dtend" title="{$calitem.end|isodate}">
							{/if}
							{$calitem.end|tiki_long_datetime}
							{if $calitem.end}
								</abbr>
							{/if}
						{/if}
					</div>
				{/if}
				{if $impossibleDates}
					<br>
					<span style="color:#900;">
						{tr}Events cannot end before they start{/tr}
					</span>
				{/if}
			</div> <!-- / .form-group -->
			{jq}
$("#allday").click(function () {
	if ($(this).prop("checked")) {
		$(".time").css("visibility", "hidden");
	} else {
		$(".time").css("visibility", "visible");
	}
});
$("#durationBtn").click(function () {
	if ($(".duration.time:visible").length) {
		$(".duration.time").hide();
		$(".end.time").show();
		$(this).text("{tr}Show duration{/tr}");
		$("#end_or_duration").val("end");
	} else {
		$(".duration.time").show();
		$(".end.time").hide();
		$(this).text("{tr}Show end time{/tr}");
		$("#end_or_duration").val("duration");
	}
	return false;
});

var getEventTimes = function() {
	var out = {};
	out.start = new Date($("#start").val() * 1000);
	out.start.setHours($("select[name=start_Hour]").val());
	out.start.setMinutes($("select[name=start_Minute]").val());
	out.end = new Date($("#end").val() * 1000);
	out.end.setHours($("select[name=end_Hour]").val());
	out.end.setMinutes($("select[name=end_Minute]").val());
	out.duration = new Date(0);
	out.duration.setHours($("select[name=duration_Hour]").val());
	out.duration.setMinutes($("select[name=duration_Minute]").val());

	return out;
};
var fNum = function (num) {
	var str = "0" + num;
	return str.substring(str.length - 2);
};

$(".start.time select, .duration.time select, #start").change(function () {
	var times = getEventTimes();
	times.end = new Date(times.start.getTime() + times.duration.getTime());
	$("select[name=end_Hour]").val(fNum(times.end.getHours())).trigger("chosen:updated");
	$("select[name=end_Minute]").val(fNum(times.end.getMinutes())).trigger("chosen:updated");
	$("#end").next()
		.datepicker("setDate", $.datepicker.formatDate($("#end").next().datepicker("option", "dateFormat"), times.end))
		.datepicker("show").datepicker("hide");
});
$(".end.time select, #end").change(function () {
	var times = getEventTimes(),
		s = times.start.getTime(),
		e = times.end.getTime();
	if (e <= s) {
		$("select[name=start_Hour]").val(fNum(times.end.getHours())).trigger("chosen:updated");
		$("select[name=start_Minute]").val(fNum(times.end.getMinutes())).trigger("chosen:updated");
		$("#start").next()
			.datepicker("setDate", $.datepicker.formatDate($("#start").next().datepicker("option", "dateFormat"), times.end))
			.datepicker("show").datepicker("hide");
		s = e;
	}
	times.duration = new Date(e - s);
	$("select[name=duration_Hour]").val(fNum(times.duration.getHours())).trigger("chosen:updated");
	$("select[name=duration_Minute]").val(fNum(times.duration.getMinutes())).trigger("chosen:updated");
}).change();	// set duration on load
			{/jq}
			{if $edit or !empty($calitem.parsed)}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Description{/tr}</label>
					<div class="col-md-9">
						{if $edit}
							{strip}
								{textarea name="save[description]" id="editwiki" cols=40 rows=10}
									{$calitem.description}
								{/textarea}
							{/strip}
						{else}
							<span{if $prefs.calendar_description_is_html neq 'y'} class="description"{/if}>
								{$calitem.parsed|default:"<i>{tr}No description{/tr}</i>"}
							</span>
						{/if}
					</div>
				</div>
			{/if}
			{if $calendar.customstatus ne 'n'}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Status{/tr}</label>
					<div class="col-md-9">
						<div class="statusbox {if $calitem.status eq 0}status0{/if}">
							{if $edit}
								<input id="status0" type="radio" name="save[status]" value="0"
								{if (!empty($calitem) and $calitem.status eq 0) or (empty($calitem) and $calendar.defaulteventstatus eq 0)}
									checked="checked"
								{/if}
								>
								<label for="status0">
									{tr}Tentative{/tr}
								</label>
							{else}
								{tr}Tentative{/tr}
							{/if}
						</div>
						<div class="statusbox	{if $calitem.status eq 1}status1{/if}">
							{if $edit}
								<input id="status1" type="radio" name="save[status]" value="1" {if $calitem.status eq 1} checked="checked" {/if} >
								<label for="status1">
									{tr}Confirmed{/tr}
								</label>
							{else}
								{tr}Confirmed{/tr}
							{/if}
						</div>
						<div class="statusbox {if $calitem.status eq 2}status2{/if}">
							{if $edit}
								<input id="status2" type="radio" name="save[status]" value="2" {if $calitem.status eq 2} checked="checked" {/if} >
								<label for="status2">
									{tr}Cancelled{/tr}
								</label>
							{else}
								{tr}Cancelled{/tr}
							{/if}
						</div>
					</div>
				</div> <!-- / .form-group -->
			{/if}
			{if $calendar.custompriorities eq 'y'}
				<div class="form-group clearfix">
					<label class="control-label col-md-3">{tr}Priority{/tr}</label>
					<div class="col-md-2">
						{if $edit}
							<select name="save[priority]" style="background-color:#{$listprioritycolors[$calitem.priority]};" onchange="this.style.bacgroundColor='#'+this.selectedIndex.value;" class="form-control">
								{foreach item=it from=$listpriorities}
									<option value="{$it}" style="background-color:#{$listprioritycolors[$it]};" {if $calitem.priority eq $it} selected="selected" {/if} >
										{$it}
									</option>
								{/foreach}
							</select>
						{else}
							<span style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;width:90%;padding:1px 4px">
								{$calitem.priority}
							</span>
						{/if}
					</div>
				</div> <!-- / .form-group -->
			{/if}
			<div class="form-group" style="display:{if $calendar.customcategories eq 'y'}block{else}none{/if};" id="calcat">
				<label class="control-label col-md-3">
					{tr}Classification{/tr}
				</label>
				<div class="col-md-9">
					{if $edit}
						{if count($listcats)}
							<select name="save[categoryId]" class="form-control">
								<option value="">
								</option>
								{foreach item=it from=$listcats}
									<option value="{$it.categoryId}" {if $calitem.categoryId eq $it.categoryId} selected="selected" {/if} >
										{$it.name|escape}
									</option>
								{/foreach}
							</select>
							{tr}or new{/tr}
						{/if}
						<input class="form-control" type="text" name="save[newcat]" value="">
					{else}
						<span class="category">
							{$calitem.categoryName|escape}
						</span>
					{/if}
				</div>
			</div> <!-- / .form-group -->
			<div class="form-group" style="display:{if $calendar.customlocations eq 'y'}block{else}none{/if};" id="calloc">
				<label class="control-label col-md-3">{tr}Location{/tr}</label>
				<div class="col-md-9">
					{if $edit}
						{if count($listlocs)}
							<select name="save[locationId]" class="form-control">
								<option value="">
								</option>
								{foreach item=it from=$listlocs}
									<option value="{$it.locationId}" {if $calitem.locationId eq $it.locationId} selected="selected" {/if} >
										{$it.name|escape}
									</option>
								{/foreach}
							</select>
							{tr}or new{/tr}
						{/if}
						<input class="form-control" type="text" name="save[newloc]" value="">
					{else}
						<span class="location">
							{$calitem.locationName|escape}
						</span>
					{/if}
				</div>
			</div> <!-- / .form-group -->
			{if $calendar.customurl ne 'n'}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}URL{/tr}</label>
					<div class="col-md-9">
						{if $edit}
							<input type="text" name="save[url]" value="{$calitem.url}" size="32" class="form-control">
						{else}
							<a class="url" href="{$calitem.url}">
								{$calitem.url|escape}
							</a>
						{/if}
					</div>
				</div> <!-- / .form-group -->
			{/if}
			<div class="form-group" style="display:{if $calendar.customlanguages eq 'y'}block{else}none{/if};" id="callang">
				<label class="control-label col-md-3">{tr}Language{/tr}</label>
				<div class="col-md-9">
					{if $edit}
						<select name="save[lang]" class="form-control">
							<option value="">
							</option>
							{foreach item=it from=$listlanguages}
								<option value="{$it.value}" {if $calitem.lang eq $it.value} selected="selected" {/if} >
									{$it.name}
								</option>
							{/foreach}
						</select>
					{else}
						{$calitem.lang|langname}
					{/if}
				</div>
			</div> <!-- / .form-group -->
			{if !empty($groupforalert) && $showeachuser eq 'y'}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Choose users to alert{/tr}</label>
					<div class="col-md-9">
						{section name=idx loop=$listusertoalert}
							{if $showeachuser eq 'n'}
								<input type="hidden" name="listtoalert[]" value="{$listusertoalert[idx].user}">
							{else}
								<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
							{/if}
						{/section}
					</div>
				</div> <!-- / .form-group -->
			{/if}
			<div class="form-group" style="display:{if $calendar.customparticipants eq 'y'}block{else}none{/if};" id="calorg">
				<label class="control-label col-md-3">{tr}Organized by{/tr}</label>
				<div class="col-md-9">
					{if isset($calitem.organizers)}
						{if $edit}
							{if $preview or $changeCal}
								<input type="text" name="save[organizers]" value="{$calitem.organizers|escape}" style="width:90%;">
							{else}
								<input type="text" name="save[organizers]" value="
								{foreach item=org from=$calitem.organizers name=organizers}
									{if $org neq ''}
										{$org|escape}
										{if !$smarty.foreach.organizers.last}
											,
										{/if}
									{/if}
								{/foreach}
								" style="width:90%;">
							{/if}
						{else}
							{foreach item=org from=$calitem.organizers}
								{$org|userlink}<br>
							{/foreach}
						{/if}
					{/if}
				</div>
			</div> <!-- / .form-group -->
			<div class="form-group" style="display:{if $calendar.customparticipants eq 'y'}block{else}none{/if};" id="calpart">
				<label class="control-label col-md-3">{tr}Participants{/tr}</label>
				<div class="col-md-9">
					{if isset($calitem.participants)}
						{if $edit}
							{if $preview or $changeCal}
								<input type="text" name="save[participants]" value="{$calitem.participants}">
							{else}
								<input type="text" name="save[participants]" value="
								{foreach item=ppl from=$calitem.participants name=participants}
									{if $ppl.name neq ''}
										{if $ppl.role}{$ppl.role}
											:
										{/if}
										{$ppl.name}
										{if !$smarty.foreach.participants.last}
											,
										{/if}
									{/if}
								{/foreach}
								">
							{/if}
							{else}
								{assign var='in_particip' value='n'}
								{foreach item=ppl from=$calitem.participants}
									{$ppl.name|userlink}
									{if $listroles[$ppl.role]}
										({$listroles[$ppl.role]})
									{/if}
									<br>
									{if $ppl.name eq $user}
										{assign var='in_particip' value='y'}
									{/if}
								{/foreach}
								{if $tiki_p_calendar_add_my_particip eq 'y'}
									{if $in_particip eq 'y'}
										{button _text="{tr}Withdraw me from the list of participants{/tr}" href="?del_me=y&viewcalitemId=$id"}
									{else}
									{button _text="{tr}Add me to the list of participants{/tr}" href="?add_me=y&viewcalitemId=$id"}
								{/if}
							{/if}
							{if $tiki_p_calendar_add_guest_particip eq 'y'}
								{* Nested forms do not work
									<form action="tiki-calendar_edit_item.php" method="post">
										<input type ="hidden" name="viewcalitemId" value="{$id}">
										<input type="text" name="guests">{help desc="{tr}Format:{/tr} {tr}Participant names separated by comma{/tr}" url='calendar'}
										<input type="submit" class="btn btn-default btn-sm" name="add_guest" value="Add guests">
									</form>
								*}
							{/if}
						{/if}
					{/if}
					{if $edit}
						<a href="#" onclick="flip('calparthelp');return false;">
							{icon name='help'}
						</a>
					{/if}
				</div>
			</div> <!-- / .form-group -->
			{if $edit}
				<div style="display: {if $calendar.customparticipants eq 'y' and (isset($cookie.show_calparthelp) and $cookie.show_calparthelp eq 'y')}block{else}none{/if};" id="calparthelp">
					{tr}Roles{/tr}<br>
					0: {tr}chair{/tr} ({tr}default role{/tr})<br>
					1: {tr}required participant{/tr}<br>
					2: {tr}optional participant{/tr}<br>
					3: {tr}non-participant{/tr}<br>
					<br>
					{tr}Input list of participants, separated by commas. Roles must be indicated by a prefix separated by a colon as in:{/tr}&nbsp;
					<code>
						{tr}role:login_or_email,login_or_email{/tr}
					</code>
					<br>
					{tr}If no role is provided, default role will be "Chair participant".{/tr}
				</div>
			{/if}
			{if $edit}
				{if $recurrence.id gt 0}
					<div class="row">
						<div class="col-md-9 col-md-push-3">
							<input type="radio" id="id_affectEvt" name="affect" value="event" checked="checked">
							<label for="id_affectEvt">
								{tr}Update this event only{/tr}
							</label><br>
							<input type="radio" id="id_affectMan" name="affect" value="manually">
							<label for="id_affectMan">
								{tr}Update every unchanged event in this recurrence series{/tr}
							</label><br>
							<input type="radio" id="id_affectAll" name="affect" value="all">
							<label for="id_affectAll">
								{tr}Update every event in this recurrence series{/tr}
							</label>
						</div>
					</div>
				{/if}
				{if !$user and $prefs.feature_antibot eq 'y'}
					{include file='antibot.tpl'}
				{/if}
			{/if}
		</div> <!-- /.wikitext -->
		{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
			{js_insert_icon type="jscalendar"}
		{/if}
	</div> <!-- /.modal-body -->
	{if $edit}
		<div class="modal-footer">
			<div class="row submit">
				<div class="col-md-9 col-md-push-3">
					<input type="submit" class="btn btn-default" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;">
					<input type="submit" class="btn btn-primary" name="act" value="{tr}Save{/tr}" onclick="needToConfirm=false;">
					{if $id}
						<input type="submit" class="btn btn-danger" onclick="needToConfirm=false;document.location='tiki-calendar_edit_item.php?calitemId={$id}&amp;delete=y';return false;" value="{tr}Delete event{/tr}">
					{/if}
					{if $recurrence.id}
						<input type="submit" class="btn btn-danger" onclick="needToConfirm=false;document.location='tiki-calendar_edit_item.php?recurrenceId={$recurrence.id}&amp;delete=y';return false;" value="{tr}Delete recurrent events{/tr}">
					{/if}
					{if $prefs.calendar_fullcalendar eq 'y'}
						{if $prefs.calendar_export_item == 'y'}
							{button href='tiki-calendar_export_ical.php? export=y&calendarItem='|cat:$id _text="{tr}Export Event as iCal{/tr}"}
						{/if}
					{/if}
					<input type="submit" class="btn btn-default" onclick="needToConfirm=false;document.location='{$referer|escape:'html'}';return false;" value="{tr}Cancel{/tr}">
				</div>
			</div>
		</div>
	{/if}
</form>
