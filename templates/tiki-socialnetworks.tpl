{* $Id$ *}

{title help="Social networks"}{tr}Social networks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

{if $tikifeedback}
	<div class="alert alert-warning">
		{section name=n loop=$tikifeedback}<div>{$tikifeedback[n].mes}</div>{/section}
	</div>
{/if}

{tabset name="mytiki_user_preference"}
	{tab name="{tr}Accounts{/tr}"}
		<h2>{tr}Accounts{/tr}</h2>

		<form action="tiki-socialnetworks.php" method="post" class="form-horizontal">
			<h2><img src="img/icons/twitter_t_logo_32.png" alt="Twitter" width="32" height="32"> Twitter</h2>
			{if $twitterRegistered==0}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}To use Twitter integration, the site admin must register this site as an application at <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}
				{/remarksbox}
			{else}
				{if $twitter}
					{button href="tiki-socialnetworks.php?remove_twitter=true" _text="{tr}Remove{/tr}"}
					{tr}Twitter authorisation.{/tr}
				{else}
					{if $show_removal}
						<a href="https://twitter.com/settings/connections" target="_blank">{tr}Click here{/tr}</a> {tr}to manage your authorisations at Twitter{/tr}<br>
					{else}
						{* Can't use button here, we need the reload/redirect to work *}
						<span class="button btn btn-default"><a href="tiki-socialnetworks.php?request_twitter=true">Authorize</a></span>
						{tr}this site with twitter.com to use Twitter integration of this site.{/tr}
					{/if}
				{/if}
			{/if}
			<h2><img src="img/icons/facebook-logo_32.png" alt="Facebook" width="32" height="32"> Facebook</h2>
			{if $facebookRegistered==0}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}To use Facebook integration, the site admin must register this site as an application at <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a> first.{/tr}
				{/remarksbox}
			{else}
				{if $facebook}
					{button href="tiki-socialnetworks.php?remove_facebook=true" _text="{tr}Remove{/tr}"}
					{tr}Facebook authorisation.{/tr}
				{else}
					{if $show_removal}
						<a href="http://facebook.com/editapps.php" target="_blank">{tr}Click here{/tr}</a> {tr}to manage your authorizations at Facebook{/tr}<br>
					{else}
						{* Can't use button here, we need the reload/redirect to work *}
						<span class="button btn btn-default"><a href="tiki-socialnetworks.php?request_facebook=true">Authorize</a></span>
						{tr}this site within facebook.com to use Facebook integration with this site.{/tr}
					{/if}
				{/if}
			{/if}
			<h2>LinkedIn</h2>
			{if $linkedInRegistered==0}
				{remarksbox type="note" title="{tr}Note{/tr}"}
				{tr}To use LinkedIn integration, the site admin must register this site as an application at <a href="https://www.linkedin.com/secure/developer" target="_blank">https://www.linkedin.com/secure/developer</a> first.{/tr}
				{/remarksbox}
			{else}
				{if $linkedIn}
					{button href="tiki-socialnetworks_linkedin.php?remove=true" _text="{tr}Remove{/tr}"}
					{tr}LinkedIn authorisation.{/tr}
				{else}
					<a class="button btn btn-default" href="tiki-socialnetworks_linkedin.php?link=true">Authorize</a>
					{tr}this site to link your user to your LinkedIn account.{/tr}
				{/if}
			{/if}
			<h2>bit.ly</h2>
			{if $prefs.socialnetworks_bitly_sitewide=='y'}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					<p>{tr}The site admin has set up a global account which will be used for this site{/tr}.</p>
				{/remarksbox}
			{else}
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}bit.ly Login{/tr}</label>
					<div class="col-sm-7">
						<input type="text" name="bitly_login" value="{$bitly_login}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}bit.ly Key{/tr}</label>
					<div class="col-sm-7">
						<input type="text" name="bitly_key" value="{$bitly_key}" class="form-control">
					</div>
				</div>
			{/if}
			<div class="form-group">
				<div class="col-sm-12">
					<input type="submit" class="btn btn-primary btn-sm" name="accounts" value="{tr}Save changes{/tr}">
				</div>
			</div>
		</form>
	{/tab}
{/tabset}
