{* $Id$ *}

{title help="User Watches"}{tr}User Watches and preferences{/tr}{/title}
{include file='tiki-mytiki_bar.tpl'}

{if $email_ok eq 'n'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
	{tr}You need to set your email to receive email notifications.{/tr}
		<a href="tiki-user_preferences.php" class="tips" title=":{tr}User preferences{/tr}">{icon name="next"}</a>
	{/remarksbox}
{/if}

{tabset name="user_watches"}

{if $prefs.feature_daily_report_watches eq 'y'}
	{tab name="{tr}Report Preferences{/tr}"}
		<h2>{tr}Report Preferences{/tr}</h2>
	{if isset($remove_user_watch_error) && $remove_user_watch_error}
		{remarksbox type="error" title="{tr}Error{/tr}"}{tr}You are not allowed to remove this notification !{/tr}{/remarksbox}
	{else}
		{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use reports to summarise notifications about objects you are watching.{/tr}{/remarksbox}
	{/if}
		<form action="tiki-user_reports.php" method="post">
			<input type="hidden" name="report_preferences" value="true">

			<p><input type="checkbox" name="use_daily_reports" value="true"
					  {if $report_preferences != false}checked{/if}> {tr}Use reports{/tr}</p>

			<p>
				{tr}Interval in which you want to get the reports{/tr}
				<select name="interval">
					<option value="minute"
							{if $report_preferences.interval eq "minute"}selected{/if}>{tr}Every minute{/tr}</option>
					<option value="hourly"
							{if $report_preferences.interval eq "hourly"}selected{/if}>{tr}Hourly{/tr}</option>
					<option value="daily"
							{if $report_preferences.interval eq "daily" or !isset($report_preferences.interval)}selected{/if}>{tr}Daily{/tr}</option>
					<option value="weekly"
							{if $report_preferences.interval eq "weekly"}selected{/if}>{tr}Weekly{/tr}</option>
					<option value="monthly"
							{if $report_preferences.interval eq "monthly"}selected{/if}>{tr}Monthly{/tr}</option>
				</select>
			</p>

			<div style="float:left; margin-right: 50px;">
				<input type="radio" name="view"
					   value="short"{if $report_preferences.view eq "short"} checked="checked"{/if}> {tr}Short report{/tr}
				<br>
				<input type="radio" name="view"
					   value="detailed"{if $report_preferences.view eq "detailed" OR $report_preferences eq false} checked="checked"{/if} /> {tr}Detailed report{/tr}
				<br>
			</div>
			<div style="float:left; margin-right: 50px;">
				<input type="radio" name="type"
					   value="html"{if $report_preferences.type eq "html" OR $report_preferences eq false} checked="checked"{/if}> {tr}HTML-Email{/tr}
				<br>
				<input type="radio" name="type"
					   value="plain"{if $report_preferences.type eq "plain"} checked="checked"{/if}> {tr}Plain text{/tr}
				<br>
			</div>
			<div>
				<input type="checkbox" name="always_email"
					   value="1"{if $report_preferences.always_email eq 1 OR $report_preferences eq false} checked="checked"{/if}> {tr}Send me an email also if nothing happened{/tr}
			</div>

			<p><input type="submit" name="submit" class="btn btn-primary" title="{tr}Apply Changes{/tr}"
					  value="{tr}Apply{/tr}"></p>
		</form>
	{/tab}
{/if}

{tab name="{tr}My watches{/tr}"}
	<h2>{tr}My watches{/tr}</h2>
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use "watches" to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon name='watch'} button on specific pages.{/tr}{/remarksbox}

{if $add_options|@count > 0}
	<h3>{tr}Add Watch{/tr}</h3>
	<form class="form-horizontal" action="tiki-user_watches.php" method="post">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="type_selector">{tr}Event:{/tr}</label>

			<div class="col-sm-9">
				<select name="event" id="type_selector" class="form-control">
					<option>{tr}Select event type{/tr}</option>
					{foreach key=event item=type from=$add_options}
						<option value="{$event|escape}">{$type.label|escape}</option>
					{/foreach}
				</select>
			</div>
		</div>
		{if $prefs.feature_categories eq 'y'}
			<div class="form-group" id="categ_list">
				<label class="control-label" for="langwatch_categ">{tr}Category{/tr}</label>

				<div class="col-sm-9">
					<select class="categwatch-select form-control" name="categwatch" id="langwatch_categ">
						{foreach item=c from=$categories}
							<option value="{$c.categId|escape}">{$c.name|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		{if $prefs.feature_multilingual eq 'y'}
			<div class="form-group" id="lang_list">
				<label>{tr}Language{/tr}</label>

				<div class="col-sm-9">
					<select name="langwatch" class="form-control">
						{foreach item=l from=$languages}
							<option value="{$l.value|escape}">{$l.name|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		<div class="form-group text-center">
			<input type="submit" class="btn btn-primary btn-sm" name="add" value="{tr}Add{/tr}">
		</div>
	</form>
	{jq}
		$('#type_selector').change( function() {
		var type = $(this).val();

		$('#lang_list').hide();
		$('#categ_list').hide();

		if( type == 'wiki_page_in_lang_created' ) {
		$('#lang_list').show();
		}

		if( type == 'category_changed_in_lang' ) {
		$('#lang_list').show();
		$('#categ_list').show();
		}
		} ).trigger('change');
	{/jq}
{/if}
	<h3>{tr}Watches{/tr}</h3>
	<form class="form-horizontal margin-bottom-md" action="tiki-user_watches.php" method="post" id='formi'>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="event">{tr}Show:{/tr}</label>

			<div class="col-sm-9">
				<select class="form-control" name="event"
						onchange="javascript:document.getElementById('formi').submit();">
					<option value=""{if $smarty.request.event eq ''} selected="selected"{/if}>{tr}All watched events{/tr}</option>
					{foreach from=$events key=name item=description}
						<option value="{$name|escape}"{if $name eq $smarty.request.event} selected="selected"{/if}>
							{if $name eq 'blog_post'}
								{tr}A user submits a blog post{/tr}
							{elseif $name eq 'forum_post_thread'}
								{tr}A user posts a forum thread{/tr}
							{elseif $name eq 'forum_post_topic'}
								{tr}A user posts a forum topic{/tr}
							{elseif $name eq 'wiki_page_changed'}
								{if $prefs.wiki_watch_comments eq 'y'}
									{tr}A user edited or commented on a wiki page{/tr}
								{else}
									{tr}A user edited a wiki page{/tr}
								{/if}
							{else}
								{$description}
							{/if}
						</option>
					{/foreach}
				</select>
			</div>
		</div>
	</form>
	<form action="tiki-user_watches.php" method="post">
		<div class="table-responsive">
			<table class="table">
				<tr>
					{if $watches}
						<th style="text-align:center;"></th>
					{/if}
					<th>{tr}Event{/tr}</th>
					<th>{tr}Object{/tr}</th>
				</tr>

				{foreach item=w from=$watches}
					<tr>
						{if $watches}
							<td class="checkbox-cell">
								<input type="checkbox" name="watch[{$w.watchId}]">
							</td>
						{/if}
						<td class="text">
							{if $w.event eq 'blog_post'}
								{tr}A user submits a blog post{/tr}
							{elseif $w.event eq 'forum_post_thread'}
								{tr}A user posts a forum thread{/tr}
							{elseif $w.event eq 'forum_post_topic'}
								{tr}A user posts a forum topic{/tr}
							{elseif $w.event eq 'wiki_page_changed'}
								{if $prefs.wiki_watch_comments eq 'y'}
									{tr}A user edited or commented on a wiki page{/tr}
								{else}
									{tr}A user edited a wiki page{/tr}
								{/if}
							{elseif isset($w.label)}
								{$w.label}
							{/if}
							({$w.event})
						</td>
						<td class="text"><a class="link" href="{$w.url}">{tr}{$w.type}:{/tr} {$w.title|escape}</a></td>
					</tr>
					{foreachelse}
					{norecords _colspan=2}
				{/foreach}
			</table>
		</div>
		{if $watches}
			<div class="form-group text-center">
				{tr}Perform action with checked:{/tr} <input type="submit" class="btn btn-warning btn-sm" name="delete"
															 value="{tr}Delete{/tr}">
			</div>
		{/if}
	</form>
{/tab}

{tab name="{tr}Notification Preferences{/tr}"}
	<h2>{tr}Notification Preferences{/tr}</h2>
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use this form to control notifications about objects you are watching.{/tr}{/remarksbox}
	<form action="tiki-user_notifications.php" method="post">
		<input type="hidden" name="notification_preferences" value="true">

		<p>{tr}Send notification when I am the editor:{/tr}</p>
		{if $prefs.feature_wiki eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="user_wiki_watch_editor" value="true"
						   {if $user_wiki_watch_editor eq 'y'}checked{/if}> {tr}Wiki{/tr}
				</label>
			</div>
		{/if}
		{if $prefs.feature_articles eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="user_article_watch_editor" value="true"
						   {if $user_article_watch_editor eq 'y'}checked{/if}> {tr}Article{/tr}
				</label>
			</div>
		{/if}
		{if $prefs.feature_blogs eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="user_blog_watch_editor" value="true"
						   {if $user_blog_watch_editor eq 'y'}checked{/if}> {tr}Blog{/tr}</label>
			</div>
		{/if}
		{if $prefs.feature_trackers eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="user_tracker_watch_editor" value="true"
						   {if $user_tracker_watch_editor eq 'y'}checked{/if}> {tr}Tracker{/tr}</label>
			</div>
		{/if}
		{if $prefs.feature_calendar eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="user_calendar_watch_editor" value="true"
						   {if $user_calendar_watch_editor eq 'y'}checked{/if}> {tr}Calendar{/tr}</label>
			</div>
		{/if}
		<div class="checkbox">
			<label>
				<input type="checkbox" name="user_comment_watch_editor" value="true"
					   {if $user_comment_watch_editor eq 'y'}checked{/if}> {tr}Comment{/tr}</label>
		</div>

		<div class="checkbox">
			<label>
				<input type="submit" class="btn btn-primary btn-sm" name="submit" title="{tr}Apply Changes{/tr}"
					   value="{tr}Apply{/tr}"></label>
		</div>
	</form>
{/tab}

{/tabset}
