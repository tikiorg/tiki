{* $Id$ *}

<h1>{tr}User Watches{/tr}: {tr}Notification Preferences{/tr}</h1>
<div style="float:left; width:60px"><img src="img/icons/large/user.png" alt="{tr}Notification Preferences{/tr}" /></div>
{tr}Use "watches" to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon _id=eye} button on specific pages.{/tr}<br/> {tr}Set up here your preferences related to receiving notifications by email about changes in the site{/tr}.
{if $email_ok eq 'n'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}You need to set your email to receive email notifications.{/tr}
		{icon _id="arrow_right" href="tiki-user_preferences.php"}
	{/remarksbox}
{/if}

<div align="left" style="margin-top:1em;">
<fieldset>
{if $prefs.feature_user_watches eq 'y'}
	<legend>{tr}Notification Preferences{/tr}</legend>
		<table class="formcolor">
				<p>{tr}Send notification when I am the editor{/tr}:</p>
				<form action="tiki-user_notifications.php" method="post">
					<input type="hidden" name="notification_preferences" value="true">
					<p><input type="checkbox" name="user_wiki_watch_editor" {if $user_wiki_watch_editor eq 'y'}checked{/if}> {tr}Wiki{/tr}</p>
					<p><input type="checkbox" name="user_article_watch_editor"  {if $user_article_watch_editor eq 'y'}checked{/if}> {tr}Article{/tr}</p>
					<p><input type="checkbox" name="user_blog_watch_editor" {if $user_blog_watch_editor eq 'y'}checked{/if}> {tr}Blog{/tr}</p>
					<p><input type="checkbox" name="user_tracker_watch_editor" {if $user_tracker_watch_editor eq 'y'}checked{/if}> {tr}Tracker{/tr}</p>
					<p><input type="checkbox" name="user_calendar_watch_editor" {if $user_calendar_watch_editor eq 'y'}checked{/if}> {tr}Calendar{/tr}</p>
					<p><input type="checkbox" name="user_comment_watch_editor" {if $user_comment_watch_editor eq 'y'}checked{/if}> {tr}Comment{/tr}</p>
			
					<p><input type="submit" class="btn btn-default" name="submit" value=" {tr}Apply{/tr} "></p>
				</form>
		</table>
{else}
	{tr}The feature user watches is disabled in this site{/tr}.<br/>
	{tr}You might ask your site admin to enable it{/tr}.
{/if}
</fieldset>

</div>
