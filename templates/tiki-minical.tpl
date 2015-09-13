{title help="User+Calendar" url="tiki-minical.php?view=$view"}{tr}Mini Calendar{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-minical.php#add" class="btn btn-default" _text="{tr}Add{/tr} "}
	{button href="tiki-minical_prefs.php" class="btn btn-default" _text="{tr}Prefs{/tr}"}
	{button href="tiki-minical.php?view=daily" class="btn btn-default" _text="{tr}Daily{/tr}"}
	{button href="tiki-minical.php?view=weekly" class="btn btn-default" _text="{tr}Weekly{/tr}"}
	{button href="tiki-minical.php?view=list" class="btn btn-default" _text="{tr}List{/tr}"}
	{button href="tiki-minical_export.php" class="btn btn-default" _text="{tr}Export{/tr}"}
	{button href="tiki-minical_prefs.php#import" class="btn btn-default" _text="{tr}Import{/tr}"}
</div>

<div class="table-responsive">
	<table class="table" >
		<tr>
			<td>
				<b>{tr}Upcoming Events{/tr}</b>
				<br>
				<table class="table">
					{section name=ix loop=$upcoming}
						<tr>
							<td class="even">
								{$upcoming[ix].start|tiki_short_datetime}
								{if $upcoming[ix].topicId}
									{if $upcoming[ix].topic.isIcon eq 'y'}
										<img title="{$upcoming[ix].topic.name}" src="{$upcoming[ix].topic.path}" alt="{tr}topic image{/tr}">
									{else}
										<img title="{$upcoming[ix].topic.name}" src="tiki-view_minical_topic.php?topicId={$upcoming[ix].topicId}" alt="{tr}topic image{/tr}">
									{/if}
								{/if}
								<a title="{$upcoming[ix].start|tiki_short_time}-{$upcoming[ix].end|tiki_short_time}:{$upcoming[ix].description}" class="link" href="tiki-minical.php?view={$view}&amp;eventId={$upcoming[ix].eventId}#add">{$upcoming[ix].title}</a>
							</td>
						</tr>
					{sectionelse}
						{norecords}
					{/section}
				</table>
			</td>
			<td width="180">
				{include file="modules/mod-calendar.tpl.nocache"}
			</td>
		</tr>
	</table>
</div>
<br>


{if $view eq 'daily'}
	<b>
		<a class="link" href="tiki-minical.php?view={$view}&amp;day={$yesterday|tiki_date_format:"%d"}&amp;mon={$yesterday|tiki_date_format:"%m"}&amp;year={$yesterday|tiki_date_format:"%Y"}">{icon name='backward' style="vertical-align:middle"}</a>
		{$pdate|tiki_long_date}
		<a class="link" href="tiki-minical.php?view={$view}&amp;day={$tomorrow|tiki_date_format:"%d"}&amp;mon={$tomorrow|tiki_date_format:"%m"}&amp;year={$tomorrow|tiki_date_format:"%Y"}">{icon name='forward' style="vertical-align:middle"}</a>
	</b>

	<table class="table">
		{section name=ix loop=$slots}
			<tr>
				<td>
					<table>
						<tr>
							<td>
								{$slots[ix].start|tiki_short_time}<!--<br>{$slots[ix].end|tiki_short_time}-->
							</td>
							<td>
								{section name=jj loop=$slots[ix].events}
									{if $slots[ix].events[jj].topicId}
										{if $slots[ix].events[jj].topic.isIcon eq 'y'}
											<img title="{$slots[ix].events[jj].topic.name}" src="{$slots[ix].events[jj].topic.path}" alt="{tr}topic image{/tr}">
										{else}
										<img title="{$slots[ix].events[jj].topic.name}" src="tiki-view_minical_topic.php?topicId={$slots[ix].events[jj].topicId}" alt="{tr}topic image{/tr}">
										{/if}
									{/if}
									<a title="{$slots[ix].events[jj].start|tiki_short_time}-{$slots[ix].events[jj].end|tiki_short_time}:{$slots[ix].events[jj].description}" class="link" href="tiki-minical.php?view={$view}&amp;eventId={$slots[ix].events[jj].eventId}#add">{$slots[ix].events[jj].title|escape}</a>
									<a class="link" href="tiki-minical.php?view={$view}&amp;remove={$slots[ix].events[jj].eventId}">{icon name='remove' alt="{tr}Remove{/tr}" style="vertical-align:middle;"}</a>
									<br>
								{/section}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		{/section}
	</table>
{/if}

{if $view eq 'weekly'}
	<a class="link" href="tiki-minical.php?view={$view}&amp;day={$prev_week_start|tiki_date_format:"%d"}&amp;mon={$prev_week_start|tiki_date_format:"%m"}&amp;year={$prev_week_start|tiki_date_format:"%Y"}">{icon name='backward'}</a>
	<b>
		{$week_start|tiki_date_format:"%b"} {$week_start|tiki_date_format:"%d"}-{$week_end|tiki_date_format:"%b"} {$week_end|tiki_date_format:"%d"}
	</b>
	<a class="link" href="tiki-minical.php?view={$view}&amp;day={$next_week_start|tiki_date_format:"%d"}&amp;mon={$next_week_start|tiki_date_format:"%m"}&amp;year={$next_week_start|tiki_date_format:"%Y"}">{icon name='forward'}</a>
	<table class="table">
		{section name=ix loop=$slots}
			<tr>
				<td>
					<table >
						<tr>
							<td >
								<a class="link" href="tiki-minical.php?view=daily&amp;day={$slots[ix].start|tiki_date_format:"%d"}&amp;mon={$slots[ix].start|tiki_date_format:"%m"}&amp;year={$slots[ix].start|tiki_date_format:"%Y"}">{$slots[ix].start|tiki_date_format:"%a"}<br>{$slots[ix].start|tiki_date_format:"%d"}</a>
							</td>
							<td>
								{section name=jj loop=$slots[ix].events}
									{$slots[ix].events[jj].start|tiki_short_time}:
									{if $slots[ix].events[jj].topicId}
										{if $slots[ix].events[jj].topic.isIcon eq 'y'}
											<img title="{$slots[ix].events[jj].topic.name}" src="{$slots[ix].events[jj].topic.path}" alt="{tr}topic image{/tr}">
										{else}
											<img title="{$slots[ix].events[jj].topic.name}" src="tiki-view_minical_topic.php?topicId={$slots[ix].events[jj].topicId}" alt="{tr}topic image{/tr}">
										{/if}
									{/if}

									<a title="{$slots[ix].events[jj].start|tiki_short_time}:{$slots[ix].events[jj].description}" class="link" href="tiki-minical.php?view={$view}&amp;eventId={$slots[ix].events[jj].eventId}#add">{$slots[ix].events[jj].title}</a>
									[<a class="link" href="tiki-minical.php?view={$view}&amp;remove={$slots[ix].events[jj].eventId}">x</a>]
									<br>
								{/section}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		{/section}
	</table>
{/if}

{if $view eq 'list' and (count($channels) > 0 or $find ne '')}
	{include file='find.tpl'}

	<a class="link" href="tiki-minical.php?view={$view}&amp;removeold=1">{tr}Remove old events{/tr}</a>
	<form action="tiki-minical.php" method="post">
		<input type="hidden" name="view" value="{$view|escape}">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th><input type="submit" class="btn btn-default btn-sm" name="delete" value="x "></th>
					<th>
						<a href="tiki-minical.php?view={$view}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a>
					</th>
					<th>
						<a href="tiki-minical.php?view={$view}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'start_desc'}start{else}start_desc{/if}">{tr}Start{/tr}</a>
					</th>
					<th>
						<a href="tiki-minical.php?view={$view}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'duration_desc'}duration_asc{else}duration_desc{/if}">{tr}duration{/tr}</a>
					</th>
					<th style="text-align:center;">
						<a href="tiki-minical.php?view={$view}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicId_desc'}topicId_asc{else}topicId_desc{/if}">{tr}Topic{/tr}</a>
					</th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td style="text-align:center;">
							<input type="checkbox" name="event[{$channels[user].eventId}]">
						</td>
						<td>
							<a class="link" href="tiki-minical.php?view={$view}&amp;eventId={$channels[user].eventId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}#add">{$channels[user].title}</a>
						</td>
						<td>{$channels[user].start|tiki_short_datetime}</td>
						<td>
							{math equation="x / 3600" x=$channels[user].duration format="%d"} {tr}h{/tr} {math equation="(x % 3600) / 60" x=$channels[user].duration} {tr}mins{/tr}
						</td>
						<td style="text-align:center;">
							{if $channels[user].topicId}
								{if $channels[user].topic.isIcon eq 'y'}
									<img title="{$channels[user].topic.name}" src="{$channels[user].topic.path}" alt="{tr}topic image{/tr}">
								{else}
									<img title="{$channels[user].topic.name}" src="tiki-view_minical_topic.php?topicId={$channels[user].topicId}" alt="{tr}topic image{/tr}">
								{/if}
							{/if}
						</td>
					</tr>
				{/section}
			</table>
		</div>
	</form>

	{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/if}

<a id="add"></a>
<h2>{if $eventId}{tr}Edit Event{/tr}{else}{tr}Add Event{/tr}{/if}</h2>
<br>
<form action="tiki-minical.php" method="post" class="form-horizontal">
	<input type="hidden" name="eventId" value="{$eventId|escape}">
	<input type="hidden" name="view" value="{$view|escape}">
	<input type="hidden" name="duration" value="60*60">

    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Title{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="title" value="{$info.title|escape}" style="width:95%" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Start{/tr}</label>
		<div class="col-sm-7">
	      	{html_select_date time=$ev_pdate end_year="+4" field_order=$prefs.display_field_order} {tr}at{/tr}
			{html_select_time minute_interval=5 time=$ev_pdate_h display_seconds=false use_24_hours=$use_24hr_clock}
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Duration{/tr}</label>
		<div class="col-sm-2">
	      	<select name="duration_hours" class="form-control">
				{html_options output=$hours values=$hours selected=$duration_hours}
			</select>
			<div class="help-block">
				{if $duration_hours>1}{tr}hours{/tr}{else}{tr}hour{/tr}{/if}
			</div>
	    </div>
	    <div class="col-sm-2">
	      	<select name="duration_minutes" class="form-control">
				{html_options output=$minutes values=$minutes selected=$duration_minutes}
			</select>
			<div class="help-block">
				{tr}minutes{/tr}
			</div>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Topic{/tr}</label>
		<div class="col-sm-7">
	      	<select name="topicId" class="form-control">
				<option value="0" {if $info.topicId eq $topics[ix].topicId}selected="selected"{/if}>{tr}None{/tr}</option>
				{section name=ix loop=$topics}
					<option value="{$topics[ix].topicId|escape}" {if $info.topicId eq $topics[ix].topicId}selected="selected"{/if}>{$topics[ix].name}</option>
				{/section}
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Description{/tr}</label>
		<div class="col-sm-7">
	      	<textarea name="description" rows="5" cols="80" style="width:95%" class="form-control">{$info.description|escape}</textarea>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			{if $eventId}
				<input type="submit" class="btn btn-warning btn-sm" name="remove2" value="{tr}Delete{/tr}">
			{/if}
	    </div>
    </div>
</form>
