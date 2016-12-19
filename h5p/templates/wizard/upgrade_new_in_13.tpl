{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new features and settings in Tiki 13{/tr}.
	<a href="http://doc.tiki.org/Tiki13" target="tikihelp" class="tikihelp" title="{tr}Tiki13:{/tr}
			{tr}Tiki13 is a post-LTS version{/tr}.
			{tr}It will be supported until 14.1 is released{/tr}.
			{tr}The requirements increased (IE9, PHP 5.5){/tr}.
			{tr}Major changes have happened, including moving to Bootstrap{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Themes{/tr} & {tr}Site layouts (based on 'Bootstrap'){/tr}</legend>
			<em>{tr}The changes in this area were refactored in Tiki 14{/tr}.</em>
			<em>{tr}See{/tr} <a href="tiki-admin.php?page=look&amp;alt=Look+%26+Feel" target="_blank">{tr}Look & Feel admin panel{/tr}</a></em>

		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Goal, Recognition and Rewards{/tr}</legend>
			{preference name=goal_enabled}
			<div class="adminoptionboxchild" id="goal_enabled_childcontainer">
				{preference name=goal_badge_tracker}
				{preference name=goal_group_blacklist}
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Notifications{/tr}</legend>
			{preference name=monitor_enabled}
			<div class="adminoptionboxchild" id="monitor_enabled_childcontainer">
				{preference name=monitor_individual_clear}
				{preference name=monitor_count_refresh_interval}
				{preference name=monitor_reply_email_pattern}
				{preference name=monitor_digest}
				<div class="alert alert-warning">
					<p>{tr}For the digest emails to be sent out, you will need to set-up a cron job.{/tr}</p>
					<p>{tr}Adjust the command parameters for your digest frequency. Default frequency is 7 days.{/tr}</p>
					<strong>{tr}Sample command:{/tr}</strong>
					<code>/usr/bin/php {$monitor_command|escape}</code>
				</div>
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Jitsi{/tr}</legend>
			<div class="form-row">
				<label for="jitsi-url">{tr}Provision URL{/tr}</label>
				<input id="jitsi-url" readonly type="text" value="{$jitsi_url|escape}" class="form-control">
			</div>
			{preference name=suite_jitsi_provision}
			{preference name=suite_jitsi_configuration}
		</fieldset>
	</div>
</div>
