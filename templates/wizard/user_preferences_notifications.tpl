{* $Id$ *}

<h1 class="pagetitle">{tr}User Watches{/tr}: {tr}Notification Preferences{/tr}</h1>
<div class="userWizardIconleft"><img src="img/icons/large/user.png" alt="{tr}Notification Preferences{/tr}" /></div>
{tr}Use 'watches' to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon _id=eye} button on specific pages.{/tr}<br/> 
{tr}Set up below preferences related to receiving notifications by email about changes in the site{/tr}.<br/><br/><br/>

{if $email_ok eq 'n'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}You need to set your email to receive email notifications.{/tr}
		{icon _id="arrow_right" href="tiki-user_preferences.php"}
	{/remarksbox}
{/if}

<div class="adminWizardContent">
<fieldset>
{if $prefs.feature_user_watches eq 'y'}
	<legend>{tr}Notification Preferences{/tr}</legend>
		<table class="formcolor">
				<p>{tr}Send notification when I am the editor{/tr}:</p>
					<p><input type="checkbox" name="user_wiki_watch_editor" {if $user_wiki_watch_editor eq 'y'}checked{/if}> {tr}Wiki{/tr}</p>
					<p><input type="checkbox" name="user_article_watch_editor"  {if $user_article_watch_editor eq 'y'}checked{/if}> {tr}Article{/tr}</p>
					<p><input type="checkbox" name="user_blog_watch_editor" {if $user_blog_watch_editor eq 'y'}checked{/if}> {tr}Blog{/tr}</p>
					<p><input type="checkbox" name="user_tracker_watch_editor" {if $user_tracker_watch_editor eq 'y'}checked{/if}> {tr}Tracker{/tr}</p>
					<p><input type="checkbox" name="user_calendar_watch_editor" {if $user_calendar_watch_editor eq 'y'}checked{/if}> {tr}Calendar{/tr}</p>
					<p><input type="checkbox" name="user_comment_watch_editor" {if $user_comment_watch_editor eq 'y'}checked{/if}> {tr}Comment{/tr}</p>
			
		</table>
{else}
	{tr}The feature user watches is disabled in this site{/tr}.<br/>
	{tr}You might ask your site admin to enable it{/tr}.
{/if}
</fieldset>

</div>
