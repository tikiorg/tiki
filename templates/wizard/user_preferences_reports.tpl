{* $Id$ *}

<div class="userWizardIconleft"><img src="img/icons/large/wizard_user48x48.png" alt="{tr}User Wizard{/tr}" /></div>
{tr}Use "watches" to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon name='watch'} button on specific pages.{/tr}<br/>
{tr}Use reports to summarise notifications about objects you are watching{/tr}.<br/><br/>

<div class="userWizardContent">
	<fieldset>
		{if $prefs.feature_daily_report_watches eq 'y'}
			<legend>{tr}Report Preferences{/tr}</legend>
			<div class="userWizardIconright"><img src="img/icons/large/stock_mail48x48.png" alt="{tr}Report Preferences{/tr}" /></div>
			<table class="formcolor">

				{if isset($remove_user_watch_error) && $remove_user_watch_error}
					{remarksbox type="error" title="{tr}Error{/tr}"}{tr}You are not allowed to remove this notification !{/tr}{/remarksbox}
				{/if}

				<p><input type="checkbox" name="use_daily_reports" value="true" {if $report_preferences != false}checked{/if}> {tr}Use reports{/tr}</p>
				<p>
					{tr}Interval in which you want to get the reports{/tr}
					<select name="interval">
						<option value="minute" {if $report_preferences.interval eq "minute"}selected{/if}>{tr}Every minute{/tr}</option>
						<option value="hourly" {if $report_preferences.interval eq "hourly"}selected{/if}>{tr}Hourly{/tr}</option>
						<option value="daily" {if $report_preferences.interval eq "daily" or !isset($report_preferences.interval)}selected{/if}>{tr}Daily{/tr}</option>
						<option value="weekly" {if $report_preferences.interval eq "weekly"}selected{/if}>{tr}Weekly{/tr}</option>
						<option value="monthly" {if $report_preferences.interval eq "monthly"}selected{/if}>{tr}Monthly{/tr}</option>
					</select>
				</p>

				<div style="float:left; margin-right: 50px;">
					<input type="radio" name="view" value="short"{if $report_preferences.view eq "short"} checked="checked"{/if}> {tr}Short report{/tr}<br>
					<input type="radio" name="view" value="detailed"{if $report_preferences.view eq "detailed" OR $report_preferences eq false} checked="checked"{/if} /> {tr}Detailed report{/tr}<br>
				</div>
				<div style="float:left; margin-right: 50px;">
					<input type="radio" name="type" value="html"{if $report_preferences.type eq "html" OR $report_preferences eq false} checked="checked"{/if}> {tr}HTML-Email{/tr}<br>
					<input type="radio" name="type" value="plain"{if $report_preferences.type eq "plain"} checked="checked"{/if}> {tr}Plain text{/tr}<br>
				</div>
				<div>
					<input type="checkbox" name="always_email" value="1"{if $report_preferences.always_email eq 1 OR $report_preferences eq false} checked="checked"{/if}> {tr}Send me an email also if nothing happened{/tr}
				</div>

			</table>
		{else}
			{tr}The feature daily reports of user watches is disabled in this site{/tr}.<br/>
			{tr}You might ask your site admin to enable it{/tr}.
		{/if}
	</fieldset>

</div>
